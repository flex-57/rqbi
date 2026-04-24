<template>
  <section class="py-10 px-4 max-w-5xl mx-auto">
    <h2 v-if="block.content.title" class="text-2xl font-bold text-rqbi-dark mb-8">
      {{ block.content.title }}
    </h2>
    <div class="grid gap-3" :class="gridClass">
      <div
        v-for="(item, i) in items" :key="i"
        v-animate-in="'scale'"
        class="overflow-hidden rounded-xl cursor-pointer group"
        @click="lightboxIndex = i"
      >
        <img
          :src="item.url"
          :alt="item.alt || ''"
          class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
        />
      </div>
    </div>

    <div
      v-if="lightboxIndex !== null"
      class="fixed inset-0 bg-black/90 z-50 flex items-center justify-center p-4"
      @click="lightboxIndex = null"
    >
      <button class="absolute top-4 right-4 text-white text-3xl leading-none" @click="lightboxIndex = null">×</button>
      <img
        :src="items[lightboxIndex].url"
        :alt="items[lightboxIndex].alt || ''"
        class="max-w-full max-h-full object-contain rounded"
        @click.stop
      />
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import type { Block } from '../stores/pages'

const props = defineProps<{ block: Block; isEditing: boolean }>()
const items = computed(() => (props.block.content.items as any[]) ?? [])
const columns = computed(() => Number(props.block.content.columns) || 3)
const gridClass = computed(() => ({
  2: 'grid-cols-1 sm:grid-cols-2',
  3: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
  4: 'grid-cols-2 sm:grid-cols-3 lg:grid-cols-4',
}[columns.value] ?? 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'))
const lightboxIndex = ref<number | null>(null)
</script>
