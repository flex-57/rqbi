<template>
  <figure class="my-8 max-w-4xl mx-auto px-4">
    <!-- top -->
    <p v-if="text && position === 'top'" class="rqbi-content text-gray-700 mb-4" v-html="text" />

    <!-- left / right -->
    <div
      v-if="text && (position === 'left' || position === 'right')"
      class="flex gap-6 items-center"
      :class="position === 'right' ? 'flex-row-reverse' : 'flex-row'"
    >
      <img :src="block.content.url as string" :alt="block.content.alt as string" class="w-1/2 h-auto rounded shadow-sm" />
      <p class="rqbi-content text-gray-700 w-1/2" v-html="text" />
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
