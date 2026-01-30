<?php

namespace App\Tests\Factory;

use App\Entity\Block;
use App\Entity\Page;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

trait PositionTrait
{
    public static function withNextPosition(PersistentProxyObjectFactory $factory, Page $page): self
    {
        // Trouve la prochaine position pour cette page
        $existingCount = count($page->getBlocks());
        return $factory->with(['position' => $existingCount + 1]);
    }
}
