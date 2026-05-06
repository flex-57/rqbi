import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { createTestingPinia } from '@pinia/testing'
import NavBar from '../components/NavBar.vue'
import { usePagesStore } from '../stores/pages'

const published = { id: 1, title: 'Accueil',    slug: 'accueil',    published: true,  depth: 0, children: [], blocks: [], parent: null, metaTitle: null, metaDescription: null }
const draft     = { id: 2, title: 'En attente',  slug: 'en-attente', published: false, depth: 0, children: [], blocks: [], parent: null, metaTitle: null, metaDescription: null }

const mountNav = async (isEditing = false, tree = [published, draft]) => {
  const pinia = createTestingPinia({ createSpy: vi.fn })
  const pagesStore = usePagesStore(pinia)  // get store BEFORE mount
  // @ts-ignore
  pagesStore.tree = tree
  const wrapper = mount(NavBar, {
    props: { isEditing },
    global: { plugins: [pinia], stubs: { RouterLink: { template: '<a><slot /></a>' } } },
  })
  return { wrapper, pagesStore }
}

describe('NavBar', () => {
  it('shows only published pages when not editing', async () => {
    const { wrapper } = await mountNav(false)
    expect(wrapper.text()).toContain('Accueil')
    expect(wrapper.text()).not.toContain('En attente')
  })

  it('shows all pages (including drafts) when editing', async () => {
    const { wrapper } = await mountNav(true)
    expect(wrapper.text()).toContain('Accueil')
    expect(wrapper.text()).toContain('En attente')
  })

  it('shows (brouillon) indicator for unpublished pages in edit mode', async () => {
    const { wrapper } = await mountNav(true)
    expect(wrapper.text()).toContain('(brouillon)')
  })

  it('does not show (brouillon) indicator when not editing', async () => {
    const { wrapper } = await mountNav(false, [published])
    expect(wrapper.text()).not.toContain('(brouillon)')
  })
})
