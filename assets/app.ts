import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './vue/App.vue'
import router from './vue/router'
import './styles/app.css'

const app = createApp(App)
app.use(createPinia())
app.use(router)

app.directive('animate-in', {
  mounted(el: HTMLElement, binding) {
    const type = binding.value || 'fade-up'
    if (type === 'scale') {
      el.style.opacity = '0'
      el.style.transform = 'scale(0.94)'
      el.style.transition = 'opacity 0.7s ease-out, transform 0.7s ease-out'
    } else {
      el.style.opacity = '0'
      el.style.transform = 'translateY(20px)'
      el.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out'
    }
    const observer = new IntersectionObserver(([entry]) => {
      if (entry.isIntersecting) {
        el.style.opacity = '1'
        el.style.transform = type === 'scale' ? 'scale(1)' : 'translateY(0)'
        observer.disconnect()
      }
    }, { threshold: 0.08 })
    observer.observe(el)
  },
})

app.mount('#app')
