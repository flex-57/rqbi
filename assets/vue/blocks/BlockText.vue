<template>
  <section class="py-10 px-4 max-w-5xl mx-auto">
    <h2 v-if="block.content.title" class="text-3xl font-bold text-rqbi-dark mb-6">
      {{ block.content.title }}
    </h2>
    <div class="rqbi-content text-gray-700" v-html="block.content.body" />
  </section>
</template>

<script setup lang="ts">
import type { Block } from '../stores/pages'
defineProps<{ block: Block; isEditing: boolean }>()
</script>
