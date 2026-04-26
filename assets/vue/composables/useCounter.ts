import { ref, onMounted, onBeforeUnmount } from 'vue'

export function useCounter(target: number, duration = 1600) {
  const value = ref(0)
  const elRef = ref<HTMLElement | null>(null)
  let started = false
  let io: IntersectionObserver | null = null

  onMounted(() => {
    if (!elRef.value) return
    io = new IntersectionObserver((entries) => {
      if (entries[0].isIntersecting && !started) {
        started = true
        const start = performance.now()
        const tick = (now: number) => {
          const t = Math.min((now - start) / duration, 1)
          const eased = 1 - Math.pow(1 - t, 3)
          value.value = Math.round(target * eased)
          if (t < 1) requestAnimationFrame(tick)
        }
        requestAnimationFrame(tick)
      }
    }, { threshold: 0.4 })
    io.observe(elRef.value)
  })

  onBeforeUnmount(() => {
    io?.disconnect()
  })

  return { value, ref: elRef }
}
