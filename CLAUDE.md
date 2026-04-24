# CMS v2 — Contexte Claude Code

## Stack
- Symfony 7.4, PHP 8.3+
- Doctrine ORM, Single Table Inheritance (STI) pour les blocs
- API JSON (pas de Twig pour le rendu métier)
- Vue.js 3 + Vite + TailwindCSS
- Authentification : JWT

## Architecture
- `src/Entity/` : Page, Block (abstract), BlockText, BlockImage, BlockSlider, BlockVideo
- `src/Entity/Trait/` : Timestampable, Positionable, ... pour éviter la duplication
- `src/Enum/` : BlockType
- `src/Factory/` : BlockFactory
- `src/Service/` : BlockManager (orchestration), pas de logique dans les contrôleurs
- `src/Controller/Api/` : uniquement JSON, pas de render() Twig
- `assets/vue/` : composants Vue (BlockRenderer, BlockText, BlockImage, etc.)
- `templates/` : UN SEUL template Twig (base.html.twig) qui boot Vue.js

## Règles de codage
- Attributs PHP 8 uniquement (pas d'annotations Doctrine)
- Injection de dépendances par constructeur
- Contenu des blocs stocké en JSON (column `content`), pas de colonnes spécifiques par type
- Position des blocs gérée explicitement (int), ordre ASC
- Arborescence : self-referencing (parent/children), 3 niveaux max
- API : groupes de sérialisation pour éviter les références circulaires
- Pas de logique métier dans les contrôleurs (appeler BlockManager)

## Conventions Vue.js
- BlockRenderer.vue est le seul composant récursif
- Map des composants dans `assets/vue/blocks/index.ts`
- Chaque bloc reçoit `block` (objet complet) et `isEditing` (bool) en props
- Formulaires dynamiques générés à partir du schéma exposé par l'API
- Pas de fetch direct dans les composants blocs (données passées par props)

## Points d'attention
- STI : toujours utiliser BlockFactory pour instancier, jamais `new BlockText()` directement
- Les images/vidéos : URLs stockées en JSON, fichiers dans `public/uploads/` ou service externe
- Migration des données anciennes : garder un mapping old_id → new_id si besoin

## Tests obligatoires
- **Unitaires** : BlockFactory, BlockManager, services purs (pas de container)
- **Intégration** : Repository + DB (SQLite in-memory pour les tests)
- **Fonctionnels** : Controllers API (WebTestCase) — un test par endpoint CRUD
- **Front** : Tests composants Vue avec Vitest (pas obligatoire à chaque composant, mais obligatoire pour BlockRenderer et le Wizard)

## Avant chaque livrable
1. Proposer le plan de tests adapté à la feature
2. Écrire les tests AVANT ou EN MÊME TEMPS que le code (pas après)
3. Vérifier que `php bin/phpunit` passe avant de valider
4. Pour l'API : tester le JSON retourné (structure + status code), pas juste le 200

## Conventions de tests spécifiques
- Nommage : `test[Action]_[Condition]_[ExpectedResult]` (ex: `testCreateBlock_WithValidData`)
- Données : utiliser des fixtures ou des factories, jamais de données en dur dans les tests
- API : utiliser `static::createClient()` et vérifier le Content-Type `application/json`
- STI : tester que la factory retourne bien la bonne classe selon l'enum

## Sécurité	
- Ex: Tous les endpoints admin nécessitent ROLE_ADMIN via voter

## Performance	
- Pas de N+1 dans les requêtes, utiliser JOIN FETCH dans les Repository

## Objectif actuel
[Mettre à jour selon la phase en cours]
- Phase 1 : API Page + Block fonctionnelle
- Phase 2 : Factory + BlockManager + endpoints CRUD
- Phase 3 : Vue.js BlockRenderer + PageViewer
- Phase 4 : Admin in-page + Wizard 2 étapes