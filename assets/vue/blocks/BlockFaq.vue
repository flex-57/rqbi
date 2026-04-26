<template>
  <section class="py-20 container-rqbi-narrow">
    <h2 v-if="block.content.title" class="mb-10" v-animate-in>{{ block.content.title }}</h2>
    <div class="flex flex-col gap-2">
      <details
        v-for="(item, i) in items" :key="i"
        v-animate-in
        class="group bg-white border border-rqbi-line rounded-xl overflow-hidden transition-colors hover:border-rqbi-red"
      >
        <summary class="flex items-center justify-between gap-4 px-6 py-5 cursor-pointer list-none">
          <span class="font-display text-lg font-medium text-rqbi-ink">{{ item.question }}</span>
          <span class="shrink-0 w-8 h-8 rounded-full bg-rqbi-cream group-hover:bg-rqbi-red group-hover:text-white text-rqbi-ink flex items-center justify-center transition-all group-open:rotate-45">+</span>
        </summary>
        <div class="px-6 pb-5 text-rqbi-ink-mute leading-relaxed text-sm">
          {{ item.answer }}
        </div>
      </details>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { Block } from '../stores/pages'

const props = defineProps<{ block: Block; isEditing: boolean }>()
const items = computed(() => (props.block.content.items as Array<{ question: string; answer: string }>) ?? [])
</script>

<style scoped>
summary::-webkit-details-marker { display: none; }
</style>
