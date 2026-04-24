<?php

namespace App\Service;

use App\Entity\Block;
use App\Entity\Page;
use App\Enum\BlockType;
use App\Factory\BlockFactory;
use App\Repository\BlockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class BlockManager
{
    public function __construct(
        private readonly BlockFactory $factory,
        private readonly BlockRepository $blockRepository,
        private readonly EntityManagerInterface $em,
        #[Autowire('%kernel.project_dir%')] private readonly string $projectDir,
    ) {}

    public function createBlock(Page $page, BlockType $type, array $content = []): Block
    {
        $block = $this->factory->create($type);
        $block->setPage($page);
        $block->setContent($content);

        $maxPosition = $this->blockRepository->findMaxPositionForPage($page->getId() ?? 0);
        $block->setPosition($maxPosition + 1);

        $this->em->persist($block);
        $this->em->flush();

        return $block;
    }

    public function updateBlock(Block $block, array $content): Block
    {
        $removed = array_diff($this->extractUploadUrls($block->getContent()), $this->extractUploadUrls($content));
        foreach ($removed as $url) {
            $this->deleteUploadFile($url);
        }

        $block->setContent($content);
        $this->em->flush();

        return $block;
    }

    public function deleteBlock(Block $block): void
    {
        foreach ($this->extractUploadUrls($block->getContent()) as $url) {
            $this->deleteUploadFile($url);
        }

        $this->em->remove($block);
        $this->em->flush();
    }

    /** @return string[] */
    private function extractUploadUrls(array $content): array
    {
        $urls = [];

        if (!empty($content['url']) && str_starts_with((string) $content['url'], '/uploads/')) {
            $urls[] = $content['url'];
        }

        foreach ($content['slides'] ?? [] as $slide) {
            if (!empty($slide['url']) && str_starts_with((string) $slide['url'], '/uploads/')) {
                $urls[] = $slide['url'];
            }
        }

        return $urls;
    }

    private function deleteUploadFile(string $url): void
    {
        $path = $this->projectDir . '/public' . $url;
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    /** @param array<array{id: int, position: int}> $order */
    public function reorderBlocks(array $order): void
    {
        foreach ($order as $item) {
            $block = $this->blockRepository->find($item['id']);
            if ($block !== null) {
                $block->setPosition($item['position']);
            }
        }
        $this->em->flush();
    }

    public function toggleVisibility(Block $block): Block
    {
        $block->setVisible(!$block->isVisible());
        $this->em->flush();

        return $block;
    }
}
