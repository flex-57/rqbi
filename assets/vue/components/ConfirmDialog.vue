<template>
  <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm p-6">
      <h3 class="text-lg font-bold text-rqbi-dark mb-2">{{ title }}</h3>
      <p class="text-gray-600 text-sm mb-6">{{ message }}</p>
      <div class="flex justify-end gap-3">
        <button class="btn-ghost" @click="$emit('cancel')">Annuler</button>
        <button class="bg-red-500 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded transition-colors" @click="$emit('confirm')">
          Confirmer
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{ title: string; message: string }>()
defineEmits<{ confirm: []; cancel: [] }>()
</script>
