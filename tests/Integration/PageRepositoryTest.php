<?php

namespace App\Tests\Integration;

use App\Entity\BlockText;
use App\Entity\Page;
use App\Repository\PageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PageRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private PageRepository $repo;

    protected function setUp(): void
    {
        static::bootKernel();
        $this->em   = static::getContainer()->get(EntityManagerInterface::class);
        $this->repo = static::getContainer()->get(PageRepository::class);
        $this->em->getConnection()->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->em->getConnection()->rollBack();
        parent::tearDown();
    }

    private function persistPage(string $title, ?Page $parent = null): Page
    {
        $page = new Page();
        $page->setTitle($title);
        $page->setSlug(strtolower(preg_replace('/\W+/', '-', $title)) . '-' . uniqid());
        if ($parent) {
            $page->setParent($parent);
        }
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

    public function testFindRootPages_ReturnsOnlyPagesWithoutParent(): void
    {
        $root  = $this->persistPage('Root');
        $child = $this->persistPage('Child', $root);
        $this->em->flush();
        $this->em->clear();

        $roots   = $this->repo->findRootPages();
        $rootIds = array_map(fn(Page $p) => $p->getId(), $roots);

        $this->assertContains($root->getId(), $rootIds);
        $this->assertNotContains($child->getId(), $rootIds);
    }

    public function testFindRootPages_OrderedByTitleAsc(): void
    {
        $pageB = $this->persistPage('ZZZ Beta');
        $pageA = $this->persistPage('ZZZ Alpha');
        $this->em->flush();
        $this->em->clear();

        $roots = $this->repo->findRootPages();

        // Filter to our two pages to avoid fixture pages interfering
        $ours = array_values(array_filter(
            $roots,
            fn(Page $p) => in_array($p->getId(), [$pageA->getId(), $pageB->getId()])
        ));

        $this->assertCount(2, $ours);
        $this->assertSame($pageA->getId(), $ours[0]->getId()); // Alpha < Beta
        $this->assertSame($pageB->getId(), $ours[1]->getId());
    }

    public function testFindWithBlocksAndChildren_ReturnsPageWithRelations(): void
    {
        $parent = $this->persistPage('Parent');
        $this->persistPage('Child', $parent);
        $this->persistBlock($parent, 1);
        $this->em->flush();
        $this->em->clear();

        $result = $this->repo->findWithBlocksAndChildren($parent->getId());

        $this->assertNotNull($result);
        $this->assertCount(1, $result->getBlocks());
        $this->assertCount(1, $result->getChildren());
    }

    public function testFindWithBlocksAndChildren_WithUnknownId_ReturnsNull(): void
    {
        $this->assertNull($this->repo->findWithBlocksAndChildren(999999));
    }

    public function testFindBySlug_WithExistingSlug_ReturnsPageWithBlocksOrderedByPosition(): void
    {
        $slug = 'test-slug-' . uniqid();
        $page = new Page();
        $page->setTitle('Slug Test');
        $page->setSlug($slug);
        $this->em->persist($page);

        $this->persistBlock($page, 3);
        $this->persistBlock($page, 1);
        $this->em->flush();
        $this->em->clear();

        $result = $this->repo->findBySlug($slug);

        $this->assertNotNull($result);
        $this->assertSame($slug, $result->getSlug());

        $positions = array_map(fn($b) => $b->getPosition(), $result->getBlocks()->toArray());
        $this->assertSame([1, 3], $positions);
    }

    public function testFindBySlug_WithUnknownSlug_ReturnsNull(): void
    {
        $this->assertNull($this->repo->findBySlug('slug-that-does-not-exist-xyz'));
    }
}
