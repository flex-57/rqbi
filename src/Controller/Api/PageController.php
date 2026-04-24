<?php

namespace App\Controller\Api;

use App\Entity\Page;
use App\Repository\PageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/api/pages', name: 'api_page_')]
class PageController extends AbstractController
{
    public function __construct(
        private readonly PageRepository $pageRepository,
        private readonly EntityManagerInterface $em,
        private readonly SerializerInterface $serializer,
        private readonly SluggerInterface $slugger,
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $pages = $this->pageRepository->findRootPages();
        $data = $this->serializer->serialize($pages, 'json', ['groups' => ['page:list']]);
        return JsonResponse::fromJsonString($data);
    }

    #[Route('/tree', name: 'tree', methods: ['GET'])]
    public function tree(): JsonResponse
    {
        $pages = $this->pageRepository->findRootPages();
        $data = $this->serializer->serialize($pages, 'json', ['groups' => ['page:read']]);
        return JsonResponse::fromJsonString($data);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $page = $this->pageRepository->findWithBlocksAndChildren($id);
        if (!$page) {
            return $this->json(['error' => 'Page introuvable'], Response::HTTP_NOT_FOUND);
        }
        $data = $this->serializer->serialize($page, 'json', ['groups' => ['page:read']]);
        return JsonResponse::fromJsonString($data);
    }

    #[Route('/slug/{slug}', name: 'show_by_slug', methods: ['GET'])]
    public function showBySlug(string $slug): JsonResponse
    {
        $page = $this->pageRepository->findBySlug($slug);
        if (!$page) {
            return $this->json(['error' => 'Page introuvable'], Response::HTTP_NOT_FOUND);
        }
        $data = $this->serializer->serialize($page, 'json', ['groups' => ['page:read']]);
        return JsonResponse::fromJsonString($data);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        if (empty($payload['title'])) {
            return $this->json(['error' => 'Le titre est obligatoire'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $page = new Page();
        $page->setTitle($payload['title']);
        $page->setSlug($this->generateUniqueSlug($payload['slug'] ?? $payload['title']));
        $page->setPublished($payload['published'] ?? false);
        $page->setMetaTitle($payload['meta_title'] ?? null);
        $page->setMetaDescription($payload['meta_description'] ?? null);

        if (!empty($payload['parent_id'])) {
            $parent = $this->pageRepository->find($payload['parent_id']);
            if ($parent) {
                if ($parent->getDepth() >= 2) {
                    return $this->json(['error' => 'Profondeur maximale (3 niveaux) atteinte'], Response::HTTP_UNPROCESSABLE_ENTITY);
                }
                $page->setParent($parent);
            }
        }

        $this->em->persist($page);
        $this->em->flush();

        $data = $this->serializer->serialize($page, 'json', ['groups' => ['page:read']]);
        return JsonResponse::fromJsonString($data, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(int $id, Request $request): JsonResponse
    {
        $page = $this->pageRepository->find($id);
        if (!$page) {
            return $this->json(['error' => 'Page introuvable'], Response::HTTP_NOT_FOUND);
        }

        $payload = json_decode($request->getContent(), true);

        if (isset($payload['title'])) {
            $page->setTitle($payload['title']);
        }
        if (isset($payload['slug'])) {
            $page->setSlug($this->generateUniqueSlug($payload['slug'], $id));
        }
        if (isset($payload['published'])) {
            $page->setPublished((bool) $payload['published']);
        }
        if (array_key_exists('parent_id', $payload)) {
            if ($payload['parent_id'] === null) {
                $page->setParent(null);
            } else {
                $parent = $this->pageRepository->find($payload['parent_id']);
                if ($parent && $parent->getDepth() < 2) {
                    $page->setParent($parent);
                }
            }
        }
        if (array_key_exists('meta_title', $payload)) {
            $page->setMetaTitle($payload['meta_title'] ?: null);
        }
        if (array_key_exists('meta_description', $payload)) {
            $page->setMetaDescription($payload['meta_description'] ?: null);
        }

        $this->em->flush();

        $data = $this->serializer->serialize($page, 'json', ['groups' => ['page:read']]);
        return JsonResponse::fromJsonString($data);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id): JsonResponse
    {
        $page = $this->pageRepository->find($id);
        if (!$page) {
            return $this->json(['error' => 'Page introuvable'], Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($page);
        $this->em->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    private function generateUniqueSlug(string $text, ?int $excludeId = null): string
    {
        $base = strtolower($this->slugger->slug($text)->toString());
        $slug = $base;
        $i = 1;

        while (true) {
            $existing = $this->pageRepository->findOneBy(['slug' => $slug]);
            if (!$existing || ($excludeId && $existing->getId() === $excludeId)) {
                break;
            }
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
