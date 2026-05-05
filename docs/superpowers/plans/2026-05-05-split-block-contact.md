# Split BlockContact Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Remplacer le bloc monolithique `BlockContact` par trois blocs indépendants : `BlockMap`, `BlockForm`, et `BlockCards` étendu avec un champ `link` optionnel.

**Architecture:** STI Doctrine — ajouter deux nouvelles entity classes + deux cases enum + mise à jour du discriminator map dans `Block.php`. Côté Vue, deux nouveaux composants bloc + extension de `BlockCards.vue`. `BlockContact` est supprimé via une migration de données puis retiré du code.

**Tech Stack:** PHP 8.3, Symfony 7.4, Doctrine ORM (STI), Vue 3, TypeScript, Vitest, PHPUnit

---

## Fichiers touchés

| Action   | Fichier |
|----------|---------|
| Créer    | `src/Entity/BlockMap.php` |
| Créer    | `src/Entity/BlockForm.php` |
| Modifier | `src/Entity/Block.php` — discriminator map |
| Modifier | `src/Enum/BlockType.php` |
| Modifier | `src/Factory/BlockFactory.php` |
| Supprimer | `src/Entity/BlockContact.php` |
| Créer    | `migrations/Version20260505120000.php` |
| Modifier | `src/DataFixtures/AppFixtures.php` |
| Modifier | `tests/Unit/BlockFactoryTest.php` |
| Modifier | `assets/vue/blocks/BlockCards.vue` |
| Créer    | `assets/vue/blocks/BlockMap.vue` |
| Créer    | `assets/vue/blocks/BlockForm.vue` |
| Supprimer | `assets/vue/blocks/BlockContact.vue` |
| Modifier | `assets/vue/blocks/index.ts` |
| Modifier | `assets/vue/components/BlockEditor.vue` |

---

## Task 1 : BlockMap — entité PHP + enum + factory (TDD)

**Files:**
- Create: `src/Entity/BlockMap.php`
- Modify: `src/Enum/BlockType.php`
- Modify: `src/Entity/Block.php`
- Modify: `src/Factory/BlockFactory.php`
- Modify: `tests/Unit/BlockFactoryTest.php`

- [ ] **Step 1 : Écrire le test qui échoue**

Dans `tests/Unit/BlockFactoryTest.php`, ajouter après le test `testCreate_GalleryType_ReturnsBlockGallery` :

```php
public function testCreate_MapType_ReturnsBlockMap(): void
{
    $block = $this->factory->create(BlockType::MAP);
    $this->assertInstanceOf(\App\Entity\BlockMap::class, $block);
    $this->assertSame(BlockType::MAP, $block->getType());
}
```

- [ ] **Step 2 : Lancer le test, vérifier qu'il échoue**

```bash
docker compose exec php php bin/phpunit tests/Unit/BlockFactoryTest.php::testCreate_MapType_ReturnsBlockMap --no-coverage
```

Attendu : erreur `BlockType::MAP` n'existe pas.

- [ ] **Step 3 : Créer `src/Entity/BlockMap.php`**

```php
<?php

namespace App\Entity;

use App\Enum\BlockType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class BlockMap extends Block
{
    public function getType(): BlockType
    {
        return BlockType::MAP;
    }
}
```

- [ ] **Step 4 : Ajouter uniquement `MAP` dans `src/Enum/BlockType.php`**

Ajouter après `case GALLERY`:
```php
case MAP          = 'map';
```

Ajouter dans `getClass()` :
```php
self::MAP          => \App\Entity\BlockMap::class,
```

Ajouter dans `getLabel()` :
```php
self::MAP          => 'Carte',
```

- [ ] **Step 5 : Mettre à jour `src/Entity/Block.php` — discriminator map**

Ajouter l'import :
```php
use App\Entity\BlockMap;
```

Ajouter dans `#[ORM\DiscriminatorMap(...)]` :
```php
'map'          => BlockMap::class,
```

- [ ] **Step 6 : Mettre à jour `src/Factory/BlockFactory.php`**

Ajouter l'import :
```php
use App\Entity\BlockMap;
```

Ajouter dans le `match` :
```php
BlockType::MAP          => new BlockMap(),
```

- [ ] **Step 7 : Lancer le test, vérifier qu'il passe**

```bash
docker compose exec php php bin/phpunit tests/Unit/BlockFactoryTest.php::testCreate_MapType_ReturnsBlockMap --no-coverage
```

