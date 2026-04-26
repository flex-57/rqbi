<template>
  <div class="py-16 max-w-5xl mx-auto px-6" v-animate-in>
    <div class="relative w-full overflow-hidden bg-rqbi-ink rounded-2xl shadow-rqbi-lg" style="min-height: 360px;">
      <div
        class="flex transition-transform duration-700 ease-out h-full"
        :style="{ transform: `translateX(-${current * 100}%)` }"
      >
        <div
          v-for="(slide, i) in slides"
          :key="i"
          class="min-w-full relative"
        >
          <img :src="slide.url" :alt="slide.alt" class="w-full h-96 object-cover" />
          <div v-if="slide.caption" class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent text-white pt-12 pb-5 px-6 font-display text-lg">
            {{ slide.caption }}
          </div>
        </div>
      </div>

      <button
        v-if="slides.length > 1"
        class="absolute left-4 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/95 hover:bg-white hover:scale-110 transition-all shadow-rqbi-md flex items-center justify-center text-rqbi-ink"
        @click="prev"
        aria-label="Précédent"
      >‹</button>
      <button
        v-if="slides.length > 1"
        class="absolute right-4 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/95 hover:bg-white hover:scale-110 transition-all shadow-rqbi-md flex items-center justify-center text-rqbi-ink"
        @click="next"
        aria-label="Suivant"
      >›</button>

      <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-10">
        <button
          v-for="(_, i) in slides"
          :key="i"
          class="h-1.5 rounded-full transition-all"
          :class="i === current ? 'bg-white w-8' : 'bg-white/50 w-1.5 hover:bg-white/80'"
          :aria-label="`Aller à la diapo ${i + 1}`"
          @click="current = i"
        />
      </div>
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
