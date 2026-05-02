<?php

namespace App\Tests\Integration;

use App\Entity\BlockText;
use App\Entity\Page;
use App\Repository\BlockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BlockRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private BlockRepository $repo;

    protected function setUp(): void
    {
        static::bootKernel();
        $this->em   = static::getContainer()->get(EntityManagerInterface::class);
        $this->repo = static::getContainer()->get(BlockRepository::class);
        $this->em->getConnection()->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->em->getConnection()->rollBack();
        parent::tearDown();
    }

    private function persistPage(): Page
    {
        $page = new Page();
        $page->setTitle('Test Page');
        $page->setSlug('test-page-' . uniqid());
        $this->em->persist($page);
        return $page;
    }

    private function persistBlock(Page $page, int $position): BlockText
    {
        $block = new BlockText();
        $block->setPage($page);
        $block->setPosition($position);
        $block->setContent(['title' => 'T', 'body' => 'B']);
        $this->em->persist($block);
        return $block;
    }

    public function testFindMaxPositionForPage_WithBlocks_ReturnsMax(): void
    {
        $page = $this->persistPage();
        $this->persistBlock($page, 3);
        $this->persistBlock($page, 8);
        $this->persistBlock($page, 5);
        $this->em->flush();

        $this->assertSame(8, $this->repo->findMaxPositionForPage($page->getId()));
    }

    public function testFindMaxPositionForPage_WithNoBlocks_ReturnsZero(): void
    {
        $page = $this->persistPage();
        $this->em->flush();

        $this->assertSame(0, $this->repo->findMaxPositionForPage($page->getId()));
    }

    public function testFindMaxPositionForPage_OnlyCountsBlocksForGivenPage(): void
    {
        $page1 = $this->persistPage();
        $page2 = $this->persistPage();
        $this->persistBlock($page1, 10);
        $this->persistBlock($page2, 2);
        $this->em->flush();

        $this->assertSame(2, $this->repo->findMaxPositionForPage($page2->getId()));
    }
}
