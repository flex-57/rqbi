<?php

namespace App\DataFixtures;

use App\Entity\Page;
use App\Entity\User;
use App\Enum\BlockType;
use App\Factory\BlockFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
        private readonly BlockFactory $blockFactory,
    ) {}

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@rqbi.fr');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin'));
        $manager->persist($admin);

        $home = new Page();
        $home->setTitle('Accueil');
        $home->setSlug('accueil');
        $home->setPublished(true);
        $manager->persist($home);
        $manager->flush();

        $hero = $this->blockFactory->create(BlockType::TEXT);
        $hero->setPage($home);
        $hero->setPosition(1);
        $hero->setContent([
            'title' => 'Bienvenue à la Régie de Quartier Behren Insertion',
            'body'  => 'La RQBI est une association d\'insertion par l\'activité économique basée à Behren-lès-Forbach. Nous accompagnons les personnes éloignées de l\'emploi vers un retour à l\'activité professionnelle.',
        ]);
        $manager->persist($hero);

        $banner = $this->blockFactory->create(BlockType::IMAGE);
        $banner->setPage($home);
        $banner->setPosition(2);
        $banner->setContent([
            'url'     => '/images/BANNIERE-LOGO-PARTENAIRE-2048x374.png',
            'alt'     => 'Partenaires RQBI',
            'caption' => 'Nos partenaires',
        ]);
        $manager->persist($banner);

        $cta = $this->blockFactory->create(BlockType::CTA);
        $cta->setPage($home);
        $cta->setPosition(3);
        $cta->setContent([
            'title'        => 'Vous cherchez un emploi ?',
            'subtitle'     => 'La RQBI vous accompagne dans votre parcours d\'insertion professionnelle.',
            'button_label' => 'Nous contacter',
            'button_url'   => '/contact',
            'background'   => 'red',
        ]);
        $manager->persist($cta);

        $divider = $this->blockFactory->create(BlockType::DIVIDER);
        $divider->setPage($home);
        $divider->setPosition(4);
        $divider->setContent(['style' => 'line', 'label' => null]);
        $manager->persist($divider);

        foreach (['Qui sommes-nous ?' => 'qui-sommes-nous', 'Nos services' => 'nos-services', 'Contact' => 'contact'] as $title => $slug) {
            $page = new Page();
            $page->setTitle($title);
            $page->setSlug($slug);
            $page->setPublished(true);
            $manager->persist($page);
        }

        $manager->flush();
    }
}
