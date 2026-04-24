<?php

namespace App\Tests\Unit;

use App\Entity\BlockText;
use App\Entity\Page;
use App\Enum\BlockType;
use App\Factory\BlockFactory;
use App\Repository\BlockRepository;
use App\Service\BlockManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BlockManagerTest extends TestCase
{
    private BlockFactory $factory;
    private BlockRepository&MockObject $blockRepository;
    private EntityManagerInterface&MockObject $em;
    private BlockManager $manager;

    protected function setUp(): void
    {
        $this->factory = new BlockFactory();
        $this->blockRepository = $this->createMock(BlockRepository::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->manager = new BlockManager($this->factory, $this->blockRepository, $this->em, '/tmp');
    }

    public function testCreateBlock_WithValidData_PersistsAndFlushes(): void
    {
        $page = new Page();
        $this->blockRepository->method('findMaxPositionForPage')->willReturn(2);

        $this->em->expects($this->once())->method('persist');
        $this->em->expects($this->once())->method('flush');

        $block = $this->manager->createBlock($page, BlockType::TEXT, ['title' => 'Test', 'body' => 'Hello']);

        $this->assertInstanceOf(BlockText::class, $block);
        $this->assertSame(3, $block->getPosition());
        $this->assertSame(['title' => 'Test', 'body' => 'Hello'], $block->getContent());
    }

    public function testCreateBlock_WithEmptyPage_SetsPositionOne(): void
    {
        $page = new Page();
        $this->blockRepository->method('findMaxPositionForPage')->willReturn(0);
        $this->em->method('persist');
        $this->em->method('flush');

        $block = $this->manager->createBlock($page, BlockType::TEXT);

        $this->assertSame(1, $block->getPosition());
    }

    public function testUpdateBlock_ChangesContentAndFlushes(): void
    {
        $block = new BlockText();
        $block->setContent(['title' => 'Old', 'body' => 'Old body']);

        $this->em->expects($this->once())->method('flush');

        $updated = $this->manager->updateBlock($block, ['title' => 'New', 'body' => 'New body']);

        $this->assertSame(['title' => 'New', 'body' => 'New body'], $updated->getContent());
    }

    public function testDeleteBlock_RemovesAndFlushes(): void
    {
        $block = new BlockText();
        $this->em->expects($this->once())->method('remove')->with($block);
        $this->em->expects($this->once())->method('flush');

        $this->manager->deleteBlock($block);
    }

    public function testToggleVisibility_TogglesAndFlushes(): void
    {
        $block = new BlockText();
        $this->assertTrue($block->isVisible());

        $this->em->expects($this->once())->method('flush');
        $result = $this->manager->toggleVisibility($block);

        $this->assertFalse($result->isVisible());
    }

    public function testReorderBlocks_UpdatesPositions(): void
    {
        $block1 = new BlockText();
        $block2 = new BlockText();

        $this->blockRepository->method('find')
            ->willReturnMap([[1, $block1], [2, $block2]]);

        $this->em->expects($this->once())->method('flush');

        $this->manager->reorderBlocks([
            ['id' => 1, 'position' => 5],
            ['id' => 2, 'position' => 3],
        ]);

        $this->assertSame(5, $block1->getPosition());
        $this->assertSame(3, $block2->getPosition());
    }
}
