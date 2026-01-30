<?php

namespace App\Tests\Functional\Factory;

use App\Tests\BaseWebTestCase;
use App\Tests\Factory\PageFactory;
use App\Tests\Factory\BlockTextFactory;
use App\Tests\Factory\UserFactory;
use Zenstruck\Foundry\Test\Factories;

class FactorySmokeTest extends BaseWebTestCase
{
    use Factories;

    public function testPageFactoryCreatesValidPage(): void
    {
        $page = PageFactory::createOne();

        $this->assertNotNull($page->getId());
        $this->assertNotEmpty($page->getSlug());
        $this->assertTrue($page->isPublished());
        $this->assertFalse($page->isHomepage());
    }

    public function testBlockTextFactoryWithPage(): void
    {
        $page = PageFactory::createOne(['title' => 'Page Test']);
        $block = BlockTextFactory::createOne([
            'page' => $page,
            'content' => 'Mon contenu de test',
        ]);

        $this->assertSame($page->getId(), $block->getPage()->getId());
        $this->assertSame('Mon contenu de test', $block->getContent());
    }

    public function testUserFactoryHashesPassword(): void
    {
        $user = UserFactory::createOne(['username' => 'testuser']);

        // Force le rechargement depuis la DB
        $this->em->refresh($user);

        $this->assertNotEmpty($user->getPassword());
        $this->assertStringStartsWith('$2y$', $user->getPassword());
    }

    public function testAdminUserCreation(): void
    {
        $admin = UserFactory::new()->asAdmin()->create();

        $this->assertContains('ROLE_ADMIN', $admin->getRoles());
    }

    public function testMultipleHomepagesAreUnique(): void
    {
        $home1 = PageFactory::new()->asHomepage()->create();
        $home2 = PageFactory::new()->asHomepage()->create();

        // Vérifie que les slugs sont différents
        $this->assertNotSame($home1->getSlug(), $home2->getSlug());
    }
}
