import { defineStore } from 'pinia'
import api from '../composables/api'
import { usePagesStore } from './pages'

export const useBlocksStore = defineStore('blocks', () => {
  const pagesStore = usePagesStore()

  async function createBlock(pageId: number, type: string, content: Record<string, unknown> = {}): Promise<void> {
    await api.post(`/api/pages/${pageId}/blocks`, { type, content })
    await pagesStore.fetchPage(pagesStore.currentPage!.slug)
  }

  async function updateBlock(id: number, content: Record<string, unknown>): Promise<void> {
    await api.put(`/api/blocks/${id}`, { content })
    await pagesStore.fetchPage(pagesStore.currentPage!.slug)
  }

  async function deleteBlock(id: number): Promise<void> {
    await api.delete(`/api/blocks/${id}`)
    await pagesStore.fetchPage(pagesStore.currentPage!.slug)
  }

  async function reorderBlocks(pageId: number, order: Array<{ id: number; position: number }>): Promise<void> {
    await api.put(`/api/pages/${pageId}/blocks/reorder`, { order })
    await pagesStore.fetchPage(pagesStore.currentPage!.slug)
  }

  async function toggleBlock(id: number): Promise<void> {
    await api.patch(`/api/blocks/${id}/toggle`)
    await pagesStore.fetchPage(pagesStore.currentPage!.slug)
  }

  async function uploadFile(file: File): Promise<string> {
    const form = new FormData()
    form.append('file', file)
    const res = await api.post('/api/upload', form)
    return res.data.url
  }

  return { createBlock, updateBlock, deleteBlock, reorderBlocks, toggleBlock, uploadFile }
})
