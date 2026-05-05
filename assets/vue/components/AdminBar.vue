<template>
  <div class="fixed bottom-0 left-0 right-0 z-40 h-11 bg-rqbi-ink text-white/80 flex items-center px-4 gap-2 text-sm">
    <span class="text-white/35 text-xs mr-auto hidden sm:block truncate max-w-[200px]">
      {{ authStore.user?.email }}
    </span>

    <button
      class="inline-flex items-center gap-1 px-3 py-1 rounded text-xs font-medium transition-colors shrink-0"
      :class="isEditing ? 'bg-rqbi-red text-white' : 'bg-white/10 hover:bg-white/20'"
      @click="$emit('toggleEditing')"
    >
      {{ isEditing ? '✓ Terminer' : '✎ Éditer' }}
    </button>

    <div v-if="isEditing" class="relative shrink-0" ref="pageMenuRef">
      <button
        class="px-3 py-1 bg-white/10 hover:bg-white/20 rounded text-xs font-medium flex items-center gap-1"
        @click="pageMenuOpen = !pageMenuOpen"
      >Pages ▾</button>
      <div
        v-if="pageMenuOpen"
        class="absolute bottom-full mb-1 right-0 bg-white text-rqbi-ink border border-rqbi-line rounded-xl shadow-rqbi-lg min-w-[220px] z-50 p-1"
      >
        <button
          class="w-full text-left px-3.5 py-2 text-sm rounded-lg hover:bg-rqbi-cream text-rqbi-blue"
          @click="openPageEditor(null); pageMenuOpen = false"
        >+ Nouvelle page racine</button>
        <button
          v-if="pagesStore.currentPage"
          class="w-full text-left px-3.5 py-2 text-sm rounded-lg hover:bg-rqbi-cream text-rqbi-blue"
          @click="openPageEditor(pagesStore.currentPage!.id); pageMenuOpen = false"
        >+ Sous-page de « {{ pagesStore.currentPage?.title }} »</button>
        <hr class="my-1 border-rqbi-line-soft" />
        <button
          v-if="pagesStore.currentPage"
          class="w-full text-left px-3.5 py-2 text-sm rounded-lg hover:bg-rqbi-cream"
          @click="editCurrentPage(); pageMenuOpen = false"
        >✎ Modifier cette page</button>
        <button
          v-if="pagesStore.currentPage"
          class="w-full text-left px-3.5 py-2 text-sm rounded-lg hover:bg-red-50 text-rqbi-red"
          @click="deleteCurrentPage(); pageMenuOpen = false"
        >✕ Supprimer cette page</button>
      </div>
    </div>

    <button
      class="px-3 py-1 bg-white/10 hover:bg-white/20 rounded text-xs font-medium shrink-0"
      @click="changePasswordOpen = true"
    >🔑 Mot de passe</button>

    <button
      class="px-3 py-1 bg-white/10 hover:bg-white/20 rounded text-xs font-medium shrink-0"
      @click="authStore.logout()"
    >Déconnexion</button>
  </div>

  <PageEditor
    v-if="pageEditorOpen"
    :page="pageToEdit"
    :parent-id="newPageParentId"
    @close="pageEditorOpen = false"
    @saved="pageEditorOpen = false"
  />

  <ConfirmDialog
    v-if="confirmDelete"
    title="Supprimer la page"
    :message="`Êtes-vous sûr de vouloir supprimer « ${pagesStore.currentPage?.title} » et tous ses blocs ?`"
    @confirm="doDeletePage"
    @cancel="confirmDelete = false"
  />

  <ChangePasswordModal
    v-if="changePasswordOpen"
    @close="changePasswordOpen = false"
  />
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { usePagesStore } from '../stores/pages'
import type { Page } from '../stores/pages'
import PageEditor from './PageEditor.vue'
import ConfirmDialog from './ConfirmDialog.vue'
import ChangePasswordModal from './ChangePasswordModal.vue'

defineProps<{ isEditing: boolean }>()
defineEmits<{ toggleEditing: [] }>()

const authStore = useAuthStore()
const pagesStore = usePagesStore()
const router = useRouter()

const pageMenuOpen = ref(false)
const pageEditorOpen = ref(false)
const pageToEdit = ref<Page | null>(null)
const newPageParentId = ref<number | null>(null)
const confirmDelete = ref(false)
const changePasswordOpen = ref(false)

function openPageEditor(parentId: number | null) {
  pageToEdit.value = null
  newPageParentId.value = parentId
  pageEditorOpen.value = true
}

function editCurrentPage() {
  pageToEdit.value = pagesStore.currentPage
  newPageParentId.value = null
  pageEditorOpen.value = true
}

function deleteCurrentPage() {
  confirmDelete.value = true
}

async function doDeletePage() {
  if (!pagesStore.currentPage) return
  confirmDelete.value = false
  await pagesStore.deletePage(pagesStore.currentPage.id)
  router.push('/accueil')
}
</script>
