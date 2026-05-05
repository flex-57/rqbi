<template>
  <section class="py-12">
    <div class="container-rqbi max-w-2xl">
      <div v-if="sent" class="text-center py-8">
        <div class="w-16 h-16 rounded-full bg-rqbi-red-soft text-rqbi-red mx-auto mb-6 flex items-center justify-center text-2xl">✓</div>
        <h3 class="font-display text-2xl font-medium mb-2">Message envoyé !</h3>
        <p class="text-rqbi-ink-mute">Nous vous répondrons dans les meilleurs délais.</p>
      </div>

      <form v-else class="bg-white border border-rqbi-line rounded-2xl p-10 space-y-4" @submit.prevent="submit" v-animate-in>
        <h2 v-if="block.content.title" class="font-display text-2xl font-medium mb-6">{{ block.content.title }}</h2>
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
          {{ sending ? 'Envoi en cours…' : String(block.content.submit_label || 'Envoyer') }}
        </button>
      </form>
    </div>
  </section>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'
import type { Block } from '../stores/pages'
import api from '../composables/api'

const props = defineProps<{ block: Block; isEditing: boolean }>()

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
