<template>
  <section class="bg-rqbi-dark py-14 px-4">
    <div class="max-w-5xl mx-auto">
      <h2 v-if="block.content.title" class="text-3xl font-extrabold text-white text-center mb-10">
        {{ block.content.title }}
      </h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
        <div v-for="(stat, i) in stats" :key="i" v-animate-in>
          <p class="text-5xl font-extrabold mb-2 leading-none" :class="colorClass(stat.color)">{{ stat.value }}</p>
          <div class="w-6 h-0.5 bg-white/20 rounded mx-auto mb-2" />
          <p class="text-gray-400 text-sm font-medium">{{ stat.label }}</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { Block } from '../stores/pages'

const props = defineProps<{ block: Block; isEditing: boolean }>()
const stats = computed(() => (props.block.content.stats as any[]) ?? [])

function colorClass(color?: string): string {
  return color === 'blue' ? 'text-rqbi-blue' : color === 'dark' ? 'text-white' : 'text-rqbi-red'
}
</script>
