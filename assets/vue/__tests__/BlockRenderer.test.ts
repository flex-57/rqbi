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

const mountRenderer = (blocks: Block[], isEditing: boolean) =>
  mount(BlockRenderer, {
    props: { blocks, isEditing },
    global: {
      plugins: [createTestingPinia()],
      directives: { 'animate-in': {} },
    },
  })

describe('BlockRenderer', () => {
  it('renders visible blocks', () => {
    const wrapper = mountRenderer([makeBlock(), makeBlock({ id: 2, position: 2 })], false)
    expect(wrapper.findAll('.group').length).toBe(2)
  })

  it('hides invisible blocks when not editing', () => {
    const wrapper = mountRenderer(
      [makeBlock({ id: 1, visible: true }), makeBlock({ id: 2, visible: false })],
      false,
    )
    expect(wrapper.findAll('.group').length).toBe(1)
  })

  it('shows all blocks in editing mode', () => {
    const wrapper = mountRenderer(
      [makeBlock({ id: 1, visible: true }), makeBlock({ id: 2, visible: false })],
      true,
    )
    expect(wrapper.findAll('.group').length).toBe(2)
  })

  it('shows add block button when editing', () => {
    const wrapper = mountRenderer([], true)
    expect(wrapper.text()).toContain('Ajouter un bloc')
  })

  it('does not show add block button when not editing', () => {
    const wrapper = mountRenderer([], false)
    expect(wrapper.text()).not.toContain('Ajouter un bloc')
  })

  it('shows editing toolbar when isEditing is true', () => {
    const wrapper = mountRenderer([makeBlock()], true)
    expect(wrapper.find('.block-toolbar').exists()).toBe(true)
  })

  it('emits addBlock when the add button is clicked', async () => {
    const wrapper = mountRenderer([], true)
    await wrapper.find('button').trigger('click')
    expect(wrapper.emitted('addBlock')).toHaveLength(1)
  })

  it('renders blocks in ascending position order', () => {
    const blocks = [
      makeBlock({ id: 1, position: 3, content: { title: 'Troisième', body: '' } }),
      makeBlock({ id: 2, position: 1, content: { title: 'Premier', body: '' } }),
      makeBlock({ id: 3, position: 2, content: { title: 'Deuxième', body: '' } }),
    ]
    const wrapper = mountRenderer(blocks, false)
    const text = wrapper.text()
    expect(text.indexOf('Premier')).toBeLessThan(text.indexOf('Deuxième'))
    expect(text.indexOf('Deuxième')).toBeLessThan(text.indexOf('Troisième'))
  })

  it('emits editBlock with the block when the edit toolbar button is clicked', async () => {
    const block = makeBlock()
    const wrapper = mountRenderer([block], true)
    const buttons = wrapper.find('.block-toolbar').findAll('button')
    await buttons[0].trigger('click')
    expect(wrapper.emitted('editBlock')).toHaveLength(1)
    expect(wrapper.emitted('editBlock')![0]).toEqual([block])
  })

  it('emits toggleBlock with the block when the visibility toolbar button is clicked', async () => {
    const block = makeBlock()
    const wrapper = mountRenderer([block], true)
    const buttons = wrapper.find('.block-toolbar').findAll('button')
    await buttons[1].trigger('click')
    expect(wrapper.emitted('toggleBlock')).toHaveLength(1)
    expect(wrapper.emitted('toggleBlock')![0]).toEqual([block])
  })

  it('emits deleteBlock with the block when the delete toolbar button is clicked', async () => {
    const block = makeBlock()
    const wrapper = mountRenderer([block], true)
    const buttons = wrapper.find('.block-toolbar').findAll('button')
    await buttons[2].trigger('click')
    expect(wrapper.emitted('deleteBlock')).toHaveLength(1)
    expect(wrapper.emitted('deleteBlock')![0]).toEqual([block])
  })
})
