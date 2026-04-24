<template>
  <section class="py-10 px-4 max-w-5xl mx-auto">
    <h2 v-if="block.content.title" class="text-2xl font-bold text-rqbi-dark mb-8">
      {{ block.content.title }}
    </h2>
    <div class="space-y-6">
      <div v-for="(event, i) in events" :key="i" v-animate-in class="flex gap-4">
        <div class="shrink-0 w-16 text-right">
          <span class="text-rqbi-red font-bold text-sm">{{ event.year }}</span>
        </div>
        <div class="relative flex-1 pb-6">
          <div class="absolute left-0 top-0 bottom-0 -translate-x-1/2 w-px bg-gray-200" />
          <div class="absolute left-0 top-1 -translate-x-1/2 w-3 h-3 rounded-full bg-rqbi-red" />
          <p class="text-gray-600 text-sm leading-relaxed pl-4">{{ event.text }}</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { Block } from '../stores/pages'

const props = defineProps<{ block: Block; isEditing: boolean }>()
const events = computed(() => (props.block.content.events as any[]) ?? [])
</script>
