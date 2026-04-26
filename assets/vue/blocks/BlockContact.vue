<template>
  <section class="py-20 container-rqbi">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
      <!-- Infos -->
      <div class="space-y-6" v-animate-in>
        <h2 v-if="block.content.title">{{ block.content.title }}</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div v-if="block.content.address" class="bg-white border border-rqbi-line rounded-xl p-5 hover:-translate-y-1 hover:border-rqbi-red transition-all">
            <p class="font-mono text-[0.7rem] tracking-widest uppercase text-rqbi-ink-mute mb-1.5">Adresse</p>
            <p class="text-sm font-medium text-rqbi-ink leading-snug whitespace-pre-line">{{ block.content.address }}</p>
          </div>
          <div v-if="block.content.phone" class="bg-white border border-rqbi-line rounded-xl p-5 hover:-translate-y-1 hover:border-rqbi-red transition-all">
            <p class="font-mono text-[0.7rem] tracking-widest uppercase text-rqbi-ink-mute mb-1.5">Téléphone</p>
            <a :href="'tel:' + (block.content.phone as string)" class="text-sm font-medium text-rqbi-blue hover:underline">{{ block.content.phone }}</a>
          </div>
          <div v-if="block.content.email" class="bg-white border border-rqbi-line rounded-xl p-5 hover:-translate-y-1 hover:border-rqbi-red transition-all">
            <p class="font-mono text-[0.7rem] tracking-widest uppercase text-rqbi-ink-mute mb-1.5">Email</p>
            <a :href="'mailto:' + (block.content.email as string)" class="text-sm font-medium text-rqbi-blue hover:underline break-all">{{ block.content.email }}</a>
          </div>
          <div v-if="block.content.hours" class="bg-white border border-rqbi-line rounded-xl p-5 hover:-translate-y-1 hover:border-rqbi-red transition-all">
            <p class="font-mono text-[0.7rem] tracking-widest uppercase text-rqbi-ink-mute mb-1.5">Horaires</p>
            <p class="text-sm font-medium text-rqbi-ink whitespace-pre-line">{{ block.content.hours }}</p>
          </div>
        </div>

        <div
          v-if="block.content.show_map && block.content.lat && block.content.lon"
          class="rounded-2xl overflow-hidden border border-rqbi-line h-72"
        >
          <iframe
            title="Localisation"
            width="100%" height="100%" frameborder="0" scrolling="no"
            :src="mapUrl"
          />
        </div>
      </div>

      <!-- Formulaire -->
      <div class="bg-white border border-rqbi-line rounded-2xl p-10 self-start" v-animate-in="{ delay: 1 }">
        <div v-if="sent" class="text-center py-8">
          <div class="w-16 h-16 rounded-full bg-rqbi-red-soft text-rqbi-red mx-auto mb-6 flex items-center justify-center text-2xl">✓</div>
          <h3 class="font-display text-2xl font-medium mb-2">Message envoyé !</h3>
          <p class="text-rqbi-ink-mute">Nous vous répondrons dans les meilleurs délais.</p>
        </div>

        <form v-else class="space-y-4" @submit.prevent="submit">
          <h3 class="font-display text-2xl font-medium mb-6">Envoyer un message</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="form-label">Nom *</label>
              <input v-model="form.name" type="text" required class="form-input" />
            </div>
            <div>
              <label class="form-label">Email *</label>
              <input v-model="form.email" type="email" required class="form-input" />
            </div>
          </div>
          <div>
            <label class="form-label">Sujet *</label>
            <input v-model="form.subject" type="text" required class="form-input" />
          </div>
          <div>
            <label class="form-label">Message *</label>
            <textarea v-model="form.message" required class="form-textarea" />
          </div>
          <p v-if="error" class="text-rqbi-red text-sm">{{ error }}</p>
          <button
            type="submit"
            :disabled="sending || isEditing"
            class="btn-primary w-full justify-center"
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
