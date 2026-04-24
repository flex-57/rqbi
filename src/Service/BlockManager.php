<?php

namespace App\Service;

use App\Entity\Block;
use App\Entity\Page;
use App\Enum\BlockType;
use App\Factory\BlockFactory;
use App\Repository\BlockRepository;
use Doctrine\ORM\EntityManagerInterface;

class BlockManager
{
    public function __construct(
        private readonly BlockFactory $factory,
        private readonly BlockRepository $blockRepository,
        private readonly EntityManagerInterface $em,
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
        $block->setContent($content);
        $this->em->flush();

        return $block;
    }

    public function deleteBlock(Block $block): void
    {
        $this->em->remove($block);
        $this->em->flush();
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
