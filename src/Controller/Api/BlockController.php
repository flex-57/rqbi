<?php

namespace App\Controller\Api;

use App\Entity\Block;
use App\Enum\BlockType;
use App\Repository\BlockRepository;
use App\Repository\PageRepository;
use App\Service\BlockManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api', name: 'api_block_')]
#[IsGranted('ROLE_ADMIN')]
class BlockController extends AbstractController
{
    public function __construct(
        private readonly BlockManager $blockManager,
        private readonly BlockRepository $blockRepository,
        private readonly PageRepository $pageRepository,
        private readonly SerializerInterface $serializer,
    ) {}

    #[Route('/blocks/types', name: 'types', methods: ['GET'])]
    public function types(): JsonResponse
    {
        $types = array_map(fn(BlockType $t) => [
            'value' => $t->value,
            'label' => $t->getLabel(),
        ], BlockType::cases());

        return $this->json($types);
    }

    #[Route('/pages/{pageId}/blocks', name: 'create', methods: ['POST'])]
    public function create(int $pageId, Request $request): JsonResponse
    {
        $page = $this->pageRepository->find($pageId);
        if (!$page) {
            return $this->json(['error' => 'Page introuvable'], Response::HTTP_NOT_FOUND);
        }

        $payload = json_decode($request->getContent(), true);

        if (empty($payload['type'])) {
            return $this->json(['error' => 'Le type de bloc est obligatoire'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $type = BlockType::from($payload['type']);
        } catch (\ValueError) {
            return $this->json(['error' => 'Type de bloc invalide'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $block = $this->blockManager->createBlock($page, $type, $payload['content'] ?? []);

        $data = $this->serializer->serialize($block, 'json', ['groups' => ['block:read']]);
        return JsonResponse::fromJsonString($data, Response::HTTP_CREATED);
    }

    #[Route('/blocks/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $block = $this->blockRepository->find($id);
        if (!$block) {
            return $this->json(['error' => 'Bloc introuvable'], Response::HTTP_NOT_FOUND);
        }

        $payload = json_decode($request->getContent(), true);
        $block = $this->blockManager->updateBlock($block, $payload['content'] ?? []);

        $data = $this->serializer->serialize($block, 'json', ['groups' => ['block:read']]);
        return JsonResponse::fromJsonString($data);
    }

    #[Route('/blocks/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $block = $this->blockRepository->find($id);
        if (!$block) {
            return $this->json(['error' => 'Bloc introuvable'], Response::HTTP_NOT_FOUND);
        }

        $this->blockManager->deleteBlock($block);
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/blocks/{id}/toggle', name: 'toggle', methods: ['PATCH'])]
    public function toggle(int $id): JsonResponse
    {
        $block = $this->blockRepository->find($id);
        if (!$block) {
            return $this->json(['error' => 'Bloc introuvable'], Response::HTTP_NOT_FOUND);
        }

        $block = $this->blockManager->toggleVisibility($block);
        $data = $this->serializer->serialize($block, 'json', ['groups' => ['block:read']]);
        return JsonResponse::fromJsonString($data);
    }

    private function resizeImage(string $path, int $maxWidth): void
    {
        $info = @getimagesize($path);
        if (!$info || $info[0] <= $maxWidth) {
            return;
        }

        [$width, $height] = $info;
        $newWidth  = $maxWidth;
        $newHeight = (int) round($height * ($maxWidth / $width));

        $src = match ($info['mime']) {
            'image/jpeg' => imagecreatefromjpeg($path),
            'image/png'  => imagecreatefrompng($path),
            'image/webp' => imagecreatefromwebp($path),
            default      => null,
        };

        if (!$src) {
            return;
        }

        $dst = imagecreatetruecolor($newWidth, $newHeight);

        if ($info['mime'] === 'image/png') {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            imagefilledrectangle($dst, 0, 0, $newWidth, $newHeight,
                imagecolorallocatealpha($dst, 255, 255, 255, 127));
        }

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        match ($info['mime']) {
            'image/jpeg' => imagejpeg($dst, $path, 85),
            'image/png'  => imagepng($dst, $path, 6),
            'image/webp' => imagewebp($dst, $path, 85),
            default      => null,
        };

        imagedestroy($src);
        imagedestroy($dst);
    }

    #[Route('/pages/{pageId}/blocks/reorder', name: 'reorder', methods: ['PUT'])]
    public function reorder(int $pageId, Request $request): JsonResponse
    {
        $page = $this->pageRepository->find($pageId);
        if (!$page) {
            return $this->json(['error' => 'Page introuvable'], Response::HTTP_NOT_FOUND);
        }

        $payload = json_decode($request->getContent(), true);
        if (!isset($payload['order']) || !is_array($payload['order'])) {
            return $this->json(['error' => 'Format invalide'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->blockManager->reorderBlocks($payload['order']);
        return $this->json(['success' => true]);
    }

    #[Route('/upload', name: 'upload', methods: ['POST'])]
    public function upload(Request $request): JsonResponse
    {
        /** @var UploadedFile|null $file */
        $file = $request->files->get('file');
        if (!$file) {
            return $this->json(['error' => 'Aucun fichier'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $mimeType = $file->getMimeType() ?? '';
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'video/mp4', 'video/webm'];
        if (!in_array($mimeType, $allowed, true)) {
            return $this->json(['error' => 'Type de fichier non autorisé'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $ext = $file->guessExtension()
            ?? pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION)
            ?: 'bin';
        $filename = uniqid('', true) . '.' . $ext;
        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads';
        $file->move($uploadDir, $filename);

        $fullPath = $uploadDir . '/' . $filename;
        if (str_starts_with($mimeType, 'image/')) {
            $this->resizeImage($fullPath, 1200);
        }

        return $this->json(['url' => '/uploads/' . $filename], Response::HTTP_CREATED);
    }
}
