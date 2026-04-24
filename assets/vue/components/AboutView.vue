<template>
  <div>
    <!-- Bandeau titre -->
    <div class="max-w-4xl mx-auto px-4 pt-10 pb-4">
      <h1
        class="text-3xl md:text-4xl font-bold text-rqbi-red mb-2 transition-all duration-700 ease-out"
        :class="visible ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-8'"
      >
        Qui sommes-nous ?
      </h1>
    </div>

    <!-- Intro -->
    <section class="py-10 px-4 max-w-4xl mx-auto">
      <p class="text-lg text-gray-700 leading-relaxed">
        Créée le <strong>4 mars 2003</strong> à Behren-lès-Forbach, la <strong>Régie de Quartier Behren Insertion (RQBI)</strong>
        est une association d'<strong>Insertion par l'Activité Économique (IAE)</strong> labellisée par le
        Centre National de Liaison des Régies de Quartier (CNLRQ). Ancrée au cœur d'un quartier
        prioritaire de la politique de la ville, elle accompagne les habitants éloignés de l'emploi
        vers un retour durable à l'activité professionnelle.
      </p>
    </section>

    <!-- Chiffres clés -->
    <section class="bg-rqbi-dark py-12 px-4">
      <div class="max-w-4xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-6 text-center">
        <div v-animate-in>
          <p class="text-4xl font-bold text-rqbi-red mb-1">2003</p>
          <p class="text-white text-sm">Année de création</p>
        </div>
        <div v-animate-in>
          <p class="text-4xl font-bold text-rqbi-red mb-1">100+</p>
          <p class="text-white text-sm">Salariés accompagnés</p>
        </div>
        <div v-animate-in>
          <p class="text-4xl font-bold text-rqbi-red mb-1">4</p>
          <p class="text-white text-sm">Secteurs d'activité</p>
        </div>
      </div>
    </section>

    <!-- Mission — 3 piliers -->
    <section class="py-10 px-4 max-w-4xl mx-auto">
      <h2 class="text-2xl font-bold text-rqbi-dark mb-8">Notre mission</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div v-animate-in class="border-t-4 border-rqbi-red pt-4">
          <h3 class="font-bold text-rqbi-dark mb-2">Approche sociale</h3>
          <p class="text-gray-600 text-sm leading-relaxed">
            Créer du lien social, offrir un emploi aux habitants en difficulté,
            assurer un accompagnement socioprofessionnel et construire des parcours
            de formation adaptés au marché du travail.
          </p>
        </div>
        <div v-animate-in class="border-t-4 border-rqbi-blue pt-4">
          <h3 class="font-bold text-rqbi-dark mb-2">Approche économique</h3>
          <p class="text-gray-600 text-sm leading-relaxed">
            Répondre aux besoins collectifs du territoire, développer des partenariats,
            et réinjecter les flux d'activité dans le territoire en circuits courts.
          </p>
        </div>
        <div v-animate-in class="border-t-4 border-rqbi-dark pt-4">
          <h3 class="font-bold text-rqbi-dark mb-2">Approche citoyenne</h3>
          <p class="text-gray-600 text-sm leading-relaxed">
            Renforcer la participation, l'implication et la responsabilité des habitants
            pour améliorer durablement la vie du quartier.
          </p>
        </div>
      </div>
    </section>

    <!-- Activités -->
    <section class="py-10 px-4 max-w-4xl mx-auto">
      <h2 class="text-2xl font-bold text-rqbi-dark mb-8">Nos activités</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div v-for="activity in activities" :key="activity.title" v-animate-in="'scale'"
          class="flex gap-4 p-5 bg-white rounded-xl border border-gray-100 shadow-sm">
          <span class="text-3xl shrink-0">{{ activity.icon }}</span>
          <div>
            <h3 class="font-bold text-rqbi-dark mb-1">{{ activity.title }}</h3>
            <p class="text-gray-600 text-sm leading-relaxed">{{ activity.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Valeurs -->
    <section class="bg-gray-50 py-10 px-4">
      <div class="max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold text-rqbi-dark mb-8">Nos valeurs</h2>
        <div class="flex flex-wrap gap-3">
          <span v-for="value in values" :key="value"
            class="px-4 py-2 bg-white border border-gray-200 rounded-full text-sm font-medium text-rqbi-dark shadow-sm">
            {{ value }}
          </span>
        </div>
      </div>
    </section>

    <!-- Historique -->
    <section class="py-10 px-4 max-w-4xl mx-auto">
      <h2 class="text-2xl font-bold text-rqbi-dark mb-8">Notre histoire</h2>
      <div class="space-y-6">
        <div v-for="event in history" :key="event.year" v-animate-in class="flex gap-4">
          <div class="shrink-0 w-16 text-right">
            <span class="text-rqbi-red font-bold text-sm">{{ event.year }}</span>
          </div>
          <div class="w-px bg-gray-200 relative">
            <div class="absolute -left-1.5 top-1 w-3 h-3 rounded-full bg-rqbi-red" />
          </div>
          <p class="text-gray-600 text-sm pb-4 leading-relaxed">{{ event.text }}</p>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, nextTick } from 'vue'

const visible = ref(false)
onMounted(() => nextTick(() => { visible.value = true }))

const activities = [
  {
    icon: '🌿',
    title: 'Espaces verts et paysage',
    description: 'Tonte, fauchage, débroussaillage, taille et abattage d\'arbres, nettoyage de chantiers, gestion des déchets verts.',
  },
  {
    icon: '👕',
    title: 'Blanchisserie, repassage et couture',
    description: 'Prestations de blanchisserie et de couture pour les particuliers et les professionnels.',
  },
  {
    icon: '🧹',
    title: 'Nettoyage et propreté',
    description: 'Entretien de locaux collectifs et d\'équipements de proximité au service des habitants.',
  },
  {
    icon: '🌱',
    title: 'Agriculture et production',
    description: 'Activités en lien avec la production biologique et le recyclage, dans une logique de développement durable.',
  },
]

const values = [
  'Développement durable',
  'Économie sociale et solidaire',
  'Participation citoyenne',
  'Ancrage territorial',
  'Circuits courts',
  'Accompagnement individualisé',
  'Cohésion sociale',
]

const history = [
  { year: '2002', text: 'La ville de Behren-lès-Forbach mène une étude d\'opportunité sur la création d\'une régie de quartier.' },
  { year: '2003', text: 'Création officielle de l\'association le 4 mars 2003, pour répondre aux besoins collectifs du territoire.' },
  { year: '2003', text: 'Obtention du label CNLRQ en décembre, intégration au réseau national des régies de quartier.' },
]
</script>
