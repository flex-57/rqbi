import { createRouter, createWebHistory } from 'vue-router'
import PageView from './components/PageView.vue'
import LoginForm from './components/LoginForm.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/login', component: LoginForm },
    { path: '/:slug*', component: PageView, props: true },
  ],
  scrollBehavior: () => ({ top: 0 }),
})

export default router
