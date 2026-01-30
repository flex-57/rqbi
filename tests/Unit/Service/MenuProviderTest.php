<?php

namespace App\Tests\Unit\Service;

use App\Entity\Page;
use App\Repository\PageRepository;
use App\Service\MenuProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class MenuProviderTest extends TestCase
{
    /** @var PageRepository&\PHPUnit\Framework\MockObject\MockObject */
    private PageRepository $pageRepository;
    /** @var TagAwareCacheInterface&\PHPUnit\Framework\MockObject\MockObject */
    private TagAwareCacheInterface $cache;
    private MenuProvider $provider;
    /** @var ItemInterface&\PHPUnit\Framework\MockObject\Stub */
    private ItemInterface $cacheItem;

    protected function setUp(): void
    {
        $this->pageRepository = $this->createMock(PageRepository::class);
        $this->cache = $this->createMock(TagAwareCacheInterface::class);

        $this->cacheItem = $this->createStub(ItemInterface::class);
        $this->cacheItem->method('expiresAfter')->willReturnSelf();
        $this->cacheItem->method('tag')->willReturnSelf();

        $this->provider = new MenuProvider($this->pageRepository, $this->cache);
    }

    public function testGetFullMenuBuildsMenuAndCachesIt(): void
    {
        // Fake page hierarchy
        $root = (new Page())->setTitle('Root')->setSlug('root')->setFullSlug('root');
        $group = (new Page())->setTitle('Group')->setSlug('group')->setFullSlug('root/group');
        $item = (new Page())->setTitle('Item')->setSlug('item')->setFullSlug('root/group/item');

        // Relations
        $root->addChild($group);
        $group->addChild($item);

        // Repository renvoie la hiérarchie
        $this->pageRepository
            ->expects($this->once())
            ->method('findRootPagesWithChildren')
            ->willReturn([$root]);

        // Cache retourne le résultat de callback
        $item = $this->createStub(ItemInterface::class);
        $item->method('expiresAfter')->willReturnSelf();
        $item->method('tag')->willReturnSelf();

        // Le cache appelle le callback avec cet item
        $this->cache
            ->expects($this->once())
            ->method('get')
            ->willReturnCallback(function ($key, $callback) use ($item) {
                return $callback($item);
            });

        $menu = $this->provider->getFullMenu();

        $this->assertCount(1, $menu);
        $this->assertSame('Root', $menu[0]['title']);
        $this->assertSame('Group', $menu[0]['menuGroups'][0]['title']);
        $this->assertSame('Item', $menu[0]['menuGroups'][0]['menuItems'][0]['title']);
    }

    public function testUsesCacheOnSecondCall(): void
    {
        $cachedValue = ['cached' => true];

        // Cache returns directly without calling repository
        $this->cache
            ->expects($this->once())
            ->method('get')
            ->willReturn($cachedValue);

        $this->pageRepository
            ->expects($this->never())
            ->method('findRootPagesWithChildren');

        $this->assertSame($cachedValue, $this->provider->getFullMenu());
    }
}
