<template>
  <div class="my-8 px-4 max-w-4xl mx-auto">
    <h3 v-if="block.content.title" class="text-xl font-semibold mb-4 text-rqbi-dark">
      {{ block.content.title }}
    </h3>
    <div class="relative w-full" style="padding-top: 56.25%">
      <iframe
        class="absolute inset-0 w-full h-full rounded shadow"
        :src="embedUrl"
        frameborder="0"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        allowfullscreen
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { Block } from '../stores/pages'

const props = defineProps<{ block: Block; isEditing: boolean }>()

const embedUrl = computed(() => {
  const url = props.block.content.url as string
  const provider = props.block.content.provider as string ?? 'youtube'

  if (provider === 'youtube') {
    const match = url.match(/(?:v=|youtu\.be\/)([A-Za-z0-9_-]{11})/)
    return match ? `https://www.youtube.com/embed/${match[1]}` : url
  }
  if (provider === 'vimeo') {
    const match = url.match(/vimeo\.com\/(\d+)/)
    return match ? `https://player.vimeo.com/video/${match[1]}` : url
  }
  return url
})
</script>
