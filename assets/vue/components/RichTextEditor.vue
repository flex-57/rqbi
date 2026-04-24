<template>
  <div class="rich-editor border rounded-lg overflow-hidden" :class="hasError ? 'border-red-400' : 'border-gray-300'">
    <!-- Toolbar -->
    <div class="flex flex-wrap items-center gap-0.5 p-2 bg-gray-50 border-b border-gray-200">

      <!-- Paragraphe / Titres -->
      <select
        class="text-xs border border-gray-200 rounded px-2 py-1 bg-white focus:outline-none focus:ring-1 focus:ring-rqbi-blue mr-1"
        :value="currentHeading"
        @change="setHeading(($event.target as HTMLSelectElement).value)"
      >
        <option value="paragraph">Paragraphe</option>
        <option value="1">Titre 1</option>
        <option value="2">Titre 2</option>
        <option value="3">Titre 3</option>
      </select>

      <div class="w-px h-5 bg-gray-200 mx-1" />

      <!-- Gras / Italique / Souligné / Barré -->
      <ToolbarBtn :active="editor?.isActive('bold')" title="Gras (Ctrl+B)" @click="editor?.chain().focus().toggleBold().run()">
        <strong>B</strong>
      </ToolbarBtn>
      <ToolbarBtn :active="editor?.isActive('italic')" title="Italique (Ctrl+I)" @click="editor?.chain().focus().toggleItalic().run()">
        <em>I</em>
      </ToolbarBtn>
      <ToolbarBtn :active="editor?.isActive('underline')" title="Souligné (Ctrl+U)" @click="editor?.chain().focus().toggleUnderline().run()">
        <span class="underline">U</span>
      </ToolbarBtn>
      <ToolbarBtn :active="editor?.isActive('strike')" title="Barré" @click="editor?.chain().focus().toggleStrike().run()">
        <span class="line-through">S</span>
      </ToolbarBtn>
      <ToolbarBtn :active="editor?.isActive('highlight')" title="Surligner" @click="editor?.chain().focus().toggleHighlight().run()">
        ▍
      </ToolbarBtn>

      <div class="w-px h-5 bg-gray-200 mx-1" />

      <!-- Listes -->
      <ToolbarBtn :active="editor?.isActive('bulletList')" title="Liste à puces" @click="editor?.chain().focus().toggleBulletList().run()">
        ≡
      </ToolbarBtn>
      <ToolbarBtn :active="editor?.isActive('orderedList')" title="Liste numérotée" @click="editor?.chain().focus().toggleOrderedList().run()">
        №
      </ToolbarBtn>

      <div class="w-px h-5 bg-gray-200 mx-1" />

      <!-- Alignement -->
      <ToolbarBtn :active="editor?.isActive({ textAlign: 'left' })" title="Aligner à gauche" @click="editor?.chain().focus().setTextAlign('left').run()">◧</ToolbarBtn>
      <ToolbarBtn :active="editor?.isActive({ textAlign: 'center' })" title="Centrer" @click="editor?.chain().focus().setTextAlign('center').run()">◫</ToolbarBtn>
      <ToolbarBtn :active="editor?.isActive({ textAlign: 'right' })" title="Aligner à droite" @click="editor?.chain().focus().setTextAlign('right').run()">◨</ToolbarBtn>

      <div class="w-px h-5 bg-gray-200 mx-1" />

      <!-- Couleur de texte -->
      <div class="flex items-center gap-1 mr-1">
        <span class="text-xs text-gray-500">A</span>
        <div class="flex gap-1">
          <button
            v-for="c in colors"
            :key="c.value"
            class="w-4 h-4 rounded-full border border-gray-300 transition-transform hover:scale-110"
            :class="{ 'ring-2 ring-offset-1 ring-rqbi-blue': editor?.isActive('textStyle', { color: c.value }) }"
            :style="{ backgroundColor: c.value }"
            :title="c.label"
            @click="editor?.chain().focus().setColor(c.value).run()"
          />
          <button
            class="w-4 h-4 rounded-full border border-gray-300 bg-white text-xs leading-none flex items-center justify-center hover:bg-gray-50"
            title="Couleur par défaut"
            @click="editor?.chain().focus().unsetColor().run()"
          >×</button>
        </div>
      </div>

      <div class="w-px h-5 bg-gray-200 mx-1" />

      <!-- Lien -->
      <ToolbarBtn :active="editor?.isActive('link')" title="Insérer un lien" @click="insertLink">🔗</ToolbarBtn>
      <ToolbarBtn v-if="editor?.isActive('link')" title="Supprimer le lien" @click="editor?.chain().focus().unsetLink().run()">✂</ToolbarBtn>

      <div class="w-px h-5 bg-gray-200 mx-1" />

      <!-- Blockquote -->
      <ToolbarBtn :active="editor?.isActive('blockquote')" title="Citation" @click="editor?.chain().focus().toggleBlockquote().run()">❝</ToolbarBtn>

      <!-- Effacer le formatage -->
      <ToolbarBtn title="Effacer le formatage" @click="editor?.chain().focus().clearNodes().unsetAllMarks().run()">✕</ToolbarBtn>
    </div>

    <!-- Zone d'édition -->
    <EditorContent
      :editor="editor"
      class="rqbi-content min-h-[180px] p-4 focus-within:outline-none text-rqbi-dark"
    />
  </div>
