<template>
  <template v-if="isLoginPage">
    <LoginForm />
  </template>
  <template v-else>
    <NavBar :is-editing="isEditing" @toggle-editing="toggleEditing" />

    <main class="min-h-[60vh]">
      <PageView :is-editing="isEditing" />
    </main>

    <footer class="bg-rqbi-ink text-white/70 pt-20 pb-8 mt-20">
      <div class="container-rqbi">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1.5fr_1fr_1fr_1fr] gap-12 pb-12 border-b border-white/10">

          <!-- Brand -->
          <div>
            <img src="/images/cropped-logo-banniere-512x193.png" alt="RQBI" class="h-14 brightness-0 invert mb-5" />
            <p class="text-sm leading-relaxed max-w-[320px]">
              Association d'insertion par l'activité économique, accompagnant les personnes
              éloignées de l'emploi vers un retour à l'activité professionnelle, depuis 2003.
            </p>
          </div>

          <!-- Navigation -->
          <div>
            <h4 class="text-white font-mono text-[0.72rem] font-medium tracking-[0.12em] uppercase mb-5">Navigation</h4>
            <ul class="flex flex-col gap-3">
              <li v-for="page in pagesStore.tree" :key="page.id">
                <RouterLink
                  :to="'/' + page.slug"
                  class="text-sm text-white/65 hover:text-white inline-flex items-center gap-2 transition-all hover:pl-1 group"
                >
                  <span class="text-rqbi-red opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                  {{ page.title }}
                </RouterLink>
              </li>
            </ul>
          </div>

          <!-- Activités -->
          <div>
            <h4 class="text-white font-mono text-[0.72rem] font-medium tracking-[0.12em] uppercase mb-5">Activités</h4>
            <ul class="flex flex-col gap-3">
              <li v-for="a in ['Espaces verts', 'Blanchisserie', 'Nettoyage', 'Agriculture']" :key="a">
                <span class="text-sm text-white/65">{{ a }}</span>
              </li>
            </ul>
          </div>

          <!-- Contact -->
          <div>
            <h4 class="text-white font-mono text-[0.72rem] font-medium tracking-[0.12em] uppercase mb-5">Contact</h4>
            <ul class="flex flex-col gap-3 text-sm">
              <li><a href="tel:+33387883985" class="text-white/65 hover:text-white transition-colors">03 87 88 39 85</a></li>
              <li><a href="mailto:secretariat@rqbi.fr" class="text-white/65 hover:text-white transition-colors">secretariat@rqbi.fr</a></li>
              <li class="text-white/65">1 rue de l'École<br/>57460 Behren-lès-Forbach</li>
            </ul>
          </div>
        </div>

        <div class="pt-8 flex justify-between items-center flex-wrap gap-4 text-xs text-white/45">
          <span>© {{ year }} Régie de Quartier Behren Insertion — Tous droits réservés</span>
          <div class="flex gap-2">
            <a href="https://www.instagram.com/regiedequartierbehren/" target="_blank" rel="noopener"
              class="w-9 h-9 rounded-full bg-white/[0.06] flex items-center justify-center hover:bg-rqbi-red transition-colors"
              aria-label="Instagram">
              <svg class="w-4 h-4 fill-white" viewBox="0 0 24 24"><path d="M12 2.16c3.2 0 3.58.01 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.42.36 1.06.41 2.23.06 1.27.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.42.16-1.06.36-2.23.41-1.27.06-1.65.07-4.85.07s-3.58-.01-4.85-.07c-1.17-.05-1.8-.25-2.23-.41a3.7 3.7 0 0 1-1.38-.9 3.7 3.7 0 0 1-.9-1.38c-.16-.42-.36-1.06-.41-2.23-.06-1.27-.07-1.65-.07-4.85s.01-3.58.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.42-.16 1.06-.36 2.23-.41 1.27-.06 1.65-.07 4.85-.07M12 0C8.74 0 8.33.01 7.05.07 5.78.13 4.9.33 4.14.63a5.9 5.9 0 0 0-2.13 1.38A5.9 5.9 0 0 0 .63 4.14C.33 4.9.13 5.78.07 7.05.01 8.33 0 8.74 0 12s.01 3.67.07 4.95c.06 1.27.26 2.15.56 2.91.32.8.74 1.48 1.38 2.13a5.9 5.9 0 0 0 2.13 1.38c.76.3 1.64.5 2.91.56C8.33 23.99 8.74 24 12 24s3.67-.01 4.95-.07c1.27-.06 2.15-.26 2.91-.56a5.9 5.9 0 0 0 2.13-1.38 5.9 5.9 0 0 0 1.38-2.13c.3-.76.5-1.64.56-2.91.06-1.28.07-1.69.07-4.95s-.01-3.67-.07-4.95c-.06-1.27-.26-2.15-.56-2.91a5.9 5.9 0 0 0-1.38-2.13A5.9 5.9 0 0 0 19.86.63C19.1.33 18.22.13 16.95.07 15.67.01 15.26 0 12 0zm0 5.84A6.16 6.16 0 1 0 12 18.16 6.16 6.16 0 0 0 12 5.84zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.4-11.85a1.44 1.44 0 1 0 0 2.88 1.44 1.44 0 0 0 0-2.88z"/></svg>
            </a>
          </div>
        </div>
      </div>
    </footer>
  </template>
</template>

<script setup lang="ts">
import { computed, ref, watch, onMounted } from 'vue'
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
const year = new Date().getFullYear()

const isLoginPage = computed(() => route.path === '/login')

function toggleEditing() {
  if (!authStore.isAdmin) return
  isEditing.value = !isEditing.value
}

watch(() => authStore.isAdmin, (isAdmin) => {
  if (!isAdmin) isEditing.value = false
})

onMounted(async () => {
  await authStore.fetchMe()
  await pagesStore.fetchTree()
})
</script>
