<template>
  <div class="relative w-full overflow-hidden bg-gray-900 my-8" style="min-height: 320px;">
    <div
      class="flex transition-transform duration-500 ease-in-out h-full"
      :style="{ transform: `translateX(-${current * 100}%)` }"
    >
      <div
        v-for="(slide, i) in slides"
        :key="i"
        class="min-w-full relative"
      >
        <img :src="slide.url" :alt="slide.alt" class="w-full h-80 object-cover" />
        <div v-if="slide.caption" class="absolute bottom-0 left-0 right-0 bg-black/50 text-white text-center py-2 text-sm">
          {{ slide.caption }}
        </div>
      </div>
    </div>

    <button
      v-if="slides.length > 1"
      class="absolute left-3 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white rounded-full p-2 shadow"
      @click="prev"
    >
      ‹
    </button>
    <button
      v-if="slides.length > 1"
      class="absolute right-3 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white rounded-full p-2 shadow"
      @click="next"
    >
      ›
    </button>

    <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-2">
      <button
        v-for="(_, i) in slides"
        :key="i"
        class="w-2 h-2 rounded-full transition-colors"
        :class="i === current ? 'bg-white' : 'bg-white/50'"
        @click="current = i"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import type { Block } from '../stores/pages'

const props = defineProps<{ block: Block; isEditing: boolean }>()

const current = ref(0)
const slides = computed(() => (props.block.content.slides as Array<{ url: string; alt: string; caption?: string }>) ?? [])

function next() { current.value = (current.value + 1) % slides.value.length }
function prev() { current.value = (current.value - 1 + slides.value.length) % slides.value.length }

let timer: ReturnType<typeof setInterval> | null = null

onMounted(() => {
  if (props.block.content.autoplay && slides.value.length > 1) {
    timer = setInterval(next, (props.block.content.interval as number) ?? 4000)
  }
})
onUnmounted(() => { if (timer) clearInterval(timer) })
</script>
