<template>
  <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
      <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <h2 class="text-xl font-bold text-rqbi-dark">
          {{ page ? 'Modifier la page' : 'Nouvelle page' }}
        </h2>
        <button class="text-gray-400 hover:text-gray-600 text-2xl leading-none" @click="$emit('close')">×</button>
      </div>

      <div class="p-6 space-y-4">
        <!-- Infos principales -->
        <div>
          <label class="form-label">Titre <span class="text-red-500">*</span></label>
          <input v-model="form.title" type="text" class="form-input" placeholder="Titre de la page" @input="updateSlug" />
        </div>
        <div>
          <label class="form-label">Slug (URL)</label>
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-400">/</span>
            <input v-model="form.slug" type="text" class="form-input" placeholder="mon-slug" />
          </div>
          <p class="text-xs text-gray-500 mt-1">Généré automatiquement depuis le titre</p>
        </div>
        <div v-if="!page">
          <label class="form-label">Page parente (optionnelle)</label>
          <select v-model="form.parent_id" class="form-input">
            <option :value="null">Aucune (page racine)</option>
            <option v-for="p in eligibleParents" :key="p.id" :value="p.id">{{ p.title }}</option>
          </select>
        </div>
        <label class="flex items-center gap-2 text-sm cursor-pointer">
          <input v-model="form.published" type="checkbox" class="rounded" />
          <span class="font-medium">Publier immédiatement</span>
        </label>

        <!-- SEO -->
        <div class="border-t border-gray-100 pt-4">
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-3">SEO</p>
          <div class="space-y-3">
            <div>
              <label class="form-label">
                Titre SEO
                <span class="text-gray-400 font-normal">(optionnel — remplace le titre dans l'onglet)</span>
              </label>
              <input
                v-model="form.meta_title"
                type="text"
                class="form-input"
                :class="{ 'border-orange-300': metaTitleLength > 60 }"
                placeholder="Titre pour les moteurs de recherche"
                maxlength="80"
              />
              <p class="text-xs mt-1" :class="metaTitleLength > 60 ? 'text-orange-500' : 'text-gray-400'">
                {{ metaTitleLength }}/60 caractères recommandés
              </p>
            </div>
            <div>
              <label class="form-label">
                Meta description
                <span class="text-gray-400 font-normal">(optionnelle)</span>
              </label>
              <textarea
                v-model="form.meta_description"
                rows="3"
                class="form-input resize-none"
                :class="{ 'border-orange-300': metaDescLength > 160 }"
                placeholder="Description affichée dans les résultats Google"
                maxlength="300"
              />
              <p class="text-xs mt-1" :class="metaDescLength > 160 ? 'text-orange-500' : 'text-gray-400'">
                {{ metaDescLength }}/160 caractères recommandés
              </p>
            </div>
          </div>
        </div>

        <!-- Aperçu Google -->
        <div v-if="form.title || form.meta_description" class="border border-gray-200 rounded-lg p-4 bg-gray-50">
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-2">Aperçu Google</p>
          <p class="text-blue-700 text-base font-medium truncate">{{ form.meta_title || form.title || 'Titre de la page' }}</p>
          <p class="text-green-700 text-xs">rqbi.fr/{{ form.slug }}</p>
          <p class="text-gray-600 text-sm mt-1 line-clamp-2">{{ form.meta_description || 'Aucune description renseignée.' }}</p>
        </div>
      </div>

      <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
        <button class="btn-ghost" @click="$emit('close')">Annuler</button>
        <button class="btn-primary" :disabled="saving || !form.title" @click="save">
          {{ saving ? 'Enregistrement...' : 'Enregistrer' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref, computed } from 'vue'
import type { Page } from '../stores/pages'
import { usePagesStore } from '../stores/pages'
import { useRouter } from 'vue-router'

const props = defineProps<{ page: Page | null; parentId?: number | null }>()
const emit = defineEmits<{ close: []; saved: [page: Page] }>()

const pagesStore = usePagesStore()
const router = useRouter()
const saving = ref(false)

const form = reactive({
  title:            props.page?.title ?? '',
  slug:             props.page?.slug ?? '',
  parent_id:        props.parentId ?? null as number | null,
  published:        props.page?.published ?? false,
  meta_title:       props.page?.metaTitle ?? '',
  meta_description: props.page?.metaDescription ?? '',
})

const eligibleParents = computed(() =>
  pagesStore.tree.filter(p => p.depth < 2 && p.id !== props.page?.id)
)

const metaTitleLength = computed(() => form.meta_title.length)
const metaDescLength  = computed(() => form.meta_description.length)

function slugify(text: string): string {
  return text.toLowerCase()
    .normalize('NFD').replace(/[̀-ͯ]/g, '')
    .replace(/[^a-z0-9\s-]/g, '')
    .trim()
    .replace(/\s+/g, '-')
}

function updateSlug() {
  if (!props.page) form.slug = slugify(form.title)
}

async function save() {
  saving.value = true
  try {
    let saved: Page
    if (props.page) {
      saved = await pagesStore.updatePage(props.page.id, {
        title:            form.title,
        slug:             form.slug,
        published:        form.published,
        meta_title:       form.meta_title || null,
        meta_description: form.meta_description || null,
      })
    } else {
      saved = await pagesStore.createPage({
        title:     form.title,
        parent_id: form.parent_id,
        published: form.published,
      })
    }
    emit('saved', saved)
    emit('close')
    if (!props.page) router.push('/' + saved.slug)
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
.form-label { @apply block text-sm font-semibold text-gray-700 mb-1; }
.form-input  { @apply w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rqbi-blue/30 focus:border-rqbi-blue transition-colors; }
</style>
