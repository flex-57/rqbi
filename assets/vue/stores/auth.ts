import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../composables/api'

interface AuthUser {
  id: number
  email: string
  roles: string[]
}

export const useAuthStore = defineStore('auth', () => {
  const token = ref<string | null>(localStorage.getItem('jwt_token'))
  const user = ref<AuthUser | null>(null)

  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.roles?.includes('ROLE_ADMIN') ?? false)

  function setToken(jwt: string) {
    token.value = jwt
    localStorage.setItem('jwt_token', jwt)
  }

  function logout() {
    token.value = null
    user.value = null
    localStorage.removeItem('jwt_token')
  }

  async function login(email: string, password: string): Promise<void> {
    const res = await api.post('/api/auth/login', { email, password })
    setToken(res.data.token)
    await fetchMe()
  }

  async function fetchMe(): Promise<void> {
    if (!token.value) return
    try {
      const res = await api.get('/api/auth/me')
      user.value = res.data
    } catch {
      logout()
    }
  }

  async function setup(email: string, password: string): Promise<void> {
    await api.post('/api/auth/setup', { email, password })
  }

  return { token, user, isAuthenticated, isAdmin, login, logout, fetchMe, setup }
})
