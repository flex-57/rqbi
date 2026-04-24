<template>
  <section class="py-10 px-4 max-w-5xl mx-auto">
    <h2 v-if="block.content.title" class="text-2xl font-bold text-rqbi-dark mb-8">
      {{ block.content.title }}
    </h2>
    <div class="space-y-2">
      <div
        v-for="(item, i) in items" :key="i"
        v-animate-in
        class="border border-gray-200 rounded-xl overflow-hidden"
      >
        <button
          class="w-full flex items-center justify-between px-6 py-4 text-left font-medium text-rqbi-dark hover:bg-gray-50 transition-colors"
          @click="toggle(i)"
        >
          <span>{{ item.question }}</span>
          <span class="text-rqbi-red text-xl transition-transform duration-200 shrink-0 ml-4"
            :class="open === i ? 'rotate-45' : ''">+</span>
        </button>
        <div v-if="open === i" class="px-6 pb-4 text-gray-600 text-sm leading-relaxed border-t border-gray-100">
          <p class="pt-3">{{ item.answer }}</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import type { Block } from '../stores/pages'

const props = defineProps<{ block: Block; isEditing: boolean }>()
const items = computed(() => (props.block.content.items as any[]) ?? [])
const open = ref<number | null>(null)
function toggle(i: number) { open.value = open.value === i ? null : i }
</script>