Attendu : OK (1 test).

- [ ] **Step 8 : Commit**

```bash
git add src/Entity/BlockMap.php src/Enum/BlockType.php src/Entity/Block.php src/Factory/BlockFactory.php tests/Unit/BlockFactoryTest.php
git commit -m "feat: BlockMap entity + BlockType::MAP"
```

---

## Task 2 : BlockForm — entité PHP + enum + factory (TDD)

**Files:**
- Create: `src/Entity/BlockForm.php`
- Modify: `src/Enum/BlockType.php`
- Modify: `src/Entity/Block.php`
- Modify: `src/Factory/BlockFactory.php`
- Modify: `tests/Unit/BlockFactoryTest.php`

- [ ] **Step 1 : Écrire le test qui échoue**

Dans `tests/Unit/BlockFactoryTest.php` :

```php
public function testCreate_FormType_ReturnsBlockForm(): void
{
    $block = $this->factory->create(BlockType::FORM);
    $this->assertInstanceOf(\App\Entity\BlockForm::class, $block);
    $this->assertSame(BlockType::FORM, $block->getType());
}
```

- [ ] **Step 2 : Lancer le test, vérifier qu'il échoue**

```bash
docker compose exec php php bin/phpunit tests/Unit/BlockFactoryTest.php::testCreate_FormType_ReturnsBlockForm --no-coverage
```

Attendu : `BlockForm not found`.

- [ ] **Step 3 : Créer `src/Entity/BlockForm.php`**

```php
<?php

namespace App\Entity;

use App\Enum\BlockType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class BlockForm extends Block
{
    public function getType(): BlockType
    {
        return BlockType::FORM;
    }
}
```

- [ ] **Step 4 : Ajouter `FORM` dans `src/Enum/BlockType.php`**

Ajouter après `case MAP`:
```php
case FORM         = 'form';
```

Dans `getClass()` :
```php
self::FORM         => \App\Entity\BlockForm::class,
```

Dans `getLabel()` :
```php
self::FORM         => 'Formulaire de contact',
```

- [ ] **Step 5 : Mettre à jour `src/Entity/Block.php`**

Ajouter l'import :
```php
use App\Entity\BlockForm;
```

Ajouter dans le discriminator map :
```php
'form'         => BlockForm::class,
```

- [ ] **Step 6 : Mettre à jour `src/Factory/BlockFactory.php`**

Ajouter l'import :
```php
use App\Entity\BlockForm;
```

Ajouter dans le `match` :
```php
BlockType::FORM         => new BlockForm(),
```

- [ ] **Step 7 : Lancer le test, vérifier qu'il passe**

```bash
docker compose exec php php bin/phpunit tests/Unit/BlockFactoryTest.php::testCreate_FormType_ReturnsBlockForm --no-coverage
```

Attendu : OK (1 test).

- [ ] **Step 8 : Commit**

```bash
git add src/Entity/BlockForm.php src/Enum/BlockType.php src/Entity/Block.php src/Factory/BlockFactory.php tests/Unit/BlockFactoryTest.php
git commit -m "feat: BlockForm entity + BlockType::FORM"
```

---

## Task 3 : Migration + suppression de BlockContact

**Files:**
- Create: `migrations/Version20260505120000.php`
- Modify: `src/Entity/Block.php`
- Modify: `src/Enum/BlockType.php`
- Modify: `src/Factory/BlockFactory.php`
- Delete: `src/Entity/BlockContact.php`
- Modify: `tests/Unit/BlockFactoryTest.php`

- [ ] **Step 1 : Mettre à jour le test — retirer le test CONTACT, vérifier qu'il passe encore**

Dans `tests/Unit/BlockFactoryTest.php`, supprimer :

```php
public function testCreate_ContactType_ReturnsBlockContact(): void
{
    $block = $this->factory->create(BlockType::CONTACT);
    $this->assertInstanceOf(BlockContact::class, $block);
}
```

Et supprimer l'import `use App\Entity\BlockContact;` en haut du fichier.

- [ ] **Step 2 : Créer la migration de données**

Créer `migrations/Version20260505120000.php` :

```php
<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260505120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Supprime les blocs de type contact (remplacés par map + form + cards)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("DELETE FROM block WHERE type = 'contact'");
    }

    public function down(Schema $schema): void
    {
        // Irréversible : les données contact ne peuvent pas être reconstituées
    }
}
```

