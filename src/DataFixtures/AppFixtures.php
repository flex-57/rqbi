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

        $this->loadHome($manager);
        $this->loadAbout($manager);
        $this->loadActions($manager);
        $this->loadContact($manager);
        $this->loadGallery($manager);

        $manager->flush();
    }

    private function page(ObjectManager $manager, string $title, string $slug): Page
    {
        $page = new Page();
        $page->setTitle($title);
        $page->setSlug($slug);
        $page->setPublished(true);
        $manager->persist($page);
        $manager->flush();
        return $page;
    }

    private function block(ObjectManager $manager, Page $page, BlockType $type, int $pos, array $content): void
    {
        $b = $this->blockFactory->create($type);
        $b->setPage($page);
        $b->setPosition($pos);
        $b->setContent($content);
        $manager->persist($b);
    }

    private function loadHome(ObjectManager $manager): void
    {
        $home = $this->page($manager, 'Accueil', 'accueil');

        $this->block($manager, $home, BlockType::SLIDER, 1, [
            'autoplay' => true,
            'interval' => 4000,
            'slides'   => [
                ['url' => 'https://www.regiedequartier-behren.fr/wp-content/uploads/2024/03/ANMP0046-scaled.jpg',           'alt' => 'Équipe RQBI'],
                ['url' => 'https://www.regiedequartier-behren.fr/wp-content/uploads/2024/03/ANMP0087-scaled.jpg',           'alt' => 'Chantier espaces verts'],
                ['url' => 'https://www.regiedequartier-behren.fr/wp-content/uploads/2024/03/ANMP0045-scaled.jpg',           'alt' => 'Activité RQBI'],
                ['url' => 'https://www.regiedequartier-behren.fr/wp-content/uploads/2024/03/ANMP0093-Copie-scaled.jpg',     'alt' => 'Chantier insertion'],
                ['url' => 'https://www.regiedequartier-behren.fr/wp-content/uploads/2024/03/ANMP0088-scaled.jpg',           'alt' => 'Jardin solidaire'],
                ['url' => 'https://www.regiedequartier-behren.fr/wp-content/uploads/2024/03/ANMP0080-scaled.jpg',           'alt' => 'Pôle propreté'],
                ['url' => 'https://www.regiedequartier-behren.fr/wp-content/uploads/2024/03/ANMP0100-scaled.jpg',           'alt' => 'Accompagnement'],
                ['url' => 'https://www.regiedequartier-behren.fr/wp-content/uploads/2024/03/ANMP0126-scaled.jpg',           'alt' => 'Formation'],
                ['url' => 'https://www.regiedequartier-behren.fr/wp-content/uploads/2024/03/ANMP0108-scaled.jpg',           'alt' => 'Insertion professionnelle'],
            ],
        ]);

        $this->block($manager, $home, BlockType::TEXT, 2, [
            'title' => 'Une approche sociale, économique et citoyenne',
            'body'  => '<p>Une approche sociale, économique et citoyenne répondant aux besoins d\'une région confrontée à des enjeux importants, favorisant la création d\'activités commerciales ou non commerciales qui soutiennent l\'emploi local.</p><p>Aujourd\'hui la Régie de Quartier « Behren Insertion » c\'est avant tout une équipe de femmes et d\'hommes aux compétences multiples, et une structure nous permettant de proposer des offres de qualité pour satisfaire pleinement chaque client. L\'intégration de notre personnel, la diversité, le développement de notre savoir et notre réactivité sont nos principaux atouts.</p>',
        ]);

        $this->block($manager, $home, BlockType::STATS, 3, [
            'title' => 'La RQBI en chiffres',
            'stats' => [
                ['value' => '97',   'label' => 'Salariés',               'color' => 'red'],
                ['value' => '20',   'label' => 'CDI',                    'color' => 'blue'],
                ['value' => '6',    'label' => 'Chantiers d\'insertion',  'color' => 'red'],
                ['value' => '2003', 'label' => 'Année de création',       'color' => 'blue'],
            ],
        ]);

        $this->block($manager, $home, BlockType::CARDS, 4, [
            'title'   => 'Nos secteurs d\'activité',
            'columns' => 3,
            'cards'   => [
                ['icon' => '🌿', 'title' => 'Espaces verts',     'text' => 'Tonte, élagage, abattage, taille de haies, entretien de massifs et pistes cyclables.',                 'accent' => 'red'],
                ['icon' => '🧹', 'title' => 'Propreté',          'text' => 'Entretien de parties communes, bâtiments communaux, nettoyage de fin de location.',                    'accent' => 'blue'],
                ['icon' => '🥦', 'title' => 'Jardin solidaire',  'text' => 'Production agricole biologique, permaculture, verger, vente de fruits et légumes Bio.',                'accent' => 'red'],
                ['icon' => '🛒', 'title' => 'Boutique solidaire','text' => 'Produits d\'hygiène à faible coût, fournitures bébé, espace d\'accueil pour bénéficiaires RSA.',       'accent' => 'blue'],
                ['icon' => '👕', 'title' => 'Laverie solidaire', 'text' => 'Lavage, repassage du linge, retouches et travaux de couture.',                                         'accent' => 'dark'],
                ['icon' => '🌳', 'title' => 'Élagage',           'text' => 'Coupe, abattage et élagage d\'arbres professionnels, broyage et valorisation des déchets verts.',      'accent' => 'dark'],
            ],
        ]);

        $this->block($manager, $home, BlockType::CTA, 5, [
            'title'        => 'Vous cherchez un emploi ?',
            'subtitle'     => 'La RQBI vous accompagne dans votre parcours d\'insertion professionnelle. Rejoignez nos chantiers et développez vos compétences.',
            'button_label' => 'Nous contacter',
            'button_url'   => '/contact',
            'background'   => 'red',
        ]);
    }

    private function loadAbout(ObjectManager $manager): void
    {
        $page = $this->page($manager, 'Qui sommes-nous ?', 'qui-sommes-nous');

        $this->block($manager, $page, BlockType::TEXT, 1, [
            'title' => 'La Régie de Quartier Behren Insertion',
            'body'  => '<p>La Régie de Quartier « Behren Insertion » (RQBI) est une <strong>Structure d\'Insertion par l\'Activité Économique (IAE)</strong> basée à Behren-lès-Forbach, en Moselle. Association de droit local (régime Alsace-Moselle), elle est labellisée CNLRQ depuis décembre 2003.</p><p>Notre équipe de femmes et d\'hommes aux compétences multiples propose des offres de qualité tout en accompagnant les personnes éloignées de l\'emploi. L\'intégration de notre personnel, la diversité, le développement du savoir et notre réactivité sont nos principaux atouts.</p><p>Notre structure se veut également responsable : bonnes conditions de travail, respect de la législation, utilisation de produits écoresponsables.</p>',
        ]);

        $this->block($manager, $page, BlockType::STATS, 2, [
            'stats' => [
                ['value' => '2003',  'label' => 'Année de création',           'color' => 'red'],
                ['value' => 'CNLRQ', 'label' => 'Labellisation nationale',     'color' => 'blue'],
                ['value' => '60%',   'label' => 'Salariés habitants de Behren','color' => 'red'],
                ['value' => '50 ETP','label' => 'Double conventionnement',     'color' => 'blue'],
            ],
        ]);

        $this->block($manager, $page, BlockType::TIMELINE, 3, [
            'title'  => 'Notre histoire',
            'events' => [
                ['year' => '2002',        'text' => 'Étude d\'opportunité menée par la ville de Behren-lès-Forbach pour la création d\'une régie de quartier.'],
                ['year' => 'Mars 2003',   'text' => 'Création officielle de l\'association le 4 mars 2003.'],
                ['year' => 'Déc. 2003',   'text' => 'Obtention du label CNLRQ et intégration au réseau national des Régies de Quartier.'],
                ['year' => 'Aujourd\'hui','text' => 'La RQBI compte entre 100 et 199 salariés (permanents + parcours d\'insertion), avec un double conventionnement EI (31 ETP) et ACI (19 ETP).'],
            ],
        ]);

        $this->block($manager, $page, BlockType::CARDS, 4, [
            'title'   => 'Nos 3 piliers',
            'columns' => 3,
            'cards'   => [
                ['icon' => '🤝', 'title' => 'Social',     'text' => 'Créer du lien social, offrir un emploi aux habitants en difficulté, accompagnement socioprofessionnel et parcours de formation.',                    'accent' => 'red'],
                ['icon' => '💼', 'title' => 'Économique', 'text' => 'Répondre aux besoins collectifs du territoire, partenariats financiers, circuits courts et développement local.',                                    'accent' => 'blue'],
                ['icon' => '🏘️', 'title' => 'Citoyen',   'text' => 'Renforcer la participation et la responsabilité des habitants dans la vie du quartier et de la cité.',                                               'accent' => 'dark'],
            ],
        ]);

        $this->block($manager, $page, BlockType::CTA, 5, [
            'title'        => 'Envie de nous rejoindre ?',
            'subtitle'     => 'Rejoignez nos chantiers d\'insertion ou découvrez comment bénéficier de nos services.',
            'button_label' => 'Contactez-nous',
            'button_url'   => '/contact',
            'background'   => 'dark',
        ]);
    }

    private function loadActions(ObjectManager $manager): void
    {
        $page = $this->page($manager, 'Nos actions', 'nos-actions');

        $this->block($manager, $page, BlockType::TEXT, 1, [
            'title' => 'Nos chantiers d\'insertion',
            'body'  => '<p>La RQBI gère <strong>6 chantiers d\'insertion</strong> offrant des emplois en CDDI à des personnes éloignées de l\'emploi. Chaque salarié bénéficie d\'un accompagnement social et professionnel personnalisé pour construire son projet et retrouver un emploi durable.</p>',
        ]);

        $this->block($manager, $page, BlockType::CARDS, 2, [
            'columns' => 3,
            'cards'   => [
                ['icon' => '🌿', 'title' => 'Espaces verts',     'text' => 'Tonte, élagage, abattage, taille de haies, entretien de massifs et pistes cyclables, nettoyage d\'étangs.',              'accent' => 'red'],
                ['icon' => '🧹', 'title' => 'Propreté',          'text' => 'Entretien de parties communes, bâtiments communaux, débarras et nettoyage de fin de location, encombrants.',             'accent' => 'blue'],
                ['icon' => '🥦', 'title' => 'Jardin solidaire',  'text' => 'Production agricole biologique, permaculture, exploitation d\'un verger, vente de fruits et légumes Bio au kg.',          'accent' => 'red'],
                ['icon' => '🛒', 'title' => 'Boutique solidaire','text' => 'Vente à faible coût de produits d\'hygiène, fournitures bébé, espace d\'accueil dédié aux bénéficiaires RSA.',           'accent' => 'blue'],
                ['icon' => '👕', 'title' => 'Laverie solidaire', 'text' => 'Lavage, repassage du linge (vêtements, linge de maison), retouches et travaux de couture.',                              'accent' => 'dark'],
                ['icon' => '🌳', 'title' => 'Élagage',           'text' => 'Coupe, abattage et élagage d\'arbres professionnels, broyage et valorisation des déchets verts.',                         'accent' => 'dark'],
            ],
        ]);

        $this->block($manager, $page, BlockType::DIVIDER, 3, ['style' => 'line', 'label' => 'Accompagnement']);

        $this->block($manager, $page, BlockType::TEXT, 4, [
            'title' => 'Accompagnement Social et Professionnel',
            'body'  => '<p>L\'ASP assure un <strong>suivi personnalisé</strong> pour aider chaque salarié à définir et concrétiser son projet professionnel. Elle intervient à deux niveaux :</p><ul><li><strong>Social :</strong> remobilisation personnelle, confiance et estime de soi, autonomie, sens de la valeur travail.</li><li><strong>Professionnel :</strong> reprise et maintien dans le travail, montée en compétences.</li></ul><p>L\'ASP accueille, écoute, établit un plan d\'action personnalisé, propose des solutions et évalue les progrès de chaque salarié.</p>',
        ]);

        $this->block($manager, $page, BlockType::FAQ, 5, [
            'title' => 'Questions fréquentes',
            'items' => [
                [
                    'question' => 'La Régie de Behren, c\'est quoi ?',
                    'answer'   => 'La Régie vise à favoriser l\'insertion professionnelle et sociale des habitants. Elle offre formations, ateliers et accompagnements personnalisés pour aider les résidents à développer leurs compétences et trouver un emploi stable.',
                ],
                [
                    'question' => 'Comment intégrer nos chantiers d\'insertion ?',
                    'answer'   => 'Toute personne peut rejoindre sous réserve d\'être au chômage de longue durée et d\'avoir été inscrite sur la plateforme de l\'inclusion par son conseiller France Travail.',
                ],
                [
                    'question' => 'Pourquoi choisir la Régie pour vos travaux ?',
                    'answer'   => 'Services de qualité réalisés par des professionnels compétents, participation à l\'insertion sociale et professionnelle, contribution au développement économique et solidaire du quartier, accompagnement personnalisé et relation de confiance.',
                ],
            ],
        ]);
    }

    private function loadContact(ObjectManager $manager): void
    {
        $page = $this->page($manager, 'Contact', 'contact');

        $this->block($manager, $page, BlockType::CONTACT, 1, [
            'title'    => 'Contactez-nous',
            'address'  => "Annexe Chateaubriand\n1 rue de l'École\n57460 Behren-lès-Forbach",
            'phone'    => '03 87 88 39 85',
            'email'    => 'secretariat@rqbi.fr',
            'hours'    => "Lun – Ven : 8h00 – 12h00\net 14h00 – 17h00",
            'show_map' => true,
            'lat'      => 49.1654,
            'lon'      => 6.9427,
        ]);
    }

    private function loadGallery(ObjectManager $manager): void
    {
        $page = $this->page($manager, 'Galerie', 'galerie');

        $this->block($manager, $page, BlockType::TEXT, 1, [
            'title' => 'Notre galerie',
            'body'  => '<p>Découvrez nos équipes en action sur les différents chantiers d\'insertion de la Régie de Quartier Behren Insertion.</p>',
        ]);

        $this->block($manager, $page, BlockType::GALLERY, 2, [
            'columns' => 3,
            'items'   => [
                ['url' => 'https://www.regiedequartier-behren.fr/wp-content/uploads/2024/06/IMG_2039-scaled.jpg',            'alt' => 'Activité RQBI'],
                ['url' => 'https://www.regiedequartier-behren.fr/wp-content/uploads/2024/06/IMG_2055-scaled.jpg',            'alt' => 'Chantier RQBI'],
                ['url' => 'https://www.regiedequartier-behren.fr/wp-content/uploads/2024/04/ANMP0066-Copie-scaled.jpg',      'alt' => 'Équipe RQBI'],
                ['url' => 'https://www.regiedequartier-behren.fr/wp-content/uploads/2024/04/ANMP0079-scaled.jpg',            'alt' => 'Espaces verts'],
                ['url' => 'https://www.regiedequartier-behren.fr/wp-content/uploads/2024/04/ANMP0081-1-scaled.jpg',          'alt' => 'Travaux paysagers'],
                ['url' => 'https://www.regiedequartier-behren.fr/wp-content/uploads/2024/04/ANMP0093-Copie-scaled.jpg',      'alt' => 'Insertion professionnelle'],
                ['url' => 'https://www.regiedequartier-behren.fr/wp-content/uploads/2024/04/ANMP0102-1-scaled.jpg',          'alt' => 'Chantier insertion'],
                ['url' => 'https://www.regiedequartier-behren.fr/wp-content/uploads/2024/04/ANMP0105-1-scaled.jpg',          'alt' => 'Formation'],
                ['url' => 'https://www.regiedequartier-behren.fr/wp-content/uploads/2024/04/ANMP0112-1-scaled.jpg',          'alt' => 'Accompagnement'],
                ['url' => 'https://www.regiedequartier-behren.fr/wp-content/uploads/2024/04/ANMP0118-scaled.jpg',            'alt' => 'Activité chantier'],
                ['url' => 'https://www.regiedequartier-behren.fr/wp-content/uploads/2024/04/Proprete-Cages-decaliers-scaled.jpg','alt' => 'Pôle propreté — cages d\'escaliers'],
                ['url' => 'https://www.regiedequartier-behren.fr/wp-content/uploads/2024/04/Proprete-portes-dentrees-scaled.jpg','alt' => 'Pôle propreté — portes d\'entrées'],
                ['url' => 'https://www.regiedequartier-behren.fr/wp-content/uploads/2024/04/Abatage.jpg',                    'alt' => 'Élagage — abattage'],
                ['url' => 'https://www.regiedequartier-behren.fr/wp-content/uploads/2024/05/ANMP0280-scaled.jpg',            'alt' => 'Équipe terrain'],
                ['url' => 'https://www.regiedequartier-behren.fr/wp-content/uploads/2024/04/ANMP0085-scaled.jpg',            'alt' => 'Chantier'],
                ['url' => 'https://www.regiedequartier-behren.fr/wp-content/uploads/2024/04/ANMP0187-scaled.jpg',            'alt' => 'Insertion'],
                ['url' => 'https://www.regiedequartier-behren.fr/wp-content/uploads/2024/04/photos.jpg',                     'alt' => 'RQBI en images'],
                ['url' => 'https://www.regiedequartier-behren.fr/wp-content/uploads/2024/04/IMG_5151-scaled.jpg',            'alt' => 'Jardin solidaire'],
                ['url' => 'https://www.regiedequartier-behren.fr/wp-content/uploads/2024/04/es5.jpg',                        'alt' => 'Espaces verts'],
            ],
        ]);
    }
}
