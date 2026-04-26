import './styles/app.css'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './vue/App.vue'
import router from './vue/router'
import { vAnimateIn } from './vue/directives/animateIn'

const app = createApp(App)
app.use(createPinia())
app.use(router)
app.directive('animate-in', vAnimateIn)
app.mount('#app')
