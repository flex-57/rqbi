# Design : Découpage de BlockContact en 3 blocs

**Date :** 2026-05-05
**Statut :** Approuvé

## Contexte

`BlockContact` est un composant monolithique qui regroupe trois responsabilités distinctes : des cartes d'informations de contact, une carte OpenStreetMap, et un formulaire de contact. Ce couplage rend l'édition rigide (impossible de placer la map seule, ou le formulaire sans les infos). L'objectif est de les séparer en blocs indépendants composables.

## Blocs résultants

### 1. `BlockCards` (extension)

Ajout d'un champ optionnel `link` par carte. Une carte avec `link` devient un `<a href>`, les autres restent identiques.

**Structure d'une carte :**
```json
{ "icon": "📞", "title": "Téléphone", "text": "03 87 88 39 85", "accent": "red", "link": "tel:0387883985" }
```

Aucun changement PHP (le type `cards` existe déjà). Mise à jour de `BlockCards.vue` et du `BlockEditor` uniquement.

### 2. `BlockMap` (nouveau)

Iframe OpenStreetMap centrée sur des coordonnées.

**Contenu JSON :**
```json
{ "title": "Nous trouver", "lat": 49.1654, "lon": 6.9427, "height": 400 }
```

**PHP :** `BlockType::MAP`, `BlockMap` entity, migration.
**Vue :** `BlockMap.vue`, entrée dans `index.ts`, éditeur dans `BlockEditor`.

### 3. `BlockForm` (nouveau)

Formulaire de contact avec les champs fixes : nom, email, sujet, message. Appel `POST /api/contact`.

**Contenu JSON :**
```json
{ "title": "Envoyer un message", "submit_label": "Envoyer" }
```

**PHP :** `BlockType::FORM`, `BlockForm` entity, migration.
**Vue :** `BlockForm.vue`, entrée dans `index.ts`, éditeur dans `BlockEditor`.

### 4. `BlockContact` (suppression)

- Enum case `CONTACT` retiré
- Entity `BlockContact` supprimée
- Migration de suppression de la colonne discriminator value
- `BlockContact.vue` supprimé
- Fixtures de la page Contact mises à jour pour utiliser les 3 blocs séparés

## Ordre de livraison

1. Extension `BlockCards` + `link`
2. `BlockMap`
3. `BlockForm`
4. Suppression `BlockContact` + fixtures

## Ce qui ne change pas

- `BlockRenderer.vue` — aucune modification (résolution dynamique par map)
- API — aucun nouvel endpoint (réutilise `/api/contact` existant)
- Structure STI Doctrine — pattern inchangé
