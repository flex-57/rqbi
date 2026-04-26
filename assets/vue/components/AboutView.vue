<template>
  <div>
    <!-- Hero -->
    <section class="relative py-20 lg:py-28 overflow-hidden">
      <div class="absolute inset-0 pointer-events-none bg-[radial-gradient(circle_at_80%_10%,rgba(208,32,26,0.06),transparent_40%),radial-gradient(circle_at_10%_90%,rgba(37,99,168,0.06),transparent_40%)]" />
      <div class="container-rqbi relative">
        <span class="eyebrow mb-6" v-animate-in>Qui sommes-nous</span>
        <h1
          class="font-display text-[clamp(2.8rem,6vw,5.5rem)] leading-[0.98] tracking-tightest font-normal max-w-4xl"
          v-animate-in="{ delay: 1 }"
        >
          Une régie ancrée<br/>
          au cœur du <em class="italic text-rqbi-red font-normal">quartier</em>.
        </h1>
        <p class="text-xl text-rqbi-ink-soft leading-relaxed max-w-2xl mt-8" v-animate-in="{ delay: 2 }">
          Créée le <strong class="text-rqbi-red">4 mars 2003</strong>, la Régie de Quartier
          Behren Insertion (RQBI) est une association d'<strong>Insertion par l'Activité Économique</strong>
          labellisée par le CNLRQ. Ancrée au cœur d'un quartier prioritaire de la politique de la ville,
          elle accompagne les habitants éloignés de l'emploi vers un retour durable à l'activité.
        </p>
      </div>
    </section>

    <!-- Chiffres clés -->
    <section class="relative bg-rqbi-ink text-white py-20 px-6 overflow-hidden">
      <div class="absolute inset-0 pointer-events-none bg-[radial-gradient(circle_at_20%_30%,rgba(208,32,26,0.15),transparent_50%)]" />
      <div class="container-rqbi relative">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-10">
          <Stat value="2003" label="Année de création de l'association" :delay="1" />
          <Stat value="100+" label="Salariés accompagnés chaque année" :delay="2" />
          <Stat value="4" label="Secteurs d'activité au service du territoire" :delay="3" />
        </div>
      </div>
    </section>

    <!-- Mission -->
    <section class="py-20 bg-rqbi-paper">
      <div class="container-rqbi">
        <div class="mb-16 max-w-3xl" v-animate-in>
          <span class="eyebrow mb-4">Notre mission</span>
          <h2 class="mb-4">Trois piliers indissociables</h2>
          <p class="text-rqbi-ink-mute text-lg">Une régie de quartier, c'est un projet collectif où l'économie est au service du territoire et de ses habitants.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
          <MissionCard
            v-for="(m, i) in missions" :key="m.title"
            :num="m.num" :title="m.title" :text="m.text" :accent="m.accent"
            :delay="(i + 1) as 1 | 2 | 3"
          />
        </div>
      </div>
    </section>

    <!-- Activités -->
    <section class="py-20">
      <div class="container-rqbi">
        <div class="mb-12 max-w-3xl" v-animate-in>
          <span class="eyebrow mb-4">Nos activités</span>
          <h2>Quatre métiers, <em class="italic text-rqbi-red font-normal">une exigence.</em></h2>
        </div>
        <div class="border-t border-rqbi-line">
          <div
            v-for="(a, i) in activities" :key="a.title"
            class="grid grid-cols-[3rem_1fr_auto] sm:grid-cols-[5rem_1fr_auto] gap-6 items-start py-8 px-2 border-b border-rqbi-line transition-colors group hover:bg-rqbi-cream-deep"
            v-animate-in
          >
            <span class="font-mono text-xs tracking-widest text-rqbi-red pt-2">{{ String(i + 1).padStart(2, '0') }}</span>
            <div>
              <h3 class="font-display text-2xl md:text-3xl font-medium mb-1.5 text-rqbi-ink">{{ a.title }}</h3>
              <p class="text-rqbi-ink-mute leading-relaxed max-w-2xl">{{ a.desc }}</p>
            </div>
            <span class="text-2xl text-rqbi-ink-faint group-hover:text-rqbi-red transition-colors">↗</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Histoire -->
    <section class="py-20 bg-rqbi-cream-deep">
      <div class="container-rqbi-narrow">
        <div class="mb-12" v-animate-in>
          <span class="eyebrow mb-4">Notre histoire</span>
          <h2>Une trajectoire <em class="italic text-rqbi-red font-normal">sur le temps long.</em></h2>
        </div>
        <div class="relative pl-8">
          <span class="absolute left-1.5 top-2 bottom-2 w-px bg-rqbi-line" />
          <div v-for="ev in history" :key="ev.year + ev.title" class="relative pb-10 last:pb-0" v-animate-in>
            <span class="absolute -left-[1.85rem] top-1.5 w-3 h-3 rounded-full bg-white border-2 border-rqbi-red" />
            <span class="font-mono text-xs tracking-widest text-rqbi-red mb-2 block">{{ ev.year }}</span>
            <h4 class="font-display text-xl font-medium mb-1.5 text-rqbi-ink">{{ ev.title }}</h4>
            <p class="text-rqbi-ink-mute text-sm leading-relaxed">{{ ev.text }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Valeurs -->
    <section class="py-20">
      <div class="container-rqbi">
        <div class="mb-10" v-animate-in>
          <span class="eyebrow mb-4">Nos valeurs</span>
          <h2>Ce qui nous tient debout.</h2>
        </div>
        <div class="flex flex-wrap gap-2.5" v-animate-in>
          <span
            v-for="v in values" :key="v"
            class="px-5 py-2.5 bg-white border border-rqbi-line rounded-full text-sm text-rqbi-ink-soft hover:bg-rqbi-ink hover:text-white hover:border-rqbi-ink hover:-translate-y-0.5 transition-all"
          >{{ v }}</span>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import Stat from './Stat.vue'
import MissionCard from './MissionCard.vue'

const missions = [
  { num: 'Approche sociale', title: 'Du lien et un emploi', accent: 'red' as const,
    text: "Créer du lien social, offrir un emploi aux habitants en difficulté, assurer un accompagnement socioprofessionnel et construire des parcours de formation adaptés au marché du travail." },
  { num: 'Approche économique', title: 'Un territoire qui circule', accent: 'blue' as const,
    text: "Répondre aux besoins collectifs du territoire, développer des partenariats, et réinjecter les flux d'activité en circuits courts." },
  { num: 'Approche citoyenne', title: 'Des habitants acteurs', accent: 'dark' as const,
    text: "Renforcer la participation, l'implication et la responsabilité des habitants pour améliorer durablement la vie du quartier." },
]

const activities = [
  { title: 'Espaces verts & paysage', desc: "Tonte, fauchage, débroussaillage, taille et abattage d'arbres, nettoyage de chantiers, gestion des déchets verts." },
  { title: 'Blanchisserie, repassage et couture', desc: "Prestations de blanchisserie et de couture pour les particuliers et les professionnels." },
  { title: 'Nettoyage et propreté', desc: "Entretien de locaux collectifs et d'équipements de proximité au service des habitants." },
  { title: 'Agriculture et production', desc: "Activités en lien avec la production biologique et le recyclage, dans une logique de développement durable." },
]

const history = [
  { year: '2002', title: "Étude d'opportunité", text: "La ville de Behren-lès-Forbach mène une étude sur la création d'une régie de quartier." },
  { year: '2003', title: 'Création officielle', text: "L'association est officiellement créée le 4 mars 2003." },
  { year: '2003', title: 'Labellisation CNLRQ', text: "Obtention du label CNLRQ en décembre, intégration au réseau national." },
  { year: '2025', title: "22 ans d'engagement", text: "Plus de 100 salariés accompagnés chaque année, sur 4 secteurs." },
]

const values = [
  'Développement durable', 'Économie sociale et solidaire', 'Participation citoyenne',
  'Ancrage territorial', 'Circuits courts', 'Accompagnement individualisé', 'Cohésion sociale',
]
</script>