- [ ] **Step 3 : Appliquer la migration**

```bash
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
```

Attendu : `1 migration executed`.

- [ ] **Step 4 : Supprimer CONTACT de `src/Enum/BlockType.php`**

Retirer :
```php
case CONTACT      = 'contact';
```

Et dans `getClass()` :
```php
self::CONTACT      => \App\Entity\BlockContact::class,
```

Et dans `getLabel()` :
```php
self::CONTACT      => 'Contact & formulaire',
```

- [ ] **Step 5 : Mettre à jour `src/Entity/Block.php`**

Retirer l'import :
```php
use App\Entity\BlockContact;
```

Retirer du discriminator map :
```php
'contact'      => BlockContact::class,
```

- [ ] **Step 6 : Mettre à jour `src/Factory/BlockFactory.php`**

Retirer l'import :
```php
use App\Entity\BlockContact;
```

Retirer du `match` :
```php
BlockType::CONTACT      => new BlockContact(),
```

- [ ] **Step 7 : Supprimer `src/Entity/BlockContact.php`**

```bash
rm src/Entity/BlockContact.php
```

- [ ] **Step 8 : Lancer toute la suite de tests PHP**

```bash
docker compose exec php php bin/phpunit --no-coverage
```

Attendu : toute la suite passe (aucune référence à BlockContact restante).

- [ ] **Step 9 : Commit**

```bash
git add migrations/Version20260505120000.php src/Entity/Block.php src/Enum/BlockType.php src/Factory/BlockFactory.php tests/Unit/BlockFactoryTest.php
git rm src/Entity/BlockContact.php
git commit -m "feat: migrate + remove BlockContact type (replaced by map/form/cards)"
```

---

## Task 4 : Vue BlockCards — champ `link` optionnel

**Files:**
- Modify: `assets/vue/blocks/BlockCards.vue`
- Modify: `assets/vue/components/BlockEditor.vue`

- [ ] **Step 1 : Mettre à jour `assets/vue/blocks/BlockCards.vue`**

Remplacer le `<div>` de carte :

```vue
<template>
  <section class="py-20">
    <div class="container-rqbi">
      <h2 v-if="block.content.title" class="text-rqbi-ink mb-12 max-w-3xl" v-animate-in>
        {{ block.content.title }}
      </h2>
      <div class="grid gap-5" :class="gridClass">
        <component
          :is="card.link ? 'a' : 'div'"
          v-for="(card, i) in cards" :key="i"
          v-animate-in="'scale'"
          class="relative bg-white rounded-2xl border border-rqbi-line p-8 transition-all duration-500 hover:-translate-y-1.5 hover:shadow-rqbi-lg overflow-hidden group"
          :href="card.link || undefined"
        >
          <span
            class="absolute top-0 left-0 right-0 h-[3px] origin-left scale-x-[0.3] group-hover:scale-x-100 transition-transform duration-500"
            :class="accentBg(card.accent)"
          />
          <span v-if="card.icon" class="block text-3xl mb-4">{{ card.icon }}</span>
          <span class="font-mono text-xs tracking-widest uppercase mb-3 block" :class="accentText(card.accent)">
            {{ String(i + 1).padStart(2, '0') }}
          </span>
          <h3 class="font-display text-2xl font-medium mb-2 text-rqbi-ink">{{ card.title }}</h3>
          <p class="text-rqbi-ink-mute text-sm leading-relaxed">{{ card.text }}</p>
        </component>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { Block } from '../stores/pages'

const props = defineProps<{ block: Block; isEditing: boolean }>()
const cards = computed(() => (props.block.content.cards as any[]) ?? [])
const columns = computed(() => Number(props.block.content.columns) || 3)
const gridClass = computed(() => ({
  2: 'grid-cols-1 sm:grid-cols-2',
  3: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
  4: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
}[columns.value] ?? 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'))

function accentBg(a?: string) {
  return a === 'blue' ? 'bg-rqbi-blue' : a === 'dark' ? 'bg-rqbi-ink' : 'bg-rqbi-red'
}
function accentText(a?: string) {
  return a === 'blue' ? 'text-rqbi-blue' : a === 'dark' ? 'text-rqbi-ink' : 'text-rqbi-red'
}
</script>
```

