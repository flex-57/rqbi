<template>
  <section class="relative bg-rqbi-ink text-white py-20 px-6 overflow-hidden">
    <div class="absolute inset-0 pointer-events-none bg-[radial-gradient(circle_at_20%_30%,rgba(208,32,26,0.15),transparent_50%),radial-gradient(circle_at_80%_70%,rgba(37,99,168,0.12),transparent_50%)]" />
    <div class="container-rqbi relative">
      <h2 v-if="block.content.title" class="font-display text-white mb-12 max-w-3xl" v-animate-in>
        {{ block.content.title }}
      </h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
        <Stat
          v-for="(stat, i) in stats" :key="i"
          :value="stat.value"
          :label="stat.label"
          :color="stat.color"
          :delay="(i + 1) as 1 | 2 | 3 | 4"
        />
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { Block } from '../stores/pages'
import Stat from '../components/Stat.vue'

const props = defineProps<{ block: Block; isEditing: boolean }>()
const stats = computed(() => (props.block.content.stats as any[]) ?? [])
</script>
