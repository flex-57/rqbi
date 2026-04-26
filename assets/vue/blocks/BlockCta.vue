<template>
  <section
    class="relative py-24 px-6 text-center overflow-hidden"
    :class="bgClass"
  >
    <div class="absolute inset-0 pointer-events-none"
      :class="block.content.background && block.content.background !== 'light'
        ? 'bg-[radial-gradient(circle_at_80%_20%,rgba(255,255,255,0.08),transparent_40%)]'
        : ''"
    />
    <div class="max-w-2xl mx-auto relative" v-animate-in>
      <h2 class="font-display mb-4" :class="textClass">{{ block.content.title }}</h2>
      <p v-if="block.content.subtitle" class="text-lg mb-10 opacity-90" :class="textClass">
        {{ block.content.subtitle }}
      </p>
      <a
        :href="(block.content.button_url as string)"
        class="inline-flex items-center gap-2 px-9 py-4 rounded-full font-medium text-base transition-all duration-300 hover:-translate-y-0.5"
        :class="btnClass"
      >
        {{ block.content.button_label }}
        <span aria-hidden="true">→</span>
      </a>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { Block } from '../stores/pages'

const props = defineProps<{ block: Block; isEditing: boolean }>()

const bgClass = computed(() => {
  switch (props.block.content.background) {
    case 'red':  return 'bg-rqbi-red'
    case 'blue': return 'bg-rqbi-blue'
    case 'dark': return 'bg-rqbi-ink'
    default:     return 'bg-rqbi-cream-deep'
  }
})
const textClass = computed(() => {
  const bg = props.block.content.background
  return bg === 'light' || bg === undefined ? 'text-rqbi-ink' : 'text-white'
})
const btnClass = computed(() => {
  const bg = props.block.content.background
  return bg === 'light' || bg === undefined
    ? 'bg-rqbi-red text-white shadow-rqbi-red hover:bg-rqbi-red-deep'
    : 'bg-white text-rqbi-ink hover:bg-rqbi-cream'
})
</script>
