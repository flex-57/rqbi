<?php

namespace App\Tests\Factory;

use App\Entity\Page;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Page>
 */
final class PageFactory extends PersistentProxyObjectFactory
{
    private static int $homePageCounter = 0;

    #[\Override]
    public static function class(): string
    {
        return Page::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'title' => self::faker()->sentence(3),
            'slug' => self::faker()->slug(3),
            'fullSlug' => '', // Le listener calcule le fullSlug lors de l'initialisation
            'isHomepage' => false,
            'isPublished' => true,
            'isInMainNav' => false,
            'position' => 1,
            'parent' => null,
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this
            ->afterPersist(function(Page $page): void {
                $slugs = array_map(
                    fn(?Page $p) => $p?->getSlug(),
                    $page->getAncestry()
                );
                $page->setFullSlug(implode('/', array_filter($slugs)));
            });
    }

    public function withNavPage(bool $isInMainNav = true): self
    {
        return $this->with(['isInMainNav' => $isInMainNav]);
    }

    public function asHomepage(): self
    {
        self::$homePageCounter++;

        return $this->with([
            'isHomepage' => true,
            'slug' => 'accueil-' . self::$homePageCounter,
            'title' => 'Accueil ' . self::$homePageCounter,
        ]);
    }

    public function withParent($parent): self
    {
        return $this->with(['parent' => $parent]);
    }

    public function published(bool $published = true): self
    {
        return $this->with(['isPublished' => $published]);
    }
}
