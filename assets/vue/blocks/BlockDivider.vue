<template>
  <div class="py-8 px-4 max-w-5xl mx-auto">
    <div v-if="!block.content.label" class="flex items-center gap-3">
      <div class="flex-1 h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent" />
      <div class="w-1.5 h-1.5 rounded-full bg-rqbi-red/40" />
      <div class="flex-1 h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent" />
    </div>
    <div v-else class="flex items-center gap-4">
      <div class="flex-1 h-px bg-gradient-to-r from-transparent to-gray-200" />
      <span class="text-sm text-gray-500 font-semibold whitespace-nowrap tracking-wide uppercase">{{ block.content.label }}</span>
      <div class="flex-1 h-px bg-gradient-to-l from-transparent to-gray-200" />
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Block } from '../stores/pages'
defineProps<{ block: Block; isEditing: boolean }>()
</script>
