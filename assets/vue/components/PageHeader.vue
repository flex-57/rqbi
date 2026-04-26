<template>
  <section class="container-rqbi pt-20 pb-12 border-b border-rqbi-line">
    <ol v-if="crumbs.length > 1" class="flex items-center gap-1.5 font-mono text-xs uppercase tracking-wider text-rqbi-ink-mute mb-6 flex-wrap" v-animate-in>
      <li v-for="(crumb, i) in crumbs" :key="crumb.slug" class="flex items-center gap-1.5">
        <span v-if="i > 0" class="text-rqbi-ink-faint">/</span>
        <RouterLink
          v-if="i < crumbs.length - 1"
          :to="'/' + crumb.slug"
          class="hover:text-rqbi-red transition-colors"
        >{{ crumb.title }}</RouterLink>
        <span v-else class="text-rqbi-ink">{{ crumb.title }}</span>
      </li>
    </ol>
    <h1
      class="font-display text-rqbi-ink text-[clamp(2.5rem,5vw,4.5rem)] leading-[1.02] tracking-tightest font-medium"
      v-animate-in="{ delay: 1 }"
      v-html="formattedTitle"
    />
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { usePagesStore } from '../stores/pages'
import type { Page } from '../stores/pages'

const props = defineProps<{ slug: string; title: string }>()
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

const formattedTitle = computed(() => {
  const words = props.title.split(' ')
  if (words.length < 2) return props.title
  const last = words.pop()
  return `${words.join(' ')} <em class="italic text-rqbi-red font-normal">${last}</em>`
})

const crumbs = computed(() => findPath(pagesStore.tree, props.slug) ?? [])
</script>
