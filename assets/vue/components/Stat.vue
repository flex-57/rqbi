<template>
  <div ref="elRef" v-animate-in="{ variant: 'scale', delay }" class="text-left pl-5 border-l-2 border-white/15 hover:border-rqbi-red transition-colors">
    <span class="block font-display font-normal text-[clamp(2.5rem,5vw,4rem)] leading-none mb-3 text-white">
      {{ display }}
    </span>
    <span class="block text-sm text-white/70 leading-snug">{{ label }}</span>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps<{
  value: string | number
  label: string
  color?: string
  delay?: 1 | 2 | 3 | 4
}>()

const parsed = computed(() => {
  const s = String(props.value)
  const match = s.match(/^(\d+)(.*)$/)
  if (!match) return { num: null as number | null, suffix: s }
  return { num: Number(match[1]), suffix: match[2] }
})

const elRef = ref<HTMLElement | null>(null)
const animated = ref(0)
let started = false

onMounted(() => {
  if (!elRef.value || parsed.value.num === null) return
  const target = parsed.value.num
  const io = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting && !started) {
      started = true
      const start = performance.now()
      const tick = (now: number) => {
        const t = Math.min((now - start) / 1600, 1)
        const eased = 1 - Math.pow(1 - t, 3)
        animated.value = Math.round(target * eased)
        if (t < 1) requestAnimationFrame(tick)
      }
      requestAnimationFrame(tick)
    }
  }, { threshold: 0.4 })
  io.observe(elRef.value)
  onBeforeUnmount(() => io.disconnect())
})

const display = computed(() => {
  if (parsed.value.num === null) return parsed.value.suffix
  return `${animated.value}${parsed.value.suffix}`
})
</script>
