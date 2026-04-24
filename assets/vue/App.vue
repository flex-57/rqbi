<template>
  <template v-if="isLoginPage">
    <LoginForm />
  </template>
  <template v-else>
    <NavBar :is-editing="isEditing" @toggle-editing="toggleEditing" />
    <main>
      <PageView :is-editing="isEditing" />
    </main>
    <footer class="bg-rqbi-dark text-white py-8 mt-16">
      <div class="max-w-7xl mx-auto px-4 text-center">
        <img src="/images/cropped-logo-banniere-180x180.png" alt="RQBI" class="h-12 mx-auto mb-4 brightness-0 invert" />
        <p class="text-sm text-gray-400">
          © {{ new Date().getFullYear() }} Régie de Quartier Behren Insertion — Tous droits réservés
        </p>
      </div>
    </footer>
  </template>
</template>

<script setup lang="ts">
import { computed, ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from './stores/auth'
import { usePagesStore } from './stores/pages'
import NavBar from './components/NavBar.vue'
import PageView from './components/PageView.vue'
import LoginForm from './components/LoginForm.vue'

const route = useRoute()
const authStore = useAuthStore()
const pagesStore = usePagesStore()
const isEditing = ref(false)

const isLoginPage = computed(() => route.path === '/login')

function toggleEditing() {
  if (!authStore.isAdmin) return
  isEditing.value = !isEditing.value
}

onMounted(async () => {
  await authStore.fetchMe()
  await pagesStore.fetchTree()
})
</script>
