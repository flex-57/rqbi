<template>
  <div class="container-rqbi py-20">
    <h1 class="font-display text-[clamp(2.5rem,5vw,4.5rem)] leading-[1.02] tracking-tightest font-medium mb-3" v-animate-in>
      Une question&nbsp;? <em class="italic text-rqbi-red font-normal">Écrivez-nous.</em>
    </h1>
    <p class="text-rqbi-ink-mute text-lg max-w-xl mb-12" v-animate-in="{ delay: 1 }">
      Notre équipe vous répond sous 48h, du lundi au vendredi.
    </p>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
      <!-- Infos -->
      <div class="space-y-6" v-animate-in>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="bg-white border border-rqbi-line rounded-xl p-5 hover:-translate-y-1 hover:border-rqbi-red transition-all">
            <p class="font-mono text-[0.7rem] tracking-widest uppercase text-rqbi-ink-mute mb-1.5">Adresse</p>
            <p class="text-sm font-medium text-rqbi-ink leading-snug">Annexe Chateaubriand<br/>1 rue de l'École<br/>57460 Behren-lès-Forbach</p>
          </div>
          <div class="bg-white border border-rqbi-line rounded-xl p-5 hover:-translate-y-1 hover:border-rqbi-red transition-all">
            <p class="font-mono text-[0.7rem] tracking-widest uppercase text-rqbi-ink-mute mb-1.5">Téléphone</p>
            <a href="tel:+33387883985" class="text-sm font-medium text-rqbi-blue hover:underline">03 87 88 39 85</a>
          </div>
          <div class="bg-white border border-rqbi-line rounded-xl p-5 hover:-translate-y-1 hover:border-rqbi-red transition-all">
            <p class="font-mono text-[0.7rem] tracking-widest uppercase text-rqbi-ink-mute mb-1.5">Email</p>
            <a href="mailto:secretariat@rqbi.fr" class="text-sm font-medium text-rqbi-blue hover:underline break-all">secretariat@rqbi.fr</a>
          </div>
          <div class="bg-white border border-rqbi-line rounded-xl p-5 hover:-translate-y-1 hover:border-rqbi-red transition-all">
            <p class="font-mono text-[0.7rem] tracking-widest uppercase text-rqbi-ink-mute mb-1.5">Horaires</p>
            <p class="text-sm font-medium text-rqbi-ink">Lun – Ven<br/>8h–12h / 14h–17h</p>
          </div>
        </div>

        <div class="rounded-2xl overflow-hidden border border-rqbi-line h-72">
          <iframe
            title="Localisation RQBI"
            width="100%" height="100%" frameborder="0" scrolling="no"
            src="https://www.openstreetmap.org/export/embed.html?bbox=6.933%2C49.161%2C6.953%2C49.170&layer=mapnik&marker=49.1654%2C6.9427"
          />
        </div>
      </div>

      <!-- Formulaire -->
      <div class="bg-white border border-rqbi-line rounded-2xl p-10 self-start" v-animate-in="{ delay: 1 }">
        <div v-if="success" class="text-center py-8">
          <div class="w-16 h-16 rounded-full bg-rqbi-red-soft text-rqbi-red mx-auto mb-6 flex items-center justify-center text-2xl">✓</div>
          <h3 class="font-display text-2xl font-medium mb-2">Message envoyé !</h3>
          <p class="text-rqbi-ink-mute">Nous vous répondrons dans les meilleurs délais.</p>
        </div>

        <form v-else @submit.prevent="submit" class="space-y-4">
          <h2 class="text-2xl font-medium mb-6">Envoyer un message</h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="form-label">Nom *</label>
              <input v-model="form.name" type="text" class="form-input" :class="{ 'border-rqbi-red': errors.name }" required />
              <p v-if="errors.name" class="text-rqbi-red text-xs mt-1">{{ errors.name }}</p>
            </div>
            <div>
              <label class="form-label">Email *</label>
              <input v-model="form.email" type="email" class="form-input" :class="{ 'border-rqbi-red': errors.email }" required />
              <p v-if="errors.email" class="text-rqbi-red text-xs mt-1">{{ errors.email }}</p>
            </div>
          </div>
          <div>
            <label class="form-label">Sujet *</label>
            <input v-model="form.subject" type="text" class="form-input" :class="{ 'border-rqbi-red': errors.subject }" required />
            <p v-if="errors.subject" class="text-rqbi-red text-xs mt-1">{{ errors.subject }}</p>
          </div>
          <div>
            <label class="form-label">Message *</label>
            <textarea v-model="form.message" class="form-textarea" :class="{ 'border-rqbi-red': errors.message }" required />
            <p v-if="errors.message" class="text-rqbi-red text-xs mt-1">{{ errors.message }}</p>
          </div>
          <p v-if="errorGlobal" class="text-rqbi-red text-sm">{{ errorGlobal }}</p>
          <button type="submit" class="btn-primary w-full justify-center" :disabled="sending">
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
