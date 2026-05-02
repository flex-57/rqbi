import { describe, it, expect, vi } from 'vitest'
import { mount, flushPromises } from '@vue/test-utils'
import { createTestingPinia } from '@pinia/testing'
import BlockEditor from '../components/BlockEditor.vue'
import { useBlocksStore } from '../stores/blocks'
import type { Block } from '../stores/pages'

const RichTextEditorStub = {
  template: '<textarea class="rich-stub" />',
  props: ['modelValue', 'hasError'],
  emits: ['update:modelValue'],
}

const makeBlock = (overrides: Partial<Block> = {}): Block => ({
  id: 1,
  type: 'text',
  content: { title: 'Titre', body: 'Contenu existant' },
  position: 1,
  visible: true,
  ...overrides,
})

const mountEditor = (block: Block | null, pageId = 1) => {
  const pinia = createTestingPinia({ createSpy: vi.fn })
  const wrapper = mount(BlockEditor, {
    props: { block, pageId },
    global: {
      plugins: [pinia],
      stubs: { RichTextEditor: RichTextEditorStub },
    },
  })
  return { wrapper, store: useBlocksStore(pinia) }
}

describe('BlockEditor', () => {
  it('shows "Ajouter un bloc" title when block is null', () => {
    const { wrapper } = mountEditor(null)
    expect(wrapper.text()).toContain('Ajouter un bloc')
  })

  it('shows "Modifier le bloc" title when block is provided', () => {
    const { wrapper } = mountEditor(makeBlock())
    expect(wrapper.text()).toContain('Modifier le bloc')
  })

  it('shows type selector grid with 12 types when creating', () => {
    const { wrapper } = mountEditor(null)
    expect(wrapper.findAll('.grid.grid-cols-3 button')).toHaveLength(12)
  })

  it('does not show type selector when editing', () => {
    const { wrapper } = mountEditor(makeBlock())
    expect(wrapper.find('.grid.grid-cols-3').exists()).toBe(false)
  })

  it('close button (×) emits close', async () => {
    const { wrapper } = mountEditor(null)
    // × is always the first button in the DOM
    await wrapper.findAll('button')[0].trigger('click')
    expect(wrapper.emitted('close')).toHaveLength(1)
  })

  it('cancel button emits close', async () => {
    const { wrapper } = mountEditor(null)
    await wrapper.find('.btn-ghost').trigger('click')
    expect(wrapper.emitted('close')).toHaveLength(1)
  })

  it('switching type clears errors and resets form', async () => {
    const { wrapper } = mountEditor(null)
    // Trigger validation error on text type (empty body)
    await wrapper.find('.btn-primary').trigger('click')
    await flushPromises()
    expect(wrapper.text()).toContain('Le contenu est obligatoire')
    // Switch to image → errors must disappear
    await wrapper.findAll('.grid.grid-cols-3 button')[1].trigger('click')
    await flushPromises()
    expect(wrapper.text()).not.toContain('Le contenu est obligatoire')
  })

  it('shows validation error when saving text block with empty body', async () => {
    const { wrapper } = mountEditor(null) // text type, body = '' by default
    await wrapper.find('.btn-primary').trigger('click')
    await flushPromises()
    expect(wrapper.text()).toContain('Le contenu est obligatoire')
  })

  it('shows validation error when saving image block with empty url', async () => {
    const { wrapper } = mountEditor(null)
    await wrapper.findAll('.grid.grid-cols-3 button')[1].trigger('click') // image
    await wrapper.find('.btn-primary').trigger('click')
    await flushPromises()
    expect(wrapper.text()).toContain("L'image est obligatoire")
  })

  it('does not call createBlock when validation fails', async () => {
    const { wrapper, store } = mountEditor(null) // text, empty body → validation fails
    await wrapper.find('.btn-primary').trigger('click')
    await flushPromises()
    expect(store.createBlock).not.toHaveBeenCalled()
  })

  it('does not emit close when validation fails', async () => {
    const { wrapper } = mountEditor(null)
    await wrapper.find('.btn-primary').trigger('click')
    await flushPromises()
    expect(wrapper.emitted('close')).toBeFalsy()
  })

  it('calls createBlock with correct pageId and type when saving new block', async () => {
    const { wrapper, store } = mountEditor(null)
    // Switch to divider (index 5) — no required fields
    await wrapper.findAll('.grid.grid-cols-3 button')[5].trigger('click')
    await wrapper.find('.btn-primary').trigger('click')
    await flushPromises()
    expect(store.createBlock).toHaveBeenCalledWith(1, 'divider', expect.any(Object))
  })

  it('calls updateBlock with block id when saving existing block', async () => {
    const block = makeBlock() // text, body = 'Contenu existant' → passes validation
    const { wrapper, store } = mountEditor(block)
    await wrapper.find('.btn-primary').trigger('click')
    await flushPromises()
    expect(store.updateBlock).toHaveBeenCalledWith(1, expect.any(Object))
  })

  it('emits close after successful save', async () => {
    const block = makeBlock()
    const { wrapper } = mountEditor(block)
    await wrapper.find('.btn-primary').trigger('click')
    await flushPromises()
    expect(wrapper.emitted('close')).toHaveLength(1)
  })
})
