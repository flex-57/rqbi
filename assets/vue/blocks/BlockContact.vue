<template>
  <section class="py-10 px-4 max-w-5xl mx-auto">
    <h2 v-if="block.content.title" class="text-2xl font-bold text-rqbi-dark mb-8">
      {{ block.content.title }}
    </h2>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

      <!-- Gauche : infos + carte -->
      <div class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div v-if="block.content.address" class="flex gap-3 p-4 bg-white rounded-xl border border-gray-100 shadow-sm">
            <span class="text-rqbi-red text-xl shrink-0">📍</span>
            <div>
              <p class="font-semibold text-rqbi-dark text-sm mb-1">Adresse</p>
              <p class="text-gray-600 text-sm whitespace-pre-line">{{ block.content.address }}</p>
            </div>
          </div>
          <div v-if="block.content.phone" class="flex gap-3 p-4 bg-white rounded-xl border border-gray-100 shadow-sm">
            <span class="text-rqbi-red text-xl shrink-0">📞</span>
            <div>
              <p class="font-semibold text-rqbi-dark text-sm mb-1">Téléphone</p>
              <a :href="'tel:' + block.content.phone" class="text-rqbi-blue text-sm hover:underline">
                {{ block.content.phone }}
              </a>
            </div>
          </div>
          <div v-if="block.content.email" class="flex gap-3 p-4 bg-white rounded-xl border border-gray-100 shadow-sm">
            <span class="text-rqbi-red text-xl shrink-0">✉️</span>
            <div>
              <p class="font-semibold text-rqbi-dark text-sm mb-1">Email</p>
              <a :href="'mailto:' + block.content.email" class="text-rqbi-blue text-sm hover:underline break-all">
                {{ block.content.email }}
              </a>
            </div>
          </div>
          <div v-if="block.content.hours" class="flex gap-3 p-4 bg-white rounded-xl border border-gray-100 shadow-sm">
            <span class="text-rqbi-red text-xl shrink-0">🕐</span>
            <div>
              <p class="font-semibold text-rqbi-dark text-sm mb-1">Horaires</p>
              <p class="text-gray-600 text-sm whitespace-pre-line">{{ block.content.hours }}</p>
            </div>
          </div>
        </div>
        <div
          v-if="block.content.show_map && block.content.lat && block.content.lon"
          class="rounded-xl overflow-hidden shadow border border-gray-100"
        >
          <iframe
            title="Localisation"
            width="100%"
            height="260"
            frameborder="0"
            scrolling="no"
            :src="mapUrl"
          />
        </div>
      </div>

      <!-- Droite : formulaire -->
      <div>
        <div v-if="sent" class="bg-green-50 border border-green-200 rounded-xl p-6 text-center h-full flex flex-col items-center justify-center">
          <p class="text-green-700 font-semibold text-lg mb-1">Message envoyé !</p>
          <p class="text-green-600 text-sm">Nous vous répondrons dans les meilleurs délais.</p>
        </div>
        <form v-else class="space-y-4" @submit.prevent="submit">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Nom <span class="text-rqbi-red">*</span></label>
              <input v-model="form.name" type="text" required class="field" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Email <span class="text-rqbi-red">*</span></label>
              <input v-model="form.email" type="email" required class="field" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Sujet <span class="text-rqbi-red">*</span></label>
            <input v-model="form.subject" type="text" required class="field" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Message <span class="text-rqbi-red">*</span></label>
            <textarea v-model="form.message" rows="5" required class="field resize-none" />
          </div>
          <p v-if="error" class="text-red-500 text-sm">{{ error }}</p>
          <button
            type="submit"
            :disabled="sending || isEditing"
            class="bg-rqbi-red text-white px-6 py-2.5 rounded-lg font-semibold text-sm hover:bg-red-700 transition-colors disabled:opacity-50"
          >
            {{ sending ? 'Envoi en cours…' : 'Envoyer le message' }}
          </button>
        </form>
      </div>

    </div>
  </section>
</template>

<script setup lang="ts">
import { reactive, ref, computed } from 'vue'
import type { Block } from '../stores/pages'
import api from '../composables/api'

const props = defineProps<{ block: Block; isEditing: boolean }>()

const mapUrl = computed(() => {
  const lat = Number(props.block.content.lat)
  const lon = Number(props.block.content.lon)
  const dLat = 0.005, dLon = 0.01
  return `https://www.openstreetmap.org/export/embed.html?bbox=${lon - dLon}%2C${lat - dLat}%2C${lon + dLon}%2C${lat + dLat}&layer=mapnik&marker=${lat}%2C${lon}`
})

const form = reactive({ name: '', email: '', subject: '', message: '' })
const sending = ref(false)
const sent = ref(false)
const error = ref('')

async function submit() {
  sending.value = true
  error.value = ''
  try {
    await api.post('/api/contact', form)
    sent.value = true
  } catch {
    error.value = "Une erreur s'est produite. Veuillez réessayer."
  } finally {
    sending.value = false
  }
}
</script>

<style scoped>
.field { @apply w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rqbi-blue/30 focus:border-rqbi-blue transition-colors; }
</style>