- [ ] **Step 2 : Ajouter le champ `link` dans l'éditeur de cartes (`BlockEditor.vue`)**

Dans la section `<!-- CARDS -->` de `BlockEditor.vue` (autour de la ligne 252), après le `<textarea>` de description de chaque carte, ajouter :

```vue
<input v-model="card.link" type="text" class="form-input" placeholder="Lien (optionnel, ex: tel:0387... ou mailto:...)" />
```

Mettre à jour `addCard()` dans le `<script>` pour inclure `link: ''` :

```ts
function addCard() {
  const cards = (form.content.cards as any[]) ?? []
  form.content.cards = [...cards, { icon: '', title: '', text: '', accent: '', link: '' }]
}
```

- [ ] **Step 3 : Vérifier dans le navigateur**

Ouvrir http://localhost:8000, aller en mode admin, éditer un bloc Cartes, vérifier que le champ "Lien" apparaît sur chaque carte.

- [ ] **Step 4 : Commit**

```bash
git add assets/vue/blocks/BlockCards.vue assets/vue/components/BlockEditor.vue
git commit -m "feat: BlockCards — champ link optionnel pour cartes cliquables"
```

---

## Task 5 : Vue BlockMap

**Files:**
- Create: `assets/vue/blocks/BlockMap.vue`
- Modify: `assets/vue/blocks/index.ts`
- Modify: `assets/vue/components/BlockEditor.vue`

- [ ] **Step 1 : Créer `assets/vue/blocks/BlockMap.vue`**

```vue
<template>
  <section class="py-12">
    <div class="container-rqbi">
      <h2 v-if="block.content.title" class="text-rqbi-ink mb-6" v-animate-in>
        {{ block.content.title }}
      </h2>
      <div
        class="rounded-2xl overflow-hidden border border-rqbi-line"
        :style="{ height: (Number(block.content.height) || 400) + 'px' }"
        v-animate-in
      >
        <iframe
          title="Carte"
          width="100%"
          height="100%"
          frameborder="0"
          scrolling="no"
          :src="mapUrl"
        />
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { Block } from '../stores/pages'

const props = defineProps<{ block: Block; isEditing: boolean }>()

const mapUrl = computed(() => {
  const lat = Number(props.block.content.lat)
  const lon = Number(props.block.content.lon)
  const dLat = 0.005, dLon = 0.01
  return `https://www.openstreetmap.org/export/embed.html?bbox=${lon - dLon}%2C${lat - dLat}%2C${lon + dLon}%2C${lat + dLat}&layer=mapnik&marker=${lat}%2C${lon}`
})
</script>
```

- [ ] **Step 2 : Ajouter dans `assets/vue/blocks/index.ts`**

```ts
import BlockMap from './BlockMap.vue'
// dans blockMap :
map: BlockMap,
```

- [ ] **Step 3 : Ajouter dans `BlockEditor.vue` — liste des types**

Dans `blockTypes` (autour de la ligne 414), remplacer l'entrée `contact` par :
```ts
{ value: 'map',  label: 'Carte' },
{ value: 'form', label: 'Formulaire de contact' },
```

- [ ] **Step 4 : Ajouter dans `BlockEditor.vue` — `resetContent`**

Dans `resetContent()` (autour de la ligne 470), remplacer l'entrée `contact` par :
```ts
map:          { title: '', lat: '49.1654', lon: '6.9427', height: 400 },
form:         { title: 'Envoyer un message', submit_label: 'Envoyer' },
```

- [ ] **Step 5 : Ajouter dans `BlockEditor.vue` — `typeIcon`**

Dans `typeIcon()` (autour de la ligne 456), remplacer `contact: '📍'` par :
```ts
map: '🗺️', form: '✉️',
```

- [ ] **Step 6 : Ajouter dans `BlockEditor.vue` — template MAP**

Remplacer le bloc `<!-- CONTACT -->` par :

```vue
<!-- MAP -->
<template v-else-if="form.type === 'map'">
  <div>
    <label class="form-label">Titre (optionnel)</label>
    <input v-model="form.content.title" type="text" class="form-input" placeholder="Nous trouver" />
  </div>
  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="form-label">Latitude <span class="text-red-500">*</span></label>
      <input v-model="form.content.lat" type="text" class="form-input" placeholder="49.1654" />
    </div>
    <div>
      <label class="form-label">Longitude <span class="text-red-500">*</span></label>
      <input v-model="form.content.lon" type="text" class="form-input" placeholder="6.9427" />
    </div>
  </div>
  <div>
    <label class="form-label">Hauteur (px)</label>
    <input v-model="form.content.height" type="number" class="form-input" placeholder="400" />
  </div>
