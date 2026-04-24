<template>
  <figure class="py-10 px-4 max-w-5xl mx-auto">
    <!-- top -->
    <p v-if="text && position === 'top'" class="rqbi-content text-gray-700 mb-4" v-html="text" />

    <!-- left / right -->
    <div
      v-if="text && (position === 'left' || position === 'right')"
      class="flex flex-col gap-6 items-center md:flex-row"
      :class="position === 'right' ? 'md:flex-row-reverse' : ''"
    >
      <img :src="block.content.url as string" :alt="block.content.alt as string" class="w-full md:w-1/2 h-auto rounded shadow-sm" />
      <p class="rqbi-content text-gray-700 w-full md:w-1/2" v-html="text" />
    </div>

    <!-- no text or top/bottom: normal image -->
    <img
      v-if="!text || position === 'top' || position === 'bottom'"
      :src="block.content.url as string"
      :alt="block.content.alt as string"
      class="w-full h-auto rounded shadow-sm"
    />

    <!-- bottom -->
    <p v-if="text && position === 'bottom'" class="rqbi-content text-gray-700 mt-4" v-html="text" />

    <figcaption v-if="block.content.caption" class="text-center text-sm text-gray-500 mt-2 italic">
      {{ block.content.caption }}
    </figcaption>
  </figure>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { Block } from '../stores/pages'

const props = defineProps<{ block: Block; isEditing: boolean }>()

const text     = computed(() => (props.block.content.text as string) || '')
const position = computed(() => (props.block.content.text_position as string) || 'bottom')
</script>
