<template>
  <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
      <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <h2 class="text-xl font-bold text-rqbi-dark">
          {{ isNew ? 'Ajouter un bloc' : 'Modifier le bloc' }}
        </h2>
        <button class="text-gray-400 hover:text-gray-600 text-2xl leading-none" @click="$emit('close')">×</button>
      </div>

      <!-- Sélection du type (ajout uniquement) -->
      <div v-if="isNew" class="p-6 border-b">
        <label class="block text-sm font-semibold text-gray-700 mb-3">Type de bloc</label>
        <div class="grid grid-cols-3 gap-3">
          <button
            v-for="t in blockTypes"
            :key="t.value"
            class="p-3 border-2 rounded-lg text-center text-sm font-medium transition-colors"
            :class="form.type === t.value
              ? 'border-rqbi-blue bg-blue-50 text-rqbi-blue'
              : 'border-gray-200 hover:border-gray-300'"
            @click="form.type = t.value; resetContent(); clearErrors()"
          >
            {{ typeIcon(t.value) }} {{ t.label }}
          </button>
        </div>
      </div>

      <!-- Formulaire dynamique selon le type -->
      <div class="p-6 space-y-4">
        <!-- TEXT -->
        <template v-if="form.type === 'text'">
          <div>
            <label class="form-label">Titre (optionnel)</label>
            <input v-model="form.content.title" type="text" class="form-input" placeholder="Titre du bloc" />
          </div>
          <div>
            <label class="form-label">Contenu <span class="text-red-500">*</span></label>
            <RichTextEditor
              v-model="form.content.body as string"
              :has-error="!!errors.body"
            />
            <p v-if="errors.body" class="text-red-500 text-xs mt-1">{{ errors.body }}</p>
          </div>
        </template>

        <!-- IMAGE -->
        <template v-else-if="form.type === 'image'">
          <div>
            <label class="form-label">Image <span class="text-red-500">*</span></label>
            <div class="flex gap-2">
              <input
                v-model="form.content.url"
                type="text"
                class="form-input flex-1"
                :class="{ 'border-red-400': errors.url }"
                placeholder="URL ou chemin"
              />
              <label class="btn-secondary cursor-pointer text-sm px-3 py-2 whitespace-nowrap">
                Parcourir
                <input type="file" accept="image/*" class="hidden" @change="handleUpload" />
              </label>
            </div>
            <p v-if="errors.url" class="text-red-500 text-xs mt-1">{{ errors.url }}</p>
            <img v-if="form.content.url" :src="form.content.url as string" class="mt-2 h-32 object-cover rounded" />
          </div>
          <div>
            <label class="form-label">Texte alternatif <span class="text-red-500">*</span></label>
            <input
              v-model="form.content.alt"
              type="text"
              class="form-input"
              :class="{ 'border-red-400': errors.alt }"
              placeholder="Description de l'image"
            />
            <p v-if="errors.alt" class="text-red-500 text-xs mt-1">{{ errors.alt }}</p>
          </div>
          <div>
            <label class="form-label">Légende (optionnelle)</label>
            <input v-model="form.content.caption" type="text" class="form-input" placeholder="Légende sous l'image" />
          </div>
          <div>
            <label class="form-label">Texte associé (optionnel)</label>
            <textarea v-model="form.content.text" rows="3" class="form-input resize-none" placeholder="Texte à afficher autour de l'image" />
          </div>
          <div v-if="form.content.text">
            <label class="form-label">Position du texte</label>
            <div class="flex gap-4">
              <label v-for="opt in textPositions" :key="opt.value" class="flex items-center gap-1 text-sm cursor-pointer">
                <input v-model="form.content.text_position" type="radio" :value="opt.value" />
                {{ opt.label }}
              </label>
            </div>
          </div>
        </template>

        <!-- SLIDER -->
        <template v-else-if="form.type === 'slider'">
          <p v-if="errors.slides" class="text-red-500 text-xs">{{ errors.slides }}</p>
          <div v-for="(slide, i) in (form.content.slides as any[])" :key="i" class="border border-gray-200 rounded-lg p-4 space-y-2">
            <div class="flex justify-between items-center">
              <span class="text-sm font-medium text-gray-600">Diapositive {{ i + 1 }}</span>
              <button class="text-red-400 hover:text-red-600 text-sm" @click="removeSlide(i)">Supprimer</button>
            </div>
            <div class="flex gap-2">
              <input v-model="slide.url" type="text" class="form-input flex-1" placeholder="URL de l'image" />
              <label class="btn-secondary cursor-pointer text-sm px-3 py-2">
                +
                <input type="file" accept="image/*" class="hidden" @change="(e) => handleSlideUpload(e, i)" />
              </label>
            </div>
            <img v-if="slide.url" :src="slide.url" class="h-24 object-cover rounded" />
            <input v-model="slide.alt" type="text" class="form-input" placeholder="Texte alternatif" />
            <input v-model="slide.caption" type="text" class="form-input" placeholder="Légende (optionnelle)" />
          </div>
          <button class="btn-ghost text-sm w-full" @click="addSlide">+ Ajouter une diapositive</button>
          <label class="flex items-center gap-2 text-sm">
            <input v-model="form.content.autoplay" type="checkbox" class="rounded" />
            Lecture automatique
          </label>
        </template>

        <!-- VIDEO -->
        <template v-else-if="form.type === 'video'">
          <div>
            <label class="form-label">URL de la vidéo <span class="text-red-500">*</span></label>
            <input
              v-model="form.content.url"
              type="text"
              class="form-input"
              :class="{ 'border-red-400': errors.url }"
              placeholder="https://youtube.com/watch?v=..."
            />
            <p class="text-xs text-gray-500 mt-1">YouTube ou Vimeo</p>
            <p v-if="errors.url" class="text-red-500 text-xs mt-1">{{ errors.url }}</p>
          </div>
          <div>
            <label class="form-label">Titre (optionnel)</label>
            <input v-model="form.content.title" type="text" class="form-input" placeholder="Titre de la vidéo" />
          </div>
          <div>
            <label class="form-label">Plateforme</label>
            <select v-model="form.content.provider" class="form-input">
              <option value="youtube">YouTube</option>
              <option value="vimeo">Vimeo</option>
            </select>
          </div>
        </template>

        <!-- CTA -->
        <template v-else-if="form.type === 'cta'">
          <div>
            <label class="form-label">Titre <span class="text-red-500">*</span></label>
            <input
              v-model="form.content.title"
              type="text"
              class="form-input"
              :class="{ 'border-red-400': errors.title }"
              placeholder="Titre principal"
            />
            <p v-if="errors.title" class="text-red-500 text-xs mt-1">{{ errors.title }}</p>
          </div>
          <div>
            <label class="form-label">Sous-titre</label>
            <input v-model="form.content.subtitle" type="text" class="form-input" placeholder="Texte descriptif" />
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="form-label">Texte du bouton <span class="text-red-500">*</span></label>
              <input
                v-model="form.content.button_label"
                type="text"
                class="form-input"
                :class="{ 'border-red-400': errors.button_label }"
                placeholder="Nous contacter"
              />
              <p v-if="errors.button_label" class="text-red-500 text-xs mt-1">{{ errors.button_label }}</p>
            </div>
            <div>
              <label class="form-label">URL du bouton <span class="text-red-500">*</span></label>
              <input
                v-model="form.content.button_url"
                type="text"
                class="form-input"
                :class="{ 'border-red-400': errors.button_url }"
                placeholder="/contact"
              />
              <p v-if="errors.button_url" class="text-red-500 text-xs mt-1">{{ errors.button_url }}</p>
            </div>
          </div>
          <div>
            <label class="form-label">Arrière-plan</label>
            <div class="flex gap-3">
              <label v-for="bg in bgOptions" :key="bg.value" class="flex items-center gap-2 cursor-pointer">
                <input v-model="form.content.background" type="radio" :value="bg.value" />
                <span class="w-5 h-5 rounded" :class="bg.class" />
                {{ bg.label }}
              </label>
            </div>
          </div>
        </template>

        <!-- DIVIDER -->
        <template v-else-if="form.type === 'divider'">
          <div>
            <label class="form-label">Texte central (optionnel)</label>
            <input v-model="form.content.label" type="text" class="form-input" placeholder="ex: Nos valeurs" />
          </div>
        </template>

        <!-- STATS -->
        <template v-else-if="form.type === 'stats'">
          <div>
            <label class="form-label">Titre (optionnel)</label>
            <input v-model="form.content.title" type="text" class="form-input" placeholder="Chiffres clés" />
          </div>
          <p v-if="errors.stats" class="text-red-500 text-xs">{{ errors.stats }}</p>
          <div v-for="(stat, i) in (form.content.stats as any[])" :key="i" class="border border-gray-200 rounded-lg p-3 space-y-2">
            <div class="flex justify-between items-center">
              <span class="text-sm font-medium text-gray-600">Stat {{ i + 1 }}</span>
              <button class="text-red-400 hover:text-red-600 text-sm" @click="removeStat(i)">Supprimer</button>
            </div>
            <div class="grid grid-cols-2 gap-2">
              <input v-model="stat.value" type="text" class="form-input" placeholder="Valeur (ex: 250)" />
              <input v-model="stat.label" type="text" class="form-input" placeholder="Libellé (ex: bénéficiaires)" />
            </div>
            <div class="flex gap-4">
              <label v-for="c in statColors" :key="c.value" class="flex items-center gap-1 text-sm cursor-pointer">
                <input v-model="stat.color" type="radio" :value="c.value" />
                {{ c.label }}
              </label>
            </div>
          </div>
          <button class="btn-ghost text-sm w-full" @click="addStat">+ Ajouter une statistique</button>
        </template>

        <!-- CARDS -->
        <template v-else-if="form.type === 'cards'">
          <div>
            <label class="form-label">Titre (optionnel)</label>
            <input v-model="form.content.title" type="text" class="form-input" placeholder="Titre de la section" />
          </div>
          <div>
            <label class="form-label">Nombre de colonnes</label>
            <select v-model="form.content.columns" class="form-input">
              <option :value="2">2 colonnes</option>
              <option :value="3">3 colonnes</option>
              <option :value="4">4 colonnes</option>
            </select>
          </div>
          <p v-if="errors.cards" class="text-red-500 text-xs">{{ errors.cards }}</p>
          <div v-for="(card, i) in (form.content.cards as any[])" :key="i" class="border border-gray-200 rounded-lg p-3 space-y-2">
            <div class="flex justify-between items-center">
              <span class="text-sm font-medium text-gray-600">Carte {{ i + 1 }}</span>
              <button class="text-red-400 hover:text-red-600 text-sm" @click="removeCard(i)">Supprimer</button>
            </div>
            <div class="grid grid-cols-2 gap-2">
              <input v-model="card.icon" type="text" class="form-input" placeholder="Icône (emoji)" />
              <input v-model="card.title" type="text" class="form-input" placeholder="Titre" />
            </div>
            <textarea v-model="card.text" rows="2" class="form-input resize-none" placeholder="Description" />
            <div class="flex gap-4">
              <label v-for="a in accentOptions" :key="a.value" class="flex items-center gap-1 text-sm cursor-pointer">
                <input v-model="card.accent" type="radio" :value="a.value" />
                {{ a.label }}
              </label>
            </div>
          </div>
          <button class="btn-ghost text-sm w-full" @click="addCard">+ Ajouter une carte</button>
        </template>

        <!-- TIMELINE -->
        <template v-else-if="form.type === 'timeline'">
          <div>
            <label class="form-label">Titre (optionnel)</label>
            <input v-model="form.content.title" type="text" class="form-input" placeholder="Notre histoire" />
          </div>
          <p v-if="errors.events" class="text-red-500 text-xs">{{ errors.events }}</p>
          <div v-for="(event, i) in (form.content.events as any[])" :key="i" class="border border-gray-200 rounded-lg p-3 space-y-2">
            <div class="flex justify-between items-center">
              <span class="text-sm font-medium text-gray-600">Événement {{ i + 1 }}</span>
              <button class="text-red-400 hover:text-red-600 text-sm" @click="removeEvent(i)">Supprimer</button>
            </div>
            <div class="grid grid-cols-3 gap-2">
              <input v-model="event.year" type="text" class="form-input" placeholder="Année" />
              <textarea v-model="event.text" rows="2" class="form-input resize-none col-span-2" placeholder="Description" />
            </div>
          </div>
          <button class="btn-ghost text-sm w-full" @click="addEvent">+ Ajouter un événement</button>
        </template>

        <!-- CONTACT -->
        <template v-else-if="form.type === 'contact'">
          <div>
            <label class="form-label">Titre (optionnel)</label>
            <input v-model="form.content.title" type="text" class="form-input" placeholder="Nous contacter" />
          </div>
          <div>
            <label class="form-label">Adresse</label>
            <textarea v-model="form.content.address" rows="2" class="form-input resize-none" placeholder="1 rue de l'École&#10;57460 Behren-lès-Forbach" />
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="form-label">Téléphone</label>
              <input v-model="form.content.phone" type="text" class="form-input" placeholder="03 87 88 39 85" />
            </div>
            <div>
              <label class="form-label">Email</label>
              <input v-model="form.content.email" type="email" class="form-input" placeholder="contact@rqbi.fr" />
            </div>
          </div>
          <div>
            <label class="form-label">Horaires</label>
            <textarea v-model="form.content.hours" rows="2" class="form-input resize-none" placeholder="Lun – Ven : 8h–12h et 13h–16h" />
          </div>
          <label class="flex items-center gap-2 text-sm">
            <input v-model="form.content.show_map" type="checkbox" class="rounded" />
            Afficher la carte
          </label>
          <template v-if="form.content.show_map">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="form-label">Latitude</label>
                <input v-model="form.content.lat" type="text" class="form-input" placeholder="49.1654" />
              </div>
              <div>
                <label class="form-label">Longitude</label>
                <input v-model="form.content.lon" type="text" class="form-input" placeholder="6.9427" />
              </div>
            </div>
          </template>
        </template>

        <!-- FAQ -->
        <template v-else-if="form.type === 'faq'">
          <div>
            <label class="form-label">Titre (optionnel)</label>
            <input v-model="form.content.title" type="text" class="form-input" placeholder="Questions fréquentes" />
          </div>
          <p v-if="errors.items" class="text-red-500 text-xs">{{ errors.items }}</p>
          <div v-for="(item, i) in (form.content.items as any[])" :key="i" class="border border-gray-200 rounded-lg p-3 space-y-2">
            <div class="flex justify-between items-center">
              <span class="text-sm font-medium text-gray-600">Question {{ i + 1 }}</span>
              <button class="text-red-400 hover:text-red-600 text-sm" @click="removeFaqItem(i)">Supprimer</button>
            </div>
            <input v-model="item.question" type="text" class="form-input" placeholder="Question" />
            <textarea v-model="item.answer" rows="3" class="form-input resize-none" placeholder="Réponse" />
          </div>
          <button class="btn-ghost text-sm w-full" @click="addFaqItem">+ Ajouter une question</button>
        </template>

        <!-- GALLERY -->
        <template v-else-if="form.type === 'gallery'">
          <div>
            <label class="form-label">Titre (optionnel)</label>
            <input v-model="form.content.title" type="text" class="form-input" placeholder="Notre galerie" />
          </div>
          <div>
            <label class="form-label">Nombre de colonnes</label>
            <select v-model="form.content.columns" class="form-input">
              <option :value="2">2 colonnes</option>
              <option :value="3">3 colonnes</option>
              <option :value="4">4 colonnes</option>
            </select>
          </div>
          <p v-if="errors.gallery_items" class="text-red-500 text-xs">{{ errors.gallery_items }}</p>
          <div v-for="(item, i) in (form.content.items as any[])" :key="i" class="border border-gray-200 rounded-lg p-3 space-y-2">
            <div class="flex justify-between items-center">
              <span class="text-sm font-medium text-gray-600">Image {{ i + 1 }}</span>
              <button class="text-red-400 hover:text-red-600 text-sm" @click="removeGalleryItem(i)">Supprimer</button>
            </div>
            <div class="flex gap-2">
              <input v-model="item.url" type="text" class="form-input flex-1" placeholder="URL de l'image" />
              <label class="btn-secondary cursor-pointer text-sm px-3 py-2">
                +
                <input type="file" accept="image/*" class="hidden" @change="(e) => handleGalleryUpload(e, i)" />
              </label>
            </div>
            <img v-if="item.url" :src="item.url" class="h-24 object-cover rounded" />
            <input v-model="item.alt" type="text" class="form-input" placeholder="Description (optionnelle)" />
          </div>
          <button class="btn-ghost text-sm w-full" @click="addGalleryItem">+ Ajouter une image</button>
        </template>
      </div>

      <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
        <button class="btn-ghost" @click="$emit('close')">Annuler</button>
        <button class="btn-primary" :disabled="saving" @click="save">
          {{ saving ? 'Enregistrement...' : 'Enregistrer' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'
import type { Block } from '../stores/pages'
import { useBlocksStore } from '../stores/blocks'
import RichTextEditor from './RichTextEditor.vue'

const props = defineProps<{ block: Block | null; pageId: number }>()
const emit = defineEmits<{ close: [] }>()

const blocksStore = useBlocksStore()
const saving = ref(false)
const isNew = !props.block
const errors = reactive<Record<string, string>>({})

function clearErrors() {
  Object.keys(errors).forEach(k => delete errors[k])
}

const blockTypes = [
  { value: 'text',         label: 'Texte' },
  { value: 'image',        label: 'Image' },
  { value: 'slider',       label: 'Slider' },
  { value: 'video',        label: 'Vidéo' },
  { value: 'cta',          label: "Appel à l'action" },
  { value: 'divider',      label: 'Séparateur' },
  { value: 'stats',        label: 'Statistiques' },
  { value: 'cards',        label: 'Cartes' },
  { value: 'timeline',     label: 'Chronologie' },
  { value: 'contact',      label: 'Contact & formulaire' },
  { value: 'faq',          label: 'FAQ' },
  { value: 'gallery',      label: 'Galerie' },
]

const textPositions = [
  { value: 'top',    label: 'Au-dessus' },
  { value: 'bottom', label: 'En-dessous' },
  { value: 'left',   label: 'À gauche' },
  { value: 'right',  label: 'À droite' },
]

const bgOptions = [
  { value: 'red',   label: 'Rouge',  class: 'bg-rqbi-red' },
  { value: 'blue',  label: 'Bleu',   class: 'bg-rqbi-blue' },
  { value: 'dark',  label: 'Sombre', class: 'bg-rqbi-dark' },
  { value: 'light', label: 'Clair',  class: 'bg-gray-100 border border-gray-300' },
]

const statColors = [
  { value: 'red',  label: 'Rouge' },
  { value: 'blue', label: 'Bleu' },
  { value: 'dark', label: 'Blanc' },
]

const accentOptions = [
  { value: '',     label: 'Aucun' },
  { value: 'red',  label: 'Rouge' },
  { value: 'blue', label: 'Bleu' },
  { value: 'dark', label: 'Sombre' },
]

function typeIcon(type: string): string {
  const icons: Record<string, string> = {
    text: '📝', image: '🖼️', slider: '🎠', video: '🎬', cta: '📣', divider: '〰️',
    stats: '📊', cards: '🗂️', timeline: '📅', contact: '📍', faq: '❓', gallery: '🖼️',
  }
  return icons[type] ?? '📦'
}

const form = reactive({
  type: props.block?.type ?? 'text',
  content: { ...(props.block?.content ?? {}) } as Record<string, unknown>,
})

function resetContent() {
  const defaults: Record<string, Record<string, unknown>> = {
    text:         { title: '', body: '' },
    image:        { url: '', alt: '', caption: '', text: '', text_position: 'bottom' },
    slider:       { slides: [], autoplay: true, interval: 4000 },
    video:        { url: '', title: '', provider: 'youtube' },
    cta:          { title: '', subtitle: '', button_label: '', button_url: '', background: 'red' },
    divider:      { style: 'line', label: '' },
    stats:        { title: '', stats: [] },
    cards:        { title: '', columns: 3, cards: [] },
    timeline:     { title: '', events: [] },
    contact:      { title: '', address: '', phone: '', email: '', hours: '', show_map: false, lat: '', lon: '' },
    faq:          { title: 'Questions fréquentes', items: [] },
    gallery:      { title: '', columns: 3, items: [] },
  }
  form.content = { ...defaults[form.type] }
}

if (isNew) resetContent()

function validate(): boolean {
  clearErrors()
  const c = form.content

  if (form.type === 'text') {
    if (!String(c.body ?? '').trim()) errors.body = 'Le contenu est obligatoire.'
  }

  if (form.type === 'image') {
    if (!String(c.url ?? '').trim()) errors.url = "L'image est obligatoire."
    if (!String(c.alt ?? '').trim()) errors.alt = 'Le texte alternatif est obligatoire.'
  }

  if (form.type === 'slider') {
    const slides = (c.slides as any[]) ?? []
    if (slides.length === 0) errors.slides = 'Ajoutez au moins une diapositive.'
    else if (slides.some((s: any) => !s.url?.trim())) errors.slides = 'Toutes les diapositives doivent avoir une image.'
  }

  if (form.type === 'video') {
    if (!String(c.url ?? '').trim()) errors.url = "L'URL de la vidéo est obligatoire."
  }

  if (form.type === 'cta') {
    if (!String(c.title ?? '').trim()) errors.title = 'Le titre est obligatoire.'
    if (!String(c.button_label ?? '').trim()) errors.button_label = 'Le texte du bouton est obligatoire.'
    if (!String(c.button_url ?? '').trim()) errors.button_url = "L'URL du bouton est obligatoire."
  }

  return Object.keys(errors).length === 0
}

function addSlide() {
  const slides = (form.content.slides as any[]) ?? []
  form.content.slides = [...slides, { url: '', alt: '', caption: '' }]
}

function removeSlide(i: number) {
  form.content.slides = (form.content.slides as any[]).filter((_, idx) => idx !== i)
}

async function handleUpload(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (!file) return
  form.content.url = await blocksStore.uploadFile(file)
}

async function handleSlideUpload(e: Event, i: number) {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (!file) return
  ;(form.content.slides as any[])[i].url = await blocksStore.uploadFile(file)
}

function addStat() {
  const stats = (form.content.stats as any[]) ?? []
  form.content.stats = [...stats, { value: '', label: '', color: 'red' }]
}
function removeStat(i: number) {
  form.content.stats = (form.content.stats as any[]).filter((_, idx) => idx !== i)
}

function addCard() {
  const cards = (form.content.cards as any[]) ?? []
  form.content.cards = [...cards, { icon: '', title: '', text: '', accent: '' }]
}
function removeCard(i: number) {
  form.content.cards = (form.content.cards as any[]).filter((_, idx) => idx !== i)
}

function addEvent() {
  const events = (form.content.events as any[]) ?? []
  form.content.events = [...events, { year: '', text: '' }]
}
function removeEvent(i: number) {
  form.content.events = (form.content.events as any[]).filter((_, idx) => idx !== i)
}

function addFaqItem() {
  const items = (form.content.items as any[]) ?? []
  form.content.items = [...items, { question: '', answer: '' }]
}
function removeFaqItem(i: number) {
  form.content.items = (form.content.items as any[]).filter((_, idx) => idx !== i)
}

function addGalleryItem() {
  const items = (form.content.items as any[]) ?? []
  form.content.items = [...items, { url: '', alt: '' }]
}
function removeGalleryItem(i: number) {
  form.content.items = (form.content.items as any[]).filter((_, idx) => idx !== i)
}

async function handleGalleryUpload(e: Event, i: number) {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (!file) return
  ;(form.content.items as any[])[i].url = await blocksStore.uploadFile(file)
}

async function save() {
  if (!validate()) return
  saving.value = true
  try {
    if (isNew) {
      await blocksStore.createBlock(props.pageId, form.type, form.content)
    } else {
      await blocksStore.updateBlock(props.block!.id, form.content)
    }
    emit('close')
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
.form-label { @apply block text-sm font-semibold text-gray-700 mb-1; }
.form-input { @apply w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rqbi-blue/30 focus:border-rqbi-blue transition-colors; }
</style>
