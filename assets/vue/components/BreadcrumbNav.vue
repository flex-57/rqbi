<template>
  <nav v-if="crumbs.length > 1" aria-label="Fil d'ariane" class="max-w-4xl mx-auto px-4 pt-6 pb-2">
    <ol class="flex items-center gap-1 text-sm text-gray-500 flex-wrap">
      <li v-for="(crumb, i) in crumbs" :key="crumb.slug" class="flex items-center gap-1">
        <span v-if="i > 0" class="text-gray-300">›</span>
        <RouterLink
          v-if="i < crumbs.length - 1"
          :to="'/' + crumb.slug"
          class="hover:text-rqbi-red transition-colors"
        >
          {{ crumb.title }}
        </RouterLink>
        <span v-else class="text-rqbi-dark font-medium">{{ crumb.title }}</span>
      </li>
    </ol>
  </nav>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { usePagesStore } from '../stores/pages'
import type { Page } from '../stores/pages'

const props = defineProps<{ slug: string }>()
const pagesStore = usePagesStore()

function findPath(pages: Page[], slug: string, path: Page[] = []): Page[] | null {
  for (const page of pages) {
    const current = [...path, page]
    if (page.slug === slug) return current
    if (page.children?.length) {
      const found = findPath(page.children, slug, current)
      if (found) return found
    }
  }
  return null
}

const crumbs = computed(() => findPath(pagesStore.tree, props.slug) ?? [])
</script>
