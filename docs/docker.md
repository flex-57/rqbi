# Docker — Guide complet

## Architecture

Le projet utilise un **Dockerfile multi-stage** (`docker/php/Dockerfile`) et deux fichiers Compose :

| Fichier | Usage |
|---------|-------|
| `docker-compose.yml` | Développement local |
| `docker-compose.prod.yml` | Production (standalone) |

### Stages du Dockerfile

```
base        — PHP 8.3-FPM Alpine + extensions (pdo_mysql, gd, intl, zip, opcache)
  └── dev         — + Xdebug 3
node-builder      — Node 20 Alpine → npm run build → public/build/
  └── prod        — base + vendor (composer --no-dev) + code + assets buildés
        └── nginx-prod — Nginx 1.27 + public/ copiée depuis prod
```

---

## Développement

### Premier démarrage

```bash
docker compose up --build
```

L'entrypoint (`docker/php/entrypoint.sh`) exécute automatiquement :
1. `composer install`
2. Génération des clés JWT (`config/jwt/private.pem` / `public.pem`)
3. `doctrine:migrations:migrate`

```bash
# Charger les fixtures RQBI (données de démonstration)
docker compose exec php php bin/console doctrine:fixtures:load
```

### Services disponibles

| Service | URL / Port | Description |
|---------|-----------|-------------|
| Application | http://localhost:8000 | Nginx → PHP-FPM |
| Vite dev server | http://localhost:5173 | Hot-reload Vue.js |
| Mailpit | http://localhost:8025 | Capture des emails (formulaire contact) |
| MySQL | localhost:**3307** | Port décalé pour éviter conflit avec MySQL local |

> Le port MySQL est **3307** (pas 3306) pour ne pas entrer en conflit avec une installation locale.

### Commandes courantes

```bash
# Lancer en arrière-plan
docker compose up -d

# Voir les logs PHP
docker compose logs -f php

# Accès shell PHP
docker compose exec php sh

# Console Symfony
docker compose exec php php bin/console <commande>

# Créer un compte admin
docker compose exec php php bin/console app:create-admin

# Lancer les tests PHPUnit
docker compose exec php php bin/phpunit

# Arrêter et supprimer les containers
docker compose down

# Supprimer aussi les volumes (reset complet BDD)
docker compose down -v
```

### Xdebug

Xdebug est préconfiguré en mode `debug` sur le port **9003**.

Dans VS Code, ajouter dans `.vscode/launch.json` :

```json
{
  "version": "0.2.0",
  "configurations": [
    {
      "name": "Listen for Xdebug (Docker)",
      "type": "php",
      "request": "launch",
      "port": 9003,
      "pathMappings": {
        "/var/www/html": "${workspaceFolder}"
      }
    }
  ]
}
```

---

## Production

### Variables d'environnement

Copier `.env.docker` en `.env.local` sur le serveur et remplir toutes les valeurs :

```bash
cp .env.docker .env.local
# Éditer .env.local avec les vraies valeurs
```

| Variable | Description |
|----------|-------------|
| `APP_SECRET` | Chaîne aléatoire 32+ caractères |
| `DATABASE_URL` | URL de connexion MySQL complète |
| `MYSQL_ROOT_PASSWORD` | Mot de passe root MySQL |
| `MYSQL_PASSWORD` | Mot de passe user `rqbi` |
| `JWT_PASSPHRASE` | Passphrase pour les clés JWT |
| `MAILER_DSN` | DSN du serveur SMTP (ex: `smtp://user:pass@host:587`) |
| `CONTACT_EMAIL` | Destinataire du formulaire de contact |

### Déploiement

```bash
# Build et démarrage
docker compose -f docker-compose.prod.yml --env-file .env.local up -d --build

# Première fois : charger les fixtures (optionnel en prod)
docker compose -f docker-compose.prod.yml exec php php bin/console doctrine:fixtures:load
```

L'entrypoint prod exécute automatiquement :
1. Génération des clés JWT si le volume `jwt_keys` est vide
2. `cache:warmup`
3. `doctrine:migrations:migrate`

### Volumes persistants (prod)

| Volume | Contenu |
|--------|---------|
| `mysql_data` | Base de données MySQL |
| `uploads` | Images uploadées (`public/uploads/`) — monté sur php ET nginx |
| `jwt_keys` | Clés JWT (`config/jwt/*.pem`) |

> Les clés JWT et la BDD survivent aux redéploiements grâce aux volumes nommés.

### Mise à jour de l'application

```bash
# Rebuild les images avec le nouveau code
docker compose -f docker-compose.prod.yml --env-file .env.local up -d --build

# Les migrations tournent automatiquement à chaque démarrage
```

---

## Structure des fichiers Docker

```
.dockerignore                   — exclut node_modules, vendor, var, uploads, config/jwt
docker/
├── php/
│   ├── Dockerfile              — multi-stage (base / dev / node-builder / prod / nginx-prod)
│   ├── entrypoint.sh           — bootstrap (composer, JWT, migrations, warmup)
│   └── conf.d/
│       ├── app.ini             — PHP prod (opcache, upload limits)
│       └── app.dev.ini         — PHP dev (Xdebug, display_errors)
└── nginx/
    ├── dev.conf                — proxy simple vers PHP-FPM
    └── prod.conf               — + cache headers, gzip, sécurité
docker-compose.yml              — dev (nginx + php + node + mysql + mailpit)
docker-compose.prod.yml         — prod standalone (nginx-prod + php + mysql)
.env.docker                     — template variables de production
```
