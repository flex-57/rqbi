<template>
  <header class="bg-white/95 backdrop-blur-sm border-b border-gray-100 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-20">
      <!-- Logo -->
      <RouterLink to="/accueil" class="flex items-center gap-3 shrink-0">
        <img src="/images/cropped-logo-banniere-512x193.png" alt="RQBI" class="h-20 w-auto" />
      </RouterLink>

      <!-- Navigation principale -->
      <nav class="hidden lg:flex items-center gap-1">
        <template v-for="page in pagesStore.tree" :key="page.id">
          <div class="relative group">
            <RouterLink
              :to="'/' + page.slug"
              class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-rqbi-red hover:bg-red-50 transition-all rounded-lg"
              active-class="text-rqbi-red bg-red-50"
            >
              {{ page.title }}
            </RouterLink>
            <!-- Sous-menu -->
            <div
              v-if="page.children?.length"
              class="absolute top-full left-0 bg-white border border-gray-100 rounded-lg shadow-lg min-w-40 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200"
            >
              <RouterLink
                v-for="child in page.children"
                :key="child.id"
                :to="'/' + child.slug"
                class="block px-4 py-2 text-sm text-rqbi-dark hover:text-rqbi-red hover:bg-gray-50 transition-colors"
              >
                {{ child.title }}
              </RouterLink>
            </div>
          </div>
        </template>
      </nav>

      <!-- Actions admin -->
      <div class="flex items-center gap-2">
        <template v-if="authStore.isAdmin">
          <button
            class="flex items-center gap-1 px-3 py-1.5 rounded text-sm font-medium transition-colors"
            :class="isEditing
              ? 'bg-rqbi-red text-white hover:bg-red-700'
              : 'bg-rqbi-blue text-white hover:bg-blue-700'"
            @click="$emit('toggleEditing')"
          >
            {{ isEditing ? '✓ Terminer' : '✏️ Éditer' }}
          </button>

          <div v-if="isEditing" class="relative" ref="pageMenuRef">
            <button
              class="px-3 py-1.5 border border-gray-300 rounded text-sm font-medium hover:bg-gray-50 flex items-center gap-1"
              @click="pageMenuOpen = !pageMenuOpen"
            >
              Pages ▾
            </button>
            <div
              v-if="pageMenuOpen"
              class="absolute right-0 top-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg min-w-48 z-50"
            >
              <button class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 text-rqbi-blue" @click="openPageEditor(null); pageMenuOpen = false">
                + Nouvelle page racine
              </button>
              <button
                v-if="pagesStore.currentPage"
                class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 text-rqbi-blue"
                @click="openPageEditor(pagesStore.currentPage!.id); pageMenuOpen = false"
              >
                + Sous-page de « {{ pagesStore.currentPage?.title }} »
              </button>
              <hr class="my-1" />
              <button
                v-if="pagesStore.currentPage"
                class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50"
                @click="editCurrentPage(); pageMenuOpen = false"
              >
                ✎ Modifier cette page
              </button>
              <button
                v-if="pagesStore.currentPage"
                class="w-full text-left px-4 py-2 text-sm hover:bg-red-50 text-red-500"
                @click="deleteCurrentPage(); pageMenuOpen = false"
              >
                ✕ Supprimer cette page
              </button>
            </div>
          </div>

          <button
            class="px-3 py-1.5 text-sm text-gray-500 hover:text-gray-700"
            @click="authStore.logout()"
          >
            Déconnexion
          </button>
        </template>

        <RouterLink
          v-else
          to="/login"
          class="px-3 py-1.5 text-sm text-gray-500 hover:text-rqbi-red transition-colors"
        >
          Admin
        </RouterLink>

        <!-- Menu mobile -->
        <button class="lg:hidden p-2 text-gray-600" @click="mobileOpen = !mobileOpen">
          <span class="text-xl">☰</span>
        </button>
      </div>
    </div>

    <!-- Menu mobile déroulant -->
    <div v-if="mobileOpen" class="lg:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-1">
      <template v-for="page in pagesStore.tree" :key="page.id">
        <RouterLink
          :to="'/' + page.slug"
          class="block py-2 text-sm font-medium text-rqbi-dark hover:text-rqbi-red"
          @click="mobileOpen = false"
        >
          {{ page.title }}
        </RouterLink>
        <template v-if="page.children?.length">
          <RouterLink
            v-for="child in page.children"
            :key="child.id"
            :to="'/' + child.slug"
            class="block py-2 pl-4 text-sm text-gray-600 hover:text-rqbi-red"
            @click="mobileOpen = false"
          >
            └ {{ child.title }}
          </RouterLink>
        </template>
      </template>
    </div>
  </header>

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
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { usePagesStore } from '../stores/pages'
import type { Page } from '../stores/pages'
import PageEditor from './PageEditor.vue'
import ConfirmDialog from './ConfirmDialog.vue'

defineProps<{ isEditing: boolean }>()
defineEmits<{ toggleEditing: [] }>()

const authStore = useAuthStore()
const pagesStore = usePagesStore()
const router = useRouter()

const mobileOpen = ref(false)
const pageMenuOpen = ref(false)
const pageEditorOpen = ref(false)
const pageToEdit = ref<Page | null>(null)
const newPageParentId = ref<number | null>(null)
const confirmDelete = ref(false)

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
