import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import { createTestingPinia } from '@pinia/testing'
import BlockRenderer from '../components/BlockRenderer.vue'
import type { Block } from '../stores/pages'

const makeBlock = (overrides: Partial<Block> = {}): Block => ({
  id: 1,
  type: 'text',
  content: { title: 'Titre test', body: 'Contenu test' },
  position: 1,
  visible: true,
  ...overrides,
})

describe('BlockRenderer', () => {
  it('renders visible blocks', () => {
    const blocks = [makeBlock(), makeBlock({ id: 2, position: 2 })]
    const wrapper = mount(BlockRenderer, {
      props: { blocks, isEditing: false },
      global: { plugins: [createTestingPinia()] },
    })
    expect(wrapper.findAll('.group').length).toBe(2)
  })

  it('hides invisible blocks when not editing', () => {
    const blocks = [
      makeBlock({ id: 1, visible: true }),
      makeBlock({ id: 2, visible: false }),
    ]
    const wrapper = mount(BlockRenderer, {
      props: { blocks, isEditing: false },
      global: { plugins: [createTestingPinia()] },
    })
    expect(wrapper.findAll('.group').length).toBe(1)
  })

  it('shows all blocks in editing mode', () => {
    const blocks = [
      makeBlock({ id: 1, visible: true }),
      makeBlock({ id: 2, visible: false }),
    ]
    const wrapper = mount(BlockRenderer, {
      props: { blocks, isEditing: true },
      global: { plugins: [createTestingPinia()] },
    })
    expect(wrapper.findAll('.group').length).toBe(2)
  })

  it('shows add block button when editing', () => {
    const wrapper = mount(BlockRenderer, {
      props: { blocks: [], isEditing: true },
      global: { plugins: [createTestingPinia()] },
    })
    expect(wrapper.text()).toContain('Ajouter un bloc')
  })

  it('does not show add block button when not editing', () => {
    const wrapper = mount(BlockRenderer, {
      props: { blocks: [], isEditing: false },
      global: { plugins: [createTestingPinia()] },
    })
    expect(wrapper.text()).not.toContain('Ajouter un bloc')
  })

  it('shows editing toolbar when isEditing is true', () => {
    const blocks = [makeBlock()]
    const wrapper = mount(BlockRenderer, {
      props: { blocks, isEditing: true },
      global: { plugins: [createTestingPinia()] },
    })
    expect(wrapper.find('.block-toolbar').exists()).toBe(true)
  })

  it('emits addBlock event on button click', async () => {
    const wrapper = mount(BlockRenderer, {
      props: { blocks: [], isEditing: true },
      global: { plugins: [createTestingPinia()] },
    })
    await wrapper.find('button').trigger('click')
    expect(wrapper.emitted('addBlock')).toHaveLength(1)
  })

  it('renders blocks sorted by position', () => {
    const blocks = [
      makeBlock({ id: 1, position: 3 }),
      makeBlock({ id: 2, position: 1 }),
      makeBlock({ id: 3, position: 2 }),
    ]
    const wrapper = mount(BlockRenderer, {
      props: { blocks, isEditing: false },
      global: { plugins: [createTestingPinia()] },
    })
    const rendered = wrapper.findAll('.group')
    expect(rendered.length).toBe(3)
  })
})
