<template>
  <div class="container-rqbi-narrow py-10">
    <div class="flex items-center gap-4">
      <span class="flex-1 h-px bg-rqbi-line" />
      <span v-if="block.content.label" class="font-mono text-xs tracking-widest uppercase text-rqbi-ink-mute">
        {{ block.content.label }}
      </span>
      <span v-else class="w-1.5 h-1.5 rounded-full bg-rqbi-red" />
      <span class="flex-1 h-px bg-rqbi-line" />
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Block } from '../stores/pages'
defineProps<{ block: Block; isEditing: boolean }>()
</script>
