<template>
  <div
    v-animate-in="{ variant: 'scale', delay }"
    class="relative bg-white rounded-2xl border border-rqbi-line p-8 transition-all duration-500 hover:-translate-y-1.5 hover:shadow-rqbi-lg overflow-hidden group"
  >
    <span
      class="absolute top-0 left-0 right-0 h-[3px] origin-left scale-x-[0.3] group-hover:scale-x-100 transition-transform duration-500"
      :class="accentBg"
    />
    <span class="font-mono text-xs tracking-widest uppercase mb-6 block" :class="accentText">{{ num }}</span>
    <h3 class="font-display text-2xl font-medium mb-3 text-rqbi-ink leading-tight">{{ title }}</h3>
    <p class="text-rqbi-ink-mute text-sm leading-relaxed">{{ text2 || text }}</p>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  num: string
  title: string
  text: string
  text2?: string
  accent?: 'red' | 'blue' | 'dark'
  delay?: 1 | 2 | 3 | 4
}>()

const accentBg = computed(() =>
  props.accent === 'blue' ? 'bg-rqbi-blue' :
  props.accent === 'dark' ? 'bg-rqbi-ink' : 'bg-rqbi-red'
)
const accentText = computed(() =>
  props.accent === 'blue' ? 'text-rqbi-blue' :
  props.accent === 'dark' ? 'text-rqbi-ink' : 'text-rqbi-red'
)
</script>
