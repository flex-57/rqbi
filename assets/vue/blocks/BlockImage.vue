<template>
  <figure class="py-12 container-rqbi" v-animate-in>
    <!-- Texte au-dessus -->
    <p v-if="text && position === 'top'" class="rqbi-content mb-6" v-html="text" />

    <!-- Image + texte côte à côte -->
    <div
      v-if="text && (position === 'left' || position === 'right')"
      class="flex flex-col gap-8 items-center md:flex-row"
      :class="position === 'right' ? 'md:flex-row-reverse' : ''"
    >
      <div class="w-full md:w-1/2 overflow-hidden rounded-2xl border border-rqbi-line shadow-rqbi-md">
        <img :src="(block.content.url as string)" :alt="(block.content.alt as string) || ''"
          class="w-full h-auto block transition-transform duration-700 hover:scale-105" />
      </div>
      <p class="rqbi-content w-full md:w-1/2" v-html="text" />
    </div>

    <!-- Image seule -->
    <div
      v-if="!text || position === 'top' || position === 'bottom'"
      class="overflow-hidden rounded-2xl border border-rqbi-line shadow-rqbi-md"
    >
      <img :src="(block.content.url as string)" :alt="(block.content.alt as string) || ''"
        class="w-full h-auto block transition-transform duration-700 hover:scale-105" />
    </div>

    <!-- Texte en-dessous -->
    <p v-if="text && position === 'bottom'" class="rqbi-content mt-6" v-html="text" />

    <figcaption v-if="block.content.caption" class="mt-3 text-sm text-rqbi-ink-mute italic font-display">
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
