<template>
  <div class="max-w-6xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold text-rqbi-dark mb-2">Nous contacter</h1>
    <p class="text-gray-500 mb-10">Une question, une demande d'information ? Notre équipe vous répond dans les meilleurs délais.</p>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

      <!-- Infos + carte -->
      <div class="space-y-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="flex gap-3 p-4 bg-white rounded-xl shadow-sm border border-gray-100">
            <span class="text-rqbi-red text-xl shrink-0">📍</span>
            <div>
              <p class="font-semibold text-rqbi-dark text-sm mb-1">Adresse</p>
              <p class="text-gray-600 text-sm">Annexe Chateaubriand<br>1 rue de l'École<br>57460 Behren-lès-Forbach</p>
            </div>
          </div>
          <div class="flex gap-3 p-4 bg-white rounded-xl shadow-sm border border-gray-100">
            <span class="text-rqbi-red text-xl shrink-0">📞</span>
            <div>
              <p class="font-semibold text-rqbi-dark text-sm mb-1">Téléphone</p>
              <a href="tel:+33387883985" class="text-rqbi-blue text-sm hover:underline">03 87 88 39 85</a>
            </div>
          </div>
          <div class="flex gap-3 p-4 bg-white rounded-xl shadow-sm border border-gray-100">
            <span class="text-rqbi-red text-xl shrink-0">✉️</span>
            <div>
              <p class="font-semibold text-rqbi-dark text-sm mb-1">Email</p>
              <a href="mailto:secretariat@rqbi.fr" class="text-rqbi-blue text-sm hover:underline break-all">
                secretariat@rqbi.fr
              </a>
            </div>
          </div>
          <div class="flex gap-3 p-4 bg-white rounded-xl shadow-sm border border-gray-100">
            <span class="text-rqbi-red text-xl shrink-0">🕐</span>
            <div>
              <p class="font-semibold text-rqbi-dark text-sm mb-1">Horaires</p>
              <p class="text-gray-600 text-sm">Lun – Ven<br>8h–12h / 14h–17h</p>
            </div>
          </div>
        </div>

        <!-- Carte OpenStreetMap -->
        <div class="rounded-xl overflow-hidden shadow border border-gray-100">
          <iframe
            title="Localisation RQBI"
            width="100%"
            height="300"
            frameborder="0"
            scrolling="no"
            src="https://www.openstreetmap.org/export/embed.html?bbox=6.933%2C49.161%2C6.953%2C49.170&layer=mapnik&marker=49.1654%2C6.9427"
          />
        </div>
      </div>

      <!-- Formulaire -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
        <h2 class="text-xl font-bold text-rqbi-dark mb-6">Envoyer un message</h2>

        <div v-if="success" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
          Votre message a bien été envoyé. Nous vous répondrons dans les meilleurs délais.
        </div>

        <form v-else @submit.prevent="submit" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="form-label">Nom <span class="text-red-500">*</span></label>
              <input v-model="form.name" type="text" class="form-input" :class="{ 'border-red-400': errors.name }" required />
              <p v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name }}</p>
            </div>
            <div>
              <label class="form-label">Email <span class="text-red-500">*</span></label>
              <input v-model="form.email" type="email" class="form-input" :class="{ 'border-red-400': errors.email }" required />
              <p v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email }}</p>
            </div>
          </div>
          <div>
            <label class="form-label">Sujet <span class="text-red-500">*</span></label>
            <input v-model="form.subject" type="text" class="form-input" :class="{ 'border-red-400': errors.subject }" required />
            <p v-if="errors.subject" class="text-red-500 text-xs mt-1">{{ errors.subject }}</p>
          </div>
          <div>
            <label class="form-label">Message <span class="text-red-500">*</span></label>
            <textarea v-model="form.message" rows="6" class="form-input resize-none" :class="{ 'border-red-400': errors.message }" required />
            <p v-if="errors.message" class="text-red-500 text-xs mt-1">{{ errors.message }}</p>
          </div>

          <p v-if="errorGlobal" class="text-red-500 text-sm">{{ errorGlobal }}</p>

          <button type="submit" class="btn-primary w-full" :disabled="sending">
            {{ sending ? 'Envoi en cours...' : 'Envoyer le message' }}
          </button>
        </form>
      </div>

    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'
import api from '../composables/api'
const sending = ref(false)
const success = ref(false)
const errorGlobal = ref('')
const errors = reactive<Record<string, string>>({})

const form = reactive({ name: '', email: '', subject: '', message: '' })

async function submit() {
  Object.keys(errors).forEach(k => delete errors[k])
  errorGlobal.value = ''
  sending.value = true
  try {
    await api.post('/api/contact', { ...form })
    success.value = true
  } catch (e: any) {
    const data = e.response?.data
    if (data?.errors) {
      Object.assign(errors, data.errors)
    } else {
      errorGlobal.value = "Une erreur est survenue, veuillez réessayer."
    }
  } finally {
    sending.value = false
  }
}
</script>

<style scoped>
.form-label { @apply block text-sm font-semibold text-gray-700 mb-1; }
.form-input  { @apply w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rqbi-blue/30 focus:border-rqbi-blue transition-colors; }
</style>
