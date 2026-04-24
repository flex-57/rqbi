<template>
  <figure class="w-full my-8">
    <img
      :src="block.content.url as string"
      :alt="block.content.alt as string"
      class="w-full h-auto object-cover rounded shadow-sm"
    />
    <figcaption v-if="block.content.caption" class="text-center text-sm text-gray-500 mt-2 italic">
      {{ block.content.caption }}
    </figcaption>
  </figure>
</template>

<script setup lang="ts">
import type { Block } from '../stores/pages'
defineProps<{ block: Block; isEditing: boolean }>()
</script>
