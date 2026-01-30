<?php

namespace App\Tests\Unit\Listener;

use App\Listener\MenuCacheInvalidator;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class MenuCacheInvalidatorTest extends TestCase
{
    public function testInvalidateTagsIsCalled(): void
    {
        $cache = $this->createMock(TagAwareCacheInterface::class);

        $cache->expects($this->once())
            ->method('invalidateTags')
            ->with(['cache_full_menu']);

        $listener = new MenuCacheInvalidator($cache);

        // Doctrine appelle __invoke()
        $listener();
    }
}
