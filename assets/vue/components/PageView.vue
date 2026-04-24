<template>
  <div>
    <div v-if="notFound" class="min-h-64 flex flex-col items-center justify-center text-center py-20">
      <p class="text-6xl mb-4">😕</p>
      <h1 class="text-3xl font-bold text-rqbi-dark mb-2">Page introuvable</h1>
      <p class="text-gray-500 mb-6">Cette page n'existe pas ou a été supprimée.</p>
      <RouterLink to="/accueil" class="btn-primary">Retour à l'accueil</RouterLink>
    </div>

    <div v-else-if="pagesStore.loading" class="min-h-64 flex items-center justify-center">
      <div class="w-10 h-10 border-4 border-rqbi-blue border-t-transparent rounded-full animate-spin" />
    </div>

    <template v-else-if="pagesStore.currentPage">
      <BlockRenderer
        :blocks="pagesStore.currentPage.blocks"
        :is-editing="isEditing"
        @add-block="blockEditorOpen = true; editingBlock = null"
        @edit-block="openEditBlock"
        @delete-block="confirmDeleteBlock"
        @toggle-block="handleToggleBlock"
        @reorder="handleReorder"
      />
    </template>
  </div>

  <BlockEditor
    v-if="blockEditorOpen && pagesStore.currentPage"
    :block="editingBlock"
    :page-id="pagesStore.currentPage.id"
    @close="blockEditorOpen = false; editingBlock = null"
  />

  <ConfirmDialog
    v-if="confirmDeleteBlockId !== null"
    title="Supprimer le bloc"
    message="Cette action est irréversible."
    @confirm="doDeleteBlock"
    @cancel="confirmDeleteBlockId = null"
  />
</template>

<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { useRoute } from 'vue-router'
import { usePagesStore } from '../stores/pages'
import { useBlocksStore } from '../stores/blocks'
import BlockRenderer from './BlockRenderer.vue'
import BlockEditor from './BlockEditor.vue'
import ConfirmDialog from './ConfirmDialog.vue'
import type { Block } from '../stores/pages'

defineProps<{ isEditing: boolean }>()

const route = useRoute()
const pagesStore = usePagesStore()
const blocksStore = useBlocksStore()

const notFound = ref(false)
const blockEditorOpen = ref(false)
const editingBlock = ref<Block | null>(null)
const confirmDeleteBlockId = ref<number | null>(null)

const slug = computed(() => {
  const s = route.params.slug
  return Array.isArray(s) ? s.join('/') : s || 'accueil'
})

async function loadPage(s: string) {
  notFound.value = false
  try {
    await pagesStore.fetchPage(s || 'accueil')
    applyMeta()
  } catch {
    notFound.value = true
  }
}

function applyMeta() {
  const page = pagesStore.currentPage
  if (!page) return

  document.title = page.metaTitle || page.title + ' — RQBI'

  let desc = document.querySelector<HTMLMetaElement>('meta[name="description"]')
  if (!desc) {
    desc = document.createElement('meta')
    desc.name = 'description'
    document.head.appendChild(desc)
  }
  desc.content = page.metaDescription ?? ''
}

watch(slug, loadPage, { immediate: true })

function openEditBlock(block: Block) {
  editingBlock.value = block
  blockEditorOpen.value = true
}

function confirmDeleteBlock(block: Block) {
  confirmDeleteBlockId.value = block.id
}

async function doDeleteBlock() {
  if (confirmDeleteBlockId.value === null) return
  await blocksStore.deleteBlock(confirmDeleteBlockId.value)
  confirmDeleteBlockId.value = null
}

async function handleToggleBlock(block: Block) {
  await blocksStore.toggleBlock(block.id)
}

async function handleReorder(order: Array<{ id: number; position: number }>) {
  if (!pagesStore.currentPage) return
  await blocksStore.reorderBlocks(pagesStore.currentPage.id, order)
}
</script>