</template>

<script setup lang="ts">
import { computed, onBeforeUnmount, watch } from 'vue'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import { TextStyle } from '@tiptap/extension-text-style'
import { Color } from '@tiptap/extension-color'
import TextAlign from '@tiptap/extension-text-align'
import Highlight from '@tiptap/extension-highlight'

// Sous-composant toolbar button inline
import { defineComponent, h } from 'vue'
const ToolbarBtn = defineComponent({
  props: { active: Boolean, title: String },
  emits: ['click'],
  setup(props, { slots, emit }) {
    return () => h('button', {
      type: 'button',
      title: props.title,
      onClick: () => emit('click'),
      class: [
        'w-7 h-7 flex items-center justify-center rounded text-sm transition-colors',
        props.active ? 'bg-rqbi-blue text-white' : 'text-gray-600 hover:bg-gray-200',
      ],
    }, slots.default?.())
  },
})

const props = defineProps<{ modelValue: string; hasError?: boolean }>()
const emit = defineEmits<{ 'update:modelValue': [value: string] }>()

const colors = [
  { value: '#D0201A', label: 'Rouge RQBI' },
  { value: '#2563A8', label: 'Bleu RQBI' },
  { value: '#1a1a1a', label: 'Noir' },
  { value: '#6b7280', label: 'Gris' },
  { value: '#ffffff', label: 'Blanc' },
]

const editor = useEditor({
  content: props.modelValue,
  extensions: [
    StarterKit.configure({
      link: { openOnClick: false },
    }),
    TextStyle,
    Color,
    Highlight.configure({ multicolor: false }),
    TextAlign.configure({ types: ['heading', 'paragraph'] }),
  ],
  editorProps: {
    attributes: { spellcheck: 'true' },
  },
  onUpdate: ({ editor }) => {
    emit('update:modelValue', editor.getHTML())
  },
})

watch(() => props.modelValue, (val) => {
  if (editor.value && editor.value.getHTML() !== val) {
    editor.value.commands.setContent(val, false)
  }
})

onBeforeUnmount(() => editor.value?.destroy())

const currentHeading = computed(() => {
  if (!editor.value) return 'paragraph'
  for (let i = 1; i <= 3; i++) {
    if (editor.value.isActive('heading', { level: i })) return String(i)
  }
  return 'paragraph'
})

function setHeading(val: string) {
  if (!editor.value) return
  if (val === 'paragraph') {
    editor.value.chain().focus().setParagraph().run()
  } else {
    editor.value.chain().focus().setHeading({ level: parseInt(val) as 1|2|3 }).run()
  }
}

function insertLink() {
  const url = window.prompt('URL du lien :', editor.value?.getAttributes('link').href ?? '')
  if (url === null) return
  if (url === '') {
    editor.value?.chain().focus().unsetLink().run()
  } else {
    editor.value?.chain().focus().setLink({ href: url }).run()
  }
}
</script>

<style>
.rich-editor .ProseMirror { outline: none; }
</style>
