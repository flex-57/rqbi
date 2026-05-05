<template>
  <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-6">
      <div class="flex items-center justify-between mb-5">
        <h3 class="text-lg font-bold text-rqbi-dark">Changer le mot de passe</h3>
        <button class="text-gray-400 hover:text-gray-600 text-xl leading-none" @click="emit('close')">×</button>
      </div>

      <div v-if="success" class="text-center py-4">
        <div class="text-3xl mb-3">✓</div>
        <p class="text-green-600 font-medium">Mot de passe modifié avec succès</p>
      </div>

      <form v-else @submit.prevent="submit">
        <div class="flex flex-col gap-4">
          <div>
            <label class="block text-sm font-medium text-rqbi-dark mb-1">Mot de passe actuel</label>
            <input
              v-model="form.current_password"
              type="password"
              autocomplete="current-password"
              class="w-full border border-rqbi-line rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rqbi-blue"
              :class="{ 'border-rqbi-red': error }"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-rqbi-dark mb-1">Nouveau mot de passe</label>
            <input
              v-model="form.new_password"
              type="password"
              autocomplete="new-password"
              class="w-full border border-rqbi-line rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rqbi-blue"
              :class="{ 'border-rqbi-red': error }"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-rqbi-dark mb-1">Confirmer le nouveau mot de passe</label>
            <input
              v-model="form.new_password_confirmation"
              type="password"
              autocomplete="new-password"
              class="w-full border border-rqbi-line rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rqbi-blue"
              :class="{ 'border-rqbi-red': error }"
            />
          </div>

          <p v-if="error" class="text-rqbi-red text-sm">{{ error }}</p>

          <div class="flex justify-end gap-3 mt-2">
            <button type="button" class="btn-ghost" @click="emit('close')">Annuler</button>
            <button type="submit" class="btn-primary" :disabled="loading">
              {{ loading ? 'Enregistrement...' : 'Enregistrer' }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import api from '../composables/api'

const emit = defineEmits<{ close: [] }>()

const form = ref({ current_password: '', new_password: '', new_password_confirmation: '' })
const error = ref('')
const loading = ref(false)
const success = ref(false)

async function submit() {
  error.value = ''

  if (!form.value.current_password) {
    error.value = 'Le mot de passe actuel est obligatoire'
    return
  }
  if (!form.value.new_password) {
    error.value = 'Le nouveau mot de passe est obligatoire'
    return
  }
  if (form.value.new_password.length < 8) {
    error.value = 'Le mot de passe doit contenir au moins 8 caractères'
    return
  }
  if (form.value.new_password !== form.value.new_password_confirmation) {
    error.value = 'Les mots de passe ne correspondent pas'
    return
  }

  loading.value = true
  try {
    await api.post('/api/auth/change-password', form.value)
    success.value = true
    setTimeout(() => emit('close'), 1500)
  } catch (e: any) {
    error.value = e.response?.data?.error ?? 'Une erreur est survenue'
  } finally {
    loading.value = false
  }
}
</script>
