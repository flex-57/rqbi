import type { Directive } from 'vue'

export const vAnimateIn: Directive<HTMLElement, string | { variant?: 'default' | 'scale'; delay?: 1 | 2 | 3 | 4 } | undefined> = {
  mounted(el, binding) {
    const opt = typeof binding.value === 'string'
      ? { variant: binding.value as 'scale' }
      : (binding.value ?? {})

    const variant = opt.variant ?? 'default'
    const delay = opt.delay

    el.classList.add(variant === 'scale' ? 'rv-scale' : 'rv')
    if (delay) el.classList.add(`rv-d${delay}`)

    const io = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            el.classList.add('in')
            io.unobserve(el)
          }
        })
      },
      { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
    )

    io.observe(el)
    ;(el as any).__animateInObserver = io
  },
  unmounted(el) {
    const io = (el as any).__animateInObserver as IntersectionObserver | undefined
    io?.disconnect()
  },
}
