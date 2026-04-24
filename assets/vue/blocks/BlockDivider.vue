<template>
  <div class="my-10 px-4 max-w-4xl mx-auto">
    <div v-if="!block.content.label" class="border-t border-gray-300" />
    <div v-else class="flex items-center gap-4">
      <div class="flex-1 border-t border-gray-300" />
      <span class="text-sm text-gray-500 font-medium whitespace-nowrap">{{ block.content.label }}</span>
      <div class="flex-1 border-t border-gray-300" />
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Block } from '../stores/pages'
defineProps<{ block: Block; isEditing: boolean }>()
</script>
