<template>
  <div class="max-w-5xl mx-auto px-4 pt-12 pb-4">
    <h1
      class="text-4xl md:text-5xl font-extrabold text-rqbi-dark mb-3 transition-all duration-700 ease-out"
      :class="visible ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-8'"
    >
      {{ title }}
    </h1>
    <div
      class="h-1 bg-rqbi-red rounded-full mb-3 transition-all duration-700 delay-200 ease-out origin-left"
      :class="visible ? 'w-14 opacity-100' : 'w-0 opacity-0'"
    />
    <ol v-if="crumbs.length > 1" class="flex items-center gap-1 text-sm text-gray-400 flex-wrap">
      <li v-for="(crumb, i) in crumbs" :key="crumb.slug" class="flex items-center gap-1">
        <span v-if="i > 0" class="text-gray-300">›</span>
        <RouterLink
          v-if="i < crumbs.length - 1"
          :to="'/' + crumb.slug"
          class="hover:text-rqbi-red transition-colors"
        >
          {{ crumb.title }}
        </RouterLink>
        <span v-else class="text-gray-500">{{ crumb.title }}</span>
      </li>
    </ol>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, nextTick } from 'vue'
import { usePagesStore } from '../stores/pages'
import type { Page } from '../stores/pages'

const props = defineProps<{ slug: string; title: string }>()
const pagesStore = usePagesStore()
const visible = ref(false)

onMounted(() => nextTick(() => { visible.value = true }))

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
