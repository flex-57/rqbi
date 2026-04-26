<template>
  <section class="py-20 container-rqbi-narrow">
    <h2 v-if="block.content.title" class="mb-12" v-animate-in>{{ block.content.title }}</h2>
    <div class="relative pl-8">
      <span class="absolute left-1.5 top-2 bottom-2 w-px bg-rqbi-line" />
      <div
        v-for="(event, i) in events" :key="i"
        v-animate-in
        class="relative pb-10 last:pb-0"
      >
        <span class="absolute -left-[1.85rem] top-1.5 w-3 h-3 rounded-full bg-white border-2 border-rqbi-red" />
        <span class="font-mono text-xs tracking-widest text-rqbi-red mb-2 block">{{ event.year }}</span>
        <h4 v-if="event.title" class="font-display text-xl font-medium mb-1.5 text-rqbi-ink">{{ event.title }}</h4>
        <p class="text-rqbi-ink-mute text-sm leading-relaxed">{{ event.text }}</p>
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
