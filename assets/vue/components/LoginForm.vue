<template>
  <div class="min-h-screen flex items-center justify-center bg-rqbi-light px-4">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-sm p-8">
      <div class="text-center mb-8">
        <img src="/images/cropped-logo-banniere-512x193.png" alt="RQBI" class="h-14 mx-auto mb-4" />
        <h1 class="text-xl font-bold text-rqbi-dark">Espace administrateur</h1>
      </div>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="form-label">Email</label>
          <input
            v-model="email"
            type="email"
            class="form-input"
            placeholder="admin@rqbi.fr"
            autocomplete="email"
            required
          />
        </div>
        <div>
          <label class="form-label">Mot de passe</label>
          <input
            v-model="password"
            type="password"
            class="form-input"
            autocomplete="current-password"
            required
          />
        </div>

        <p v-if="error" class="text-red-500 text-sm text-center">{{ error }}</p>

        <button type="submit" class="btn-primary w-full text-center" :disabled="loading">
          {{ loading ? 'Connexion...' : 'Se connecter' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
const router = useRouter()

const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)

async function submit() {
  loading.value = true
  error.value = ''
  try {
    await authStore.login(email.value, password.value)
    router.push('/accueil')
  } catch {
    error.value = 'Identifiants incorrects'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.form-label { @apply block text-sm font-semibold text-gray-700 mb-1; }
.form-input { @apply w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rqbi-blue/30 focus:border-rqbi-blue; }
</style>
