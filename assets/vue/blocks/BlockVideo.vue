<template>
  <section class="py-12 container-rqbi-narrow" v-animate-in>
    <h3 v-if="block.content.title" class="font-display text-2xl font-medium mb-6 text-rqbi-ink">
      {{ block.content.title }}
    </h3>
    <div class="rounded-2xl overflow-hidden border border-rqbi-line shadow-rqbi-md aspect-video bg-rqbi-ink">
      <video
        v-if="isNativeVideo"
        :src="(block.content.url as string)"
        controls
        class="w-full h-full object-cover"
      />
      <iframe
        v-else
        :src="embedUrl"
        class="w-full h-full"
        frameborder="0"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        allowfullscreen
      />
    </div>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { Block } from '../stores/pages'

const props = defineProps<{ block: Block; isEditing: boolean }>()

const isNativeVideo = computed(() =>
  /\.(mp4|webm)$/i.test(props.block.content.url as string ?? '')
)

const embedUrl = computed(() => {
  const url = props.block.content.url as string
  const provider = (props.block.content.provider as string) ?? 'youtube'

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
