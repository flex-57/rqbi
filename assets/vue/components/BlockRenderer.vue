<template>
  <div>
    <div
      v-for="block in visibleBlocks"
      :key="block.id"
      class="group relative transition-opacity"
      :class="{
        'block-editing-overlay': isEditing && block.visible,
        'opacity-50': isEditing && draggedId === block.id,
        'border-t-2 border-rqbi-blue': isEditing && dragOverId === block.id && draggedId !== block.id,
      }"
      :draggable="isEditing"
      @dragstart="onDragStart(block)"
      @dragover.prevent="onDragOver(block)"
      @dragleave="onDragLeave(block)"
      @drop.prevent="onDrop(block)"
      @dragend="onDragEnd"
    >
      <component
        :is="blockComponent(block.type)"
        :block="block"
        :is-editing="isEditing"
      />

      <div v-if="isEditing" class="block-toolbar">
        <span class="p-1 text-gray-400 cursor-grab select-none" title="Glisser pour déplacer">⠿</span>
        <button
          class="p-1 hover:bg-gray-100 rounded text-rqbi-blue"
          title="Modifier"
          @click="$emit('editBlock', block)"
        >✎</button>
        <button
          class="p-1 hover:bg-gray-100 rounded"
          :class="block.visible ? 'text-gray-400' : 'text-rqbi-red'"
          :title="block.visible ? 'Masquer' : 'Afficher'"
          @click="$emit('toggleBlock', block)"
        >{{ block.visible ? '👁' : '🚫' }}</button>
        <button
          class="p-1 hover:bg-red-50 rounded text-red-500"
          title="Supprimer"
          @click="$emit('deleteBlock', block)"
        >✕</button>
      </div>
    </div>

    <div v-if="isEditing" class="flex justify-center my-6">
      <button
        class="flex items-center gap-2 px-5 py-2 border-2 border-dashed border-rqbi-blue text-rqbi-blue font-semibold rounded-lg hover:bg-blue-50 transition-colors"
        @click="$emit('addBlock')"
      >
        + Ajouter un bloc
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import type { Component } from 'vue'
import type { Block } from '../stores/pages'
import blockMap from '../blocks/index'

const props = defineProps<{ blocks: Block[]; isEditing: boolean }>()

const emit = defineEmits<{
  addBlock: []
  editBlock: [block: Block]
  deleteBlock: [block: Block]
  toggleBlock: [block: Block]
  reorder: [order: Array<{ id: number; position: number }>]
}>()

const draggedId = ref<number | null>(null)
const dragOverId = ref<number | null>(null)

const visibleBlocks = computed(() => {
  const sorted = [...props.blocks].sort((a, b) => a.position - b.position)
  return props.isEditing ? sorted : sorted.filter(b => b.visible)
})

function blockComponent(type: string): Component {
  return blockMap[type] ?? blockMap['text']
}

function onDragStart(block: Block) {
  draggedId.value = block.id
}

function onDragOver(block: Block) {
  if (draggedId.value !== null && draggedId.value !== block.id) {
    dragOverId.value = block.id
  }
}

function onDragLeave(block: Block) {
  if (dragOverId.value === block.id) dragOverId.value = null
}

function onDrop(target: Block) {
  if (draggedId.value === null || draggedId.value === target.id) return

  const sorted = [...visibleBlocks.value]
  const fromIndex = sorted.findIndex(b => b.id === draggedId.value)
  const toIndex   = sorted.findIndex(b => b.id === target.id)

  if (fromIndex === -1 || toIndex === -1) return

  sorted.splice(toIndex, 0, sorted.splice(fromIndex, 1)[0])

  const order = sorted.map((b, i) => ({ id: b.id, position: i + 1 }))
  emit('reorder', order)

  draggedId.value = null
  dragOverId.value = null
}

function onDragEnd() {
  draggedId.value = null
  dragOverId.value = null
}
</script>
