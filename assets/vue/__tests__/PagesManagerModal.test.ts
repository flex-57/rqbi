import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { createTestingPinia } from '@pinia/testing'
import PagesManagerModal from '../components/PagesManagerModal.vue'
import { usePagesStore } from '../stores/pages'

const mockRouter = { push: vi.fn() }
vi.mock('vue-router', () => ({ useRouter: () => mockRouter }))

const pageA = { id: 1, title: 'Accueil',   slug: 'accueil',   published: true,  depth: 0, children: [], blocks: [], parent: null, metaTitle: null, metaDescription: null }
const pageB = { id: 2, title: 'Brouillon', slug: 'brouillon', published: false, depth: 0, children: [], blocks: [], parent: null, metaTitle: null, metaDescription: null }
const pageC = { id: 3, title: 'Sous-page', slug: 'sous-page', published: true,  depth: 1, children: [], blocks: [], parent: null, metaTitle: null, metaDescription: null }

const mountModal = (tree = [pageA, pageB]) => {
  const pinia = createTestingPinia({ createSpy: vi.fn })
  const pagesStore = usePagesStore(pinia)
  // @ts-ignore — pinia testing spy
  pagesStore.tree = tree
  const wrapper = mount(PagesManagerModal, { global: { plugins: [pinia] } })
  return { wrapper, pagesStore }
}

describe('PagesManagerModal', () => {
  it('renders all pages in the list', () => {
    const { wrapper } = mountModal()
    expect(wrapper.text()).toContain('Accueil')
    expect(wrapper.text()).toContain('Brouillon')
  })

  it('shows Publié badge for published page', () => {
    const { wrapper } = mountModal([pageA])
    expect(wrapper.text()).toContain('Publié')
  })

  it('shows Brouillon badge for unpublished page', () => {
    const { wrapper } = mountModal([pageB])
    expect(wrapper.text()).toContain('Brouillon')
  })

  it('indents child pages with data-depth attribute', () => {
    const { wrapper } = mountModal([{ ...pageA, children: [pageC] }])
    expect(wrapper.find('[data-depth="0"]').exists()).toBe(true)
    expect(wrapper.find('[data-depth="1"]').exists()).toBe(true)
  })

  it('Dépublier button calls pagesStore.updatePage with published: false', async () => {
    const { wrapper, pagesStore } = mountModal([pageA])
    const btn = wrapper.findAll('button').find(b => b.text() === 'Dépublier')!
    await btn.trigger('click')
    expect(pagesStore.updatePage).toHaveBeenCalledWith(1, { published: false })
  })

  it('Publier button calls pagesStore.updatePage with published: true', async () => {
    const { wrapper, pagesStore } = mountModal([pageB])
    const btn = wrapper.findAll('button').find(b => b.text() === 'Publier')!
    await btn.trigger('click')
    expect(pagesStore.updatePage).toHaveBeenCalledWith(2, { published: true })
  })

  it('→ button pushes route and emits close', async () => {
    const { wrapper } = mountModal([pageA])
    const btn = wrapper.findAll('button').find(b => b.text() === '→')!
    await btn.trigger('click')
    expect(mockRouter.push).toHaveBeenCalledWith('/accueil')
    expect(wrapper.emitted('close')).toHaveLength(1)
  })

  it('× button emits close', async () => {
    const { wrapper } = mountModal()
    const btn = wrapper.findAll('button').find(b => b.text() === '×')!
    await btn.trigger('click')
    expect(wrapper.emitted('close')).toHaveLength(1)
  })

  it('shows empty message when no pages', () => {
    const { wrapper } = mountModal([])
    expect(wrapper.text()).toContain('Aucune page')
  })
})
