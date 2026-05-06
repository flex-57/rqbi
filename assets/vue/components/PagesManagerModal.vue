<template>
  <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg p-6">
      <div class="flex items-center justify-between mb-5">
        <h3 class="text-lg font-bold text-rqbi-dark">Toutes les pages</h3>
        <button
          class="text-gray-400 hover:text-gray-600 text-xl leading-none"
          @click="emit('close')"
        >×</button>
      </div>

      <div class="flex flex-col gap-0.5 max-h-96 overflow-y-auto">
        <template v-if="flatPages.length > 0">
          <div
            v-for="{ page, depth } in flatPages"
            :key="page.id"
            :data-depth="depth"
            class="flex items-center gap-2 py-2 pr-2 rounded-lg hover:bg-rqbi-cream"
            :style="{ paddingLeft: `${12 + depth * 20}px` }"
          >
            <span class="flex-1 text-sm text-rqbi-ink truncate">{{ page.title }}</span>
            <span
              class="text-xs px-1.5 py-0.5 rounded-full shrink-0"
              :class="page.published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
            >{{ page.published ? 'Publié' : 'Brouillon' }}</span>
            <button
              class="text-xs px-2 py-0.5 rounded border border-rqbi-line bg-white hover:bg-rqbi-cream shrink-0"
              @click="toggle(page)"
            >{{ page.published ? 'Dépublier' : 'Publier' }}</button>
            <button
              class="text-xs px-2 py-0.5 rounded bg-rqbi-blue text-white hover:opacity-80 shrink-0"
              @click="navigate(page)"
            >→</button>
          </div>
        </template>
        <p v-else class="text-sm text-rqbi-ink-mute text-center py-8">
          Aucune page créée
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { usePagesStore } from '../stores/pages'
import type { Page } from '../stores/pages'

const emit = defineEmits<{ close: [] }>()

const pagesStore = usePagesStore()
const router = useRouter()

interface FlatPage { page: Page; depth: number }

function flattenTree(pages: Page[], depth = 0): FlatPage[] {
  return pages.flatMap(p => [
    { page: p, depth },
    ...flattenTree(p.children ?? [], depth + 1),
  ])
}

const flatPages = computed(() => flattenTree(pagesStore.tree))

async function toggle(page: Page) {
  try {
    await pagesStore.updatePage(page.id, { published: !page.published })
  } catch (e) {
    console.error('Erreur lors de la mise à jour du statut de publication', e)
  }
}

function navigate(page: Page) {
  router.push('/' + page.slug)
  emit('close')
}
</script>
