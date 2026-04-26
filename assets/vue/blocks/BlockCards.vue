<template>
  <section class="py-20">
    <div class="container-rqbi">
      <h2 v-if="block.content.title" class="text-rqbi-ink mb-12 max-w-3xl" v-animate-in>
        {{ block.content.title }}
      </h2>
      <div class="grid gap-5" :class="gridClass">
        <div
          v-for="(card, i) in cards" :key="i"
          v-animate-in="'scale'"
          class="relative bg-white rounded-2xl border border-rqbi-line p-8 transition-all duration-500 hover:-translate-y-1.5 hover:shadow-rqbi-lg overflow-hidden group"
        >
          <span
            class="absolute top-0 left-0 right-0 h-[3px] origin-left scale-x-[0.3] group-hover:scale-x-100 transition-transform duration-500"
            :class="accentBg(card.accent)"
          />
          <span v-if="card.icon" class="block text-3xl mb-4">{{ card.icon }}</span>
          <span class="font-mono text-xs tracking-widest uppercase mb-3 block" :class="accentText(card.accent)">
            {{ String(i + 1).padStart(2, '0') }}
          </span>
          <h3 class="font-display text-2xl font-medium mb-2 text-rqbi-ink">{{ card.title }}</h3>
          <p class="text-rqbi-ink-mute text-sm leading-relaxed">{{ card.text }}</p>
        </div>
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

function accentBg(a?: string) {
  return a === 'blue' ? 'bg-rqbi-blue' : a === 'dark' ? 'bg-rqbi-ink' : 'bg-rqbi-red'
}
function accentText(a?: string) {
  return a === 'blue' ? 'text-rqbi-blue' : a === 'dark' ? 'text-rqbi-ink' : 'text-rqbi-red'
}
</script>
