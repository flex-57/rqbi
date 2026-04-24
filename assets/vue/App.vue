<template>
  <template v-if="isLoginPage">
    <LoginForm />
  </template>
  <template v-else>
    <!-- Barre d'accent rouge -->
    <div class="h-1 bg-rqbi-red w-full" />

    <NavBar :is-editing="isEditing" @toggle-editing="toggleEditing" />
    <main>
      <PageView :is-editing="isEditing" />
    </main>

    <footer class="bg-rqbi-dark text-white mt-16 border-t-4 border-rqbi-red">
      <div class="max-w-7xl mx-auto px-6 pt-12 pb-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">

        <!-- Colonne 1 : logo + description -->
        <div>
          <img src="/images/cropped-logo-banniere-512x193.png" alt="RQBI" class="h-10 w-auto mb-5 brightness-0 invert opacity-90" />
          <p class="text-gray-400 text-sm leading-relaxed">
            Association d'insertion par l'activité économique, accompagnant les personnes éloignées de l'emploi vers un retour à l'activité professionnelle.
          </p>
          <div class="flex gap-3 mt-5">
            <a href="https://www.instagram.com/regiedequartierbehren/" target="_blank" rel="noopener"
              class="w-8 h-8 rounded-full bg-white/10 hover:bg-rqbi-red flex items-center justify-center text-sm transition-colors">
              📸
            </a>
          </div>
        </div>

        <!-- Colonne 2 : navigation -->
        <div>
          <h3 class="text-white text-xs font-bold uppercase tracking-widest mb-5">Navigation</h3>
          <ul class="space-y-2.5">
            <li v-for="page in pagesStore.tree" :key="page.id">
              <RouterLink :to="'/' + page.slug"
                class="text-gray-400 hover:text-white text-sm transition-colors flex items-center gap-2 group">
                <span class="w-1 h-1 rounded-full bg-rqbi-red/50 group-hover:bg-rqbi-red transition-colors shrink-0" />
                {{ page.title }}
              </RouterLink>
            </li>
          </ul>
        </div>

        <!-- Colonne 3 : coordonnées -->
        <div>
          <h3 class="text-white text-xs font-bold uppercase tracking-widest mb-5">Coordonnées</h3>
          <ul class="space-y-3 text-gray-400 text-sm">
            <li class="flex gap-3 items-start">
              <span class="shrink-0 mt-0.5 text-rqbi-red">📍</span>
              <span>Annexe Chateaubriand, 1 rue de l'École<br>57460 Behren-lès-Forbach</span>
            </li>
            <li class="flex gap-3 items-center">
              <span class="shrink-0 text-rqbi-red">📞</span>
              <a href="tel:+33387883985" class="hover:text-white transition-colors">03 87 88 39 85</a>
            </li>
            <li class="flex gap-3 items-center">
              <span class="shrink-0 text-rqbi-red">✉️</span>
              <a href="mailto:secretariat@rqbi.fr" class="hover:text-white transition-colors break-all">
                secretariat@rqbi.fr
              </a>
            </li>
            <li class="flex gap-3 items-center">
              <span class="shrink-0 text-rqbi-red">🕐</span>
              <span>Lun – Ven : 8h–12h et 13h–16h</span>
            </li>
          </ul>
        </div>

      </div>

      <div class="border-t border-white/10 py-4">
        <p class="text-center text-xs text-gray-600">
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