</template>
```

- [ ] **Step 7 : Vérifier dans le navigateur**

En mode admin, ajouter un nouveau bloc → sélectionner "Carte" → renseigner lat/lon → enregistrer → vérifier que la map OpenStreetMap s'affiche.

- [ ] **Step 8 : Commit**

```bash
git add assets/vue/blocks/BlockMap.vue assets/vue/blocks/index.ts assets/vue/components/BlockEditor.vue
git commit -m "feat: BlockMap — carte OpenStreetMap indépendante"
```

---

## Task 6 : Vue BlockForm

**Files:**
- Create: `assets/vue/blocks/BlockForm.vue`
- Modify: `assets/vue/blocks/index.ts`
- Modify: `assets/vue/components/BlockEditor.vue`

- [ ] **Step 1 : Créer `assets/vue/blocks/BlockForm.vue`**

```vue
<template>
  <section class="py-12">
    <div class="container-rqbi max-w-2xl">
      <div v-if="sent" class="text-center py-8">
        <div class="w-16 h-16 rounded-full bg-rqbi-red-soft text-rqbi-red mx-auto mb-6 flex items-center justify-center text-2xl">✓</div>
        <h3 class="font-display text-2xl font-medium mb-2">Message envoyé !</h3>
        <p class="text-rqbi-ink-mute">Nous vous répondrons dans les meilleurs délais.</p>
      </div>

      <form v-else class="bg-white border border-rqbi-line rounded-2xl p-10 space-y-4" @submit.prevent="submit" v-animate-in>
        <h2 v-if="block.content.title" class="font-display text-2xl font-medium mb-6">{{ block.content.title }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="form-label">Nom *</label>
            <input v-model="form.name" type="text" required class="form-input" />
          </div>
          <div>
            <label class="form-label">Email *</label>
            <input v-model="form.email" type="email" required class="form-input" />
          </div>
        </div>
        <div>
          <label class="form-label">Sujet *</label>
          <input v-model="form.subject" type="text" required class="form-input" />
        </div>
        <div>
          <label class="form-label">Message *</label>
          <textarea v-model="form.message" required class="form-textarea" />
        </div>
        <p v-if="error" class="text-rqbi-red text-sm">{{ error }}</p>
        <button
          type="submit"
          :disabled="sending || isEditing"
          class="btn-primary w-full justify-center"
        >
          {{ sending ? 'Envoi en cours…' : String(block.content.submit_label || 'Envoyer') }}
        </button>
      </form>
    </div>
  </section>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'
import type { Block } from '../stores/pages'
import api from '../composables/api'

const props = defineProps<{ block: Block; isEditing: boolean }>()

const form = reactive({ name: '', email: '', subject: '', message: '' })
const sending = ref(false)
const sent = ref(false)
const error = ref('')

async function submit() {
  sending.value = true
  error.value = ''
  try {
    await api.post('/api/contact', form)
    sent.value = true
  } catch {
    error.value = "Une erreur s'est produite. Veuillez réessayer."
  } finally {
    sending.value = false
  }
}
</script>
```

- [ ] **Step 2 : Ajouter dans `assets/vue/blocks/index.ts`**

```ts
import BlockForm from './BlockForm.vue'
// dans blockMap :
form: BlockForm,
```

- [ ] **Step 3 : Ajouter le template FORM dans `BlockEditor.vue`**

Après le bloc `<!-- MAP -->` :

```vue
<!-- FORM -->
<template v-else-if="form.type === 'form'">
  <div>
    <label class="form-label">Titre</label>
    <input v-model="form.content.title" type="text" class="form-input" placeholder="Envoyer un message" />
  </div>
  <div>
    <label class="form-label">Texte du bouton</label>
    <input v-model="form.content.submit_label" type="text" class="form-input" placeholder="Envoyer" />
  </div>
  <p class="text-sm text-gray-500">Les champs du formulaire (nom, email, sujet, message) sont fixes.</p>
</template>
```

- [ ] **Step 4 : Vérifier dans le navigateur**

En mode admin, ajouter un bloc "Formulaire de contact" → enregistrer → vérifier que le formulaire s'affiche. En mode visiteur, soumettre le formulaire → vérifier la confirmation.

- [ ] **Step 5 : Commit**

```bash
git add assets/vue/blocks/BlockForm.vue assets/vue/blocks/index.ts assets/vue/components/BlockEditor.vue
git commit -m "feat: BlockForm — formulaire de contact indépendant"
```

---

## Task 7 : Suppression de BlockContact côté Vue

**Files:**
- Delete: `assets/vue/blocks/BlockContact.vue`
- Modify: `assets/vue/blocks/index.ts`

- [ ] **Step 1 : Supprimer `assets/vue/blocks/BlockContact.vue`**

```bash
rm assets/vue/blocks/BlockContact.vue
```

- [ ] **Step 2 : Retirer de `assets/vue/blocks/index.ts`**

Supprimer :
```ts
import BlockContact from './BlockContact.vue'
// et dans blockMap :
contact: BlockContact,
```

- [ ] **Step 3 : Lancer le build TypeScript pour vérifier**

```bash
docker compose exec node npm run build 2>&1 | tail -20
```

Attendu : aucune erreur TypeScript liée à BlockContact.

- [ ] **Step 4 : Commit**

```bash
git rm assets/vue/blocks/BlockContact.vue
git add assets/vue/blocks/index.ts
git commit -m "chore: remove BlockContact Vue component (replaced by BlockMap + BlockForm)"
```

---

## Task 8 : Mise à jour des fixtures

**Files:**
- Modify: `src/DataFixtures/AppFixtures.php`

- [ ] **Step 1 : Mettre à jour `loadContact()` dans `AppFixtures.php`**

Remplacer la méthode `loadContact` par :

```php
private function loadContact(ObjectManager $manager): void
{
    $page = $this->page($manager, 'Contact', 'contact');

    $this->block($manager, $page, BlockType::CARDS, 1, [
        'columns' => 2,
        'cards'   => [
            ['icon' => '📍', 'title' => 'Adresse',   'text' => "Annexe Chateaubriand\n1 rue de l'École\n57460 Behren-lès-Forbach", 'accent' => 'red'],
            ['icon' => '📞', 'title' => 'Téléphone', 'text' => '03 87 88 39 85', 'accent' => 'blue', 'link' => 'tel:0387883985'],
            ['icon' => '✉️', 'title' => 'Email',      'text' => 'secretariat@rqbi.fr', 'accent' => 'red', 'link' => 'mailto:secretariat@rqbi.fr'],
            ['icon' => '🕐', 'title' => 'Horaires',   'text' => "Lun – Ven : 8h00 – 12h00\net 14h00 – 17h00", 'accent' => 'blue'],
        ],
    ]);

    $this->block($manager, $page, BlockType::MAP, 2, [
        'title'  => 'Nous trouver',
        'lat'    => 49.1654,
        'lon'    => 6.9427,
        'height' => 400,
    ]);

    $this->block($manager, $page, BlockType::FORM, 3, [
        'title'        => 'Envoyer un message',
        'submit_label' => 'Envoyer',
    ]);
}
```

- [ ] **Step 2 : Recharger les fixtures**

```bash
docker compose exec php php bin/console doctrine:fixtures:load --no-interaction
```

Attendu : `loading App\DataFixtures\AppFixtures` sans erreur.

- [ ] **Step 3 : Vérifier dans le navigateur**

Ouvrir http://localhost:8000/contact — vérifier que les 3 blocs s'affichent (cartes, map, formulaire).

- [ ] **Step 4 : Commit**

```bash
git add src/DataFixtures/AppFixtures.php
git commit -m "feat: fixtures contact page — 3 blocs séparés (cards + map + form)"
```

---

## Task 9 : Tests PHP — suite complète

**Files:**
- Modify: `tests/Unit/BlockFactoryTest.php`

- [ ] **Step 1 : Lancer toute la suite**

```bash
docker compose exec php php bin/phpunit --no-coverage
```

Attendu : toute la suite passe. Vérifier notamment que `testCreate_AllTypes_ReturnCorrectType` (data provider sur `BlockType::cases()`) couvre MAP et FORM et ne contient plus CONTACT.

- [ ] **Step 2 : Commit final si des ajustements ont été nécessaires**

```bash
git add tests/
git commit -m "test: suite complète PHP après split BlockContact"
```
