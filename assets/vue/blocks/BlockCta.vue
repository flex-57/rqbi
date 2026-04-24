<template>
  <section
    class="py-16 px-4 text-center"
    :class="bgClass"
  >
    <div class="max-w-2xl mx-auto">
      <h2 class="text-3xl font-bold mb-4" :class="textClass">{{ block.content.title }}</h2>
      <p v-if="block.content.subtitle" class="text-lg mb-8 opacity-90" :class="textClass">
        {{ block.content.subtitle }}
      </p>
      <a
        :href="block.content.button_url as string"
        class="inline-block px-9 py-4 font-bold rounded-full transition-all duration-200 text-base shadow-lg hover:shadow-xl hover:-translate-y-0.5"
        :class="btnClass"
      >
        {{ block.content.button_label }}
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
    case 'dark': return 'bg-rqbi-dark'
    default:     return 'bg-rqbi-light'
  }
})

const textClass = computed(() => {
  const bg = props.block.content.background
  return bg === 'light' || bg === undefined ? 'text-rqbi-dark' : 'text-white'
})

const btnClass = computed(() => {
  const bg = props.block.content.background
  return bg === 'light' || bg === undefined
    ? 'bg-rqbi-red text-white hover:bg-red-700'
    : 'bg-white text-rqbi-dark hover:bg-gray-100'
})
</script>
