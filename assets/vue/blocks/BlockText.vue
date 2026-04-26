<template>
  <section class="py-12 container-rqbi-narrow">
    <h2 v-if="block.content.title" class="mb-6" v-animate-in>{{ block.content.title }}</h2>
    <div class="rqbi-content" v-html="block.content.body" v-animate-in />
  </section>
</template>

<script setup lang="ts">
import type { Block } from '../stores/pages'
defineProps<{ block: Block; isEditing: boolean }>()
</script>
