import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../composables/api'

export interface Block {
  id: number
  type: string
  content: Record<string, unknown>
  position: number
  visible: boolean
}

export interface Page {
  id: number
  title: string
  slug: string
  depth: number
  published: boolean
  metaTitle: string | null
  metaDescription: string | null
  parent: Page | null
  children: Page[]
  blocks: Block[]
}

export const usePagesStore = defineStore('pages', () => {
  const tree = ref<Page[]>([])
  const currentPage = ref<Page | null>(null)
  const loading = ref(false)

  async function fetchTree(): Promise<void> {
    const res = await api.get('/api/pages/tree')
    tree.value = res.data
  }

  async function fetchPage(slug: string): Promise<void> {
    loading.value = true
    try {
      const res = await api.get(`/api/pages/slug/${slug}`)
      currentPage.value = res.data
    } finally {
      loading.value = false
    }
  }

  async function createPage(payload: { title: string; parent_id?: number | null; published?: boolean }): Promise<Page> {
    const res = await api.post('/api/pages', payload)
    await fetchTree()
    return res.data
  }

  async function updatePage(id: number, payload: Partial<{ title: string; slug: string; published: boolean; parent_id: number | null; meta_title: string | null; meta_description: string | null }>): Promise<Page> {
    const res = await api.put(`/api/pages/${id}`, payload)
    await fetchTree()
    if (currentPage.value?.id === id) {
      currentPage.value = res.data
    }
    return res.data
  }

  async function deletePage(id: number): Promise<void> {
    await api.delete(`/api/pages/${id}`)
    await fetchTree()
    if (currentPage.value?.id === id) {
      currentPage.value = null
    }
  }

  return { tree, currentPage, loading, fetchTree, fetchPage, createPage, updatePage, deletePage }
})
