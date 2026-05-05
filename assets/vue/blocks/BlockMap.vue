<template>
  <section class="py-12">
    <div class="container-rqbi">
      <h2 v-if="block.content.title" class="text-rqbi-ink mb-6" v-animate-in>
        {{ block.content.title }}
      </h2>
      <div
        class="rounded-2xl overflow-hidden border border-rqbi-line"
        :style="{ height: (Number(block.content.height) || 400) + 'px' }"
        v-animate-in
      >
        <iframe
          title="Carte"
          width="100%"
          height="100%"
          frameborder="0"
          scrolling="no"
          :src="mapUrl"
        />
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { Block } from '../stores/pages'

const props = defineProps<{ block: Block; isEditing: boolean }>()

const mapUrl = computed(() => {
  const lat = Number(props.block.content.lat)
  const lon = Number(props.block.content.lon)
  const dLat = 0.005, dLon = 0.01
  return `https://www.openstreetmap.org/export/embed.html?bbox=${lon - dLon}%2C${lat - dLat}%2C${lon + dLon}%2C${lat + dLat}&layer=mapnik&marker=${lat}%2C${lon}`
})
</script>
