<?php

namespace App\Tests\Factory;

use App\Entity\BlockText;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<BlockText>
 */
final class BlockTextFactory extends PersistentProxyObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return BlockText::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'content' => self::faker()->paragraphs(3, true),
            'isActive' => true,
            'page' => PageFactory::new(),
            'position' => 1, // Simplifié pour l'instant
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this;
    }

    public function withShortContent(): self
    {
        return $this->with(['content' => 'Court']);
    }

    public function inactive(): self
    {
        return $this->with(['isActive' => false]);
    }

    public function forPage($page): self
    {
        return $this->with(['page' => $page]);
    }
}
