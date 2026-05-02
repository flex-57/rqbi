# CMS RQBI

CMS sur-mesure pour la **Régie de Quartier Behren Insertion** — association d'insertion par l'activité économique à Behren-lès-Forbach (Moselle).

## Stack

| Couche | Technologies |
|--------|-------------|
| Backend | Symfony 7.4 · PHP 8.3 · Doctrine ORM (STI) · JWT |
| Frontend | Vue.js 3.5 · Vite 5.4 · TailwindCSS 3.4 · Pinia 3 |
| Base de données | MySQL 8 (dev/prod) · SQLite (tests) |
| Tests | PHPUnit 12 · Vitest 2 |

## Démarrage

### Avec Docker (recommandé)

```bash
docker compose up --build
```

L'entrypoint installe les dépendances, génère les clés JWT et lance les migrations automatiquement.

| Service | URL |
|---------|-----|
| Application | http://localhost:8000 |
| Vite (hot-reload) | http://localhost:5173 |
| Mailpit (mails dev) | http://localhost:8025 |
| MySQL | localhost:3307 |

```bash
# Charger les fixtures (première fois)
docker compose exec php php bin/console doctrine:fixtures:load
```

Voir [docs/docker.md](docs/docker.md) pour la documentation complète.

### Sans Docker

```bash
# Dépendances
composer install
npm install

# Clés JWT
php bin/console lexik:jwt:generate-keypair

# Base de données + fixtures
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load

# Serveurs de développement
symfony serve          # Backend — https://127.0.0.1:8000
npm run dev            # Frontend Vite (terminal séparé)
```

Compte admin de test : `admin@rqbi.fr` / `admin`
Accès administration : `/login` (lien « Admin » discret en haut à droite)

## Architecture

```
src/
├── Controller/Api/     — API JSON pure (PageController, BlockController, AuthController, ContactController)
├── Entity/             — Page, Block (STI, 12 types), User
├── Entity/Trait/       — Timestampable, Positionable, …
├── Enum/               — BlockType
├── Factory/            — BlockFactory (seul point d'instanciation des blocs)
├── Repository/         — PageRepository, BlockRepository
└── Service/            — BlockManager (orchestration CRUD blocs + uploads)

assets/vue/
├── blocks/             — 12 composants bloc (BlockText, BlockImage, BlockSlider, …)
├── components/         — BlockRenderer, BlockEditor, PageEditor, NavBar, PageView, …
├── stores/             — auth, pages, blocks (Pinia)
└── composables/        — api.ts (Axios + JWT intercepteur)

templates/
└── base.html.twig      — unique template Twig, bootstrap Vue.js
```

## Types de blocs

`Text` · `Image` · `Slider` · `Video` · `CTA` · `Divider` · `Stats` · `Cards` · `Timeline` · `Contact` · `FAQ` · `Gallery`

## Tests

```bash
# Suite PHP complète
php bin/phpunit

# Par catégorie
php bin/phpunit tests/Unit/
php bin/phpunit tests/Integration/
php bin/phpunit tests/Functional/

# Suite Vitest (composants Vue)
npx vitest run
```

| Catégorie | Fichiers | Couverture |
|-----------|----------|-----------|
| Unitaires | `BlockFactoryTest`, `BlockManagerTest` | Factory STI, orchestration blocs |
| Intégration | `BlockRepositoryTest`, `PageRepositoryTest` | Repository + SQLite, isolation par rollback |
| Fonctionnels | `AuthControllerTest`, `PageControllerTest`, `BlockControllerTest`, `ContactControllerTest` | Endpoints API JSON |
| Vitest | `BlockRenderer.test.ts`, `BlockEditor.test.ts` | Composants Vue (rendu, émits, validation) |

La base SQLite de test est recréée automatiquement avant chaque run (`tests/bootstrap.php`). Les tests d'intégration utilisent un rollback DBAL par test pour garantir l'isolation.

## Upload d'images

`POST /api/upload` — redimensionne à max 1 200 px (GD), stocke dans `public/uploads/`.
Le dossier `public/uploads/` est ignoré par git.
