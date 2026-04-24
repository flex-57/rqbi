<template>
  <section class="py-10 px-4 max-w-5xl mx-auto">
    <h2 v-if="block.content.title" class="text-2xl font-bold text-rqbi-dark mb-8">
      {{ block.content.title }}
    </h2>
    <div class="grid gap-6" :class="gridClass">
      <div
        v-for="(card, i) in cards" :key="i"
        v-animate-in="'scale'"
        class="bg-white rounded-xl border border-gray-100 shadow-sm p-6"
        :class="accentClass(card.accent)"
      >
        <div v-if="card.icon" class="text-3xl mb-3">{{ card.icon }}</div>
        <h3 class="font-bold text-rqbi-dark mb-2">{{ card.title }}</h3>
        <p class="text-gray-600 text-sm leading-relaxed">{{ card.text }}</p>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { Block } from '../stores/pages'

const props = defineProps<{ block: Block; isEditing: boolean }>()
const cards = computed(() => (props.block.content.cards as any[]) ?? [])
const columns = computed(() => Number(props.block.content.columns) || 3)
const gridClass = computed(() => ({
  2: 'grid-cols-1 sm:grid-cols-2',
  3: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
  4: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
}[columns.value] ?? 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'))

function accentClass(accent?: string): string {
  return accent === 'red' ? 'border-t-4 border-t-rqbi-red'
    : accent === 'blue'   ? 'border-t-4 border-t-rqbi-blue'
    : accent === 'dark'   ? 'border-t-4 border-t-rqbi-dark'
    : ''
}
</script>
