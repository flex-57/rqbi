<template>
  <section class="py-20 container-rqbi">
    <h2 v-if="block.content.title" class="mb-12 max-w-3xl" v-animate-in>{{ block.content.title }}</h2>
    <div class="grid gap-3" :class="gridClass">
      <div
        v-for="(item, i) in items" :key="i"
        v-animate-in="'scale'"
        class="group relative aspect-square overflow-hidden rounded-xl bg-rqbi-cream-deep cursor-pointer"
        @click="lightboxIndex = i"
      >
        <img :src="item.url" :alt="item.alt || ''"
          class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
        <div v-if="item.caption" class="absolute inset-x-0 bottom-0 p-3 bg-gradient-to-t from-black/80 to-transparent translate-y-2 group-hover:translate-y-0 transition-transform">
          <span class="text-white text-xs font-medium">{{ item.caption }}</span>
        </div>
      </div>
    </div>

    <!-- Lightbox -->
    <div
      v-if="lightboxIndex !== null"
      class="fixed inset-0 bg-black/90 z-50 flex items-center justify-center p-4"
      @click="lightboxIndex = null"
    >
      <button class="absolute top-4 right-4 text-white text-3xl leading-none hover:text-rqbi-red transition-colors" @click="lightboxIndex = null">×</button>
      <img
        :src="items[lightboxIndex].url"
        :alt="items[lightboxIndex].alt || ''"
        class="max-w-full max-h-full object-contain rounded-xl"
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
