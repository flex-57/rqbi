# CMS RQBI

CMS sur-mesure pour la **Régie de Quartier Behren Insertion** — association d'insertion par l'activité économique à Behren-lès-Forbach (Moselle).

## Stack

| Couche | Technologies |
|--------|-------------|
| Backend | Symfony 7.2 · PHP 8.3 · Doctrine ORM (STI) · JWT |
| Frontend | Vue.js 3 · Vite 5 · TailwindCSS 3 · Pinia |
| Base de données | MySQL 8 (dev/prod) · SQLite (tests) |
| Tests | PHPUnit 12 — 53 tests unitaires + fonctionnels |

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
npm run dev            # Frontend Vite (Terminal séparé)
```

Compte admin de test : `admin@rqbi.fr` / `admin`
Accès administration : `/login` (lien « Admin » discret en haut à droite)

## Architecture

```
src/
├── Controller/Api/     — API JSON pure (PageController, BlockController, ContactController)
├── Entity/             — Page, Block (STI, 12 types), User
├── Factory/            — BlockFactory (seul point d'instanciation des blocs)
├── Service/            — BlockManager (CRUD blocs + nettoyage uploads)
└── Enum/               — BlockType

assets/
├── app.ts              — Bootstrap Vue + directive v-animate-in
├── styles/app.css      — Polices, palette RQBI, composants Tailwind
└── vue/
    ├── blocks/         — 12 composants bloc (Text, Image, Slider, Video, Cta, …)
    ├── components/     — NavBar, PageView, BlockRenderer, BlockEditor, …
    ├── stores/         — auth, pages, blocks (Pinia)
    └── composables/    — api.ts (Axios + JWT), useCounter.ts
```

## Types de blocs

`Text` · `Image` · `Slider` · `Video` · `CTA` · `Divider` · `Stats` · `Cards` · `Timeline` · `Contact` · `FAQ` · `Gallery`

## Tests

```bash
php bin/phpunit                  # Suite complète (53 tests)
php bin/phpunit tests/Unit/      # Unitaires uniquement
php bin/phpunit tests/Functional # Fonctionnels uniquement
```

La base SQLite de test est recréée automatiquement avant chaque run (`tests/bootstrap.php`).

## Upload d'images

`POST /api/upload` — redimensionne à max 1 200 px (GD), stocke dans `public/uploads/`.
Le dossier `public/uploads/` est ignoré par git.
