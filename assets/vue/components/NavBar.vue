<template>
  <header
    class="sticky top-0 z-40 transition-all duration-300"
    :class="scrolled
      ? 'bg-rqbi-cream/95 backdrop-blur-md border-b border-rqbi-line'
      : 'bg-rqbi-cream/85 backdrop-blur-md border-b border-transparent'"
  >
    <div class="max-w-[1200px] mx-auto px-6 flex items-center justify-between gap-8 h-20">
      <!-- Logo -->
      <RouterLink to="/accueil" class="flex items-center gap-3 shrink-0">
        <img src="/images/cropped-logo-banniere-512x193.png" alt="RQBI" class="h-11 w-auto" />
        <span class="hidden lg:block font-display italic text-xs leading-tight text-rqbi-ink-mute max-w-[140px]">
          Régie de Quartier<br/>Behren Insertion
        </span>
      </RouterLink>

      <!-- Navigation principale -->
      <nav class="hidden lg:flex items-center gap-1 flex-1 justify-center">
        <template v-for="page in visiblePages" :key="page.id">
          <div class="relative group">
            <RouterLink
              :to="'/' + page.slug"
              class="relative inline-flex items-center px-3.5 py-2 rounded-full text-[0.93rem] font-medium text-rqbi-ink-soft hover:text-rqbi-red transition-colors"
              active-class="!text-rqbi-red after:content-[''] after:absolute after:-bottom-1 after:left-1/2 after:-translate-x-1/2 after:w-1.5 after:h-1.5 after:rounded-full after:bg-rqbi-red"
            >
              {{ page.title }}
              <span v-if="props.isEditing && !page.published" class="text-[0.65em] opacity-50 ml-0.5">(brouillon)</span>
              <span v-if="page.children?.length" class="ml-1 text-[0.7em] opacity-60">▾</span>
            </RouterLink>
            <div
              v-if="page.children?.length"
              class="absolute top-full left-1/2 -translate-x-1/2 translate-y-[-6px] opacity-0 invisible group-hover:opacity-100 group-hover:visible group-hover:translate-y-2 transition-all duration-200 bg-white border border-rqbi-line rounded-xl shadow-rqbi-lg min-w-[240px] p-2"
            >
              <RouterLink
                v-for="child in page.children"
                :key="child.id"
                :to="'/' + child.slug"
                class="block px-3.5 py-2.5 rounded-lg text-sm text-rqbi-ink-soft hover:bg-rqbi-cream hover:text-rqbi-red transition-colors"
              >{{ child.title }}</RouterLink>
            </div>
          </div>
        </template>
      </nav>

      <!-- Actions -->
      <div class="flex items-center gap-2 shrink-0">
        <RouterLink
          v-if="!authStore.isAdmin"
          to="/login"
          class="hidden md:inline-block px-3 py-1.5 text-xs text-rqbi-ink-faint hover:text-rqbi-ink transition-colors"
        >Admin</RouterLink>

        <!-- Burger mobile -->
        <button
          class="lg:hidden inline-flex flex-col gap-[5px] p-2.5 rounded-lg"
          @click="mobileOpen = !mobileOpen"
          aria-label="Menu"
        >
          <span class="block w-[22px] h-[1.6px] bg-rqbi-ink transition-transform duration-300"
            :class="{ 'translate-y-[6.6px] rotate-45': mobileOpen }"></span>
          <span class="block w-[22px] h-[1.6px] bg-rqbi-ink transition-opacity duration-200"
            :class="{ 'opacity-0': mobileOpen }"></span>
          <span class="block w-[22px] h-[1.6px] bg-rqbi-ink transition-transform duration-300"
            :class="{ '-translate-y-[6.6px] -rotate-45': mobileOpen }"></span>
        </button>
      </div>
    </div>

    <!-- Menu mobile -->
    <div v-if="mobileOpen" class="lg:hidden border-t border-rqbi-line bg-white px-6 py-4">
      <template v-for="page in visiblePages" :key="page.id">
        <RouterLink
          :to="'/' + page.slug"
          class="block py-3 text-base font-medium text-rqbi-ink hover:text-rqbi-red border-b border-rqbi-line-soft"
          @click="mobileOpen = false"
        >{{ page.title }}</RouterLink>
        <template v-if="page.children?.length">
          <RouterLink
            v-for="child in page.children"
            :key="child.id"
            :to="'/' + child.slug"
            class="block py-2.5 pl-6 text-sm text-rqbi-ink-mute hover:text-rqbi-red border-b border-rqbi-line-soft"
            @click="mobileOpen = false"
          >└ {{ child.title }}</RouterLink>
        </template>
      </template>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useAuthStore } from '../stores/auth'
import { usePagesStore } from '../stores/pages'

const props = defineProps<{ isEditing?: boolean }>()

const authStore = useAuthStore()
const pagesStore = usePagesStore()

const visiblePages = computed(() =>
  props.isEditing
    ? pagesStore.tree
    : pagesStore.tree.filter(p => p.published)
)

const scrolled = ref(false)
const mobileOpen = ref(false)

function onScroll() { scrolled.value = window.scrollY > 12 }
onMounted(() => { window.addEventListener('scroll', onScroll, { passive: true }); onScroll() })
onBeforeUnmount(() => window.removeEventListener('scroll', onScroll))
</script>
