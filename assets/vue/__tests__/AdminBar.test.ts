import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { createTestingPinia } from '@pinia/testing'
import AdminBar from '../components/AdminBar.vue'
import { useAuthStore } from '../stores/auth'

const PageEditorStub        = { template: '<div class="page-editor-stub" />',      props: ['page', 'parentId'] }
const ConfirmDialogStub     = { template: '<div />',                                props: ['title', 'message'] }
const ChangePasswordModalStub = { template: '<div class="change-password-stub" />' }
const PagesManagerModalStub   = { template: '<div class="pages-manager-stub" />' }

const mountBar = (isEditing = false) => {
  const pinia = createTestingPinia({ createSpy: vi.fn })
  const wrapper = mount(AdminBar, {
    props: { isEditing },
    global: {
      plugins: [pinia],
      stubs: {
        PageEditor:          PageEditorStub,
        ConfirmDialog:       ConfirmDialogStub,
        ChangePasswordModal: ChangePasswordModalStub,
        PagesManagerModal:   PagesManagerModalStub,
      },
    },
  })
  const authStore = useAuthStore(pinia)
  // @ts-ignore — pinia testing spy
  authStore.user = { id: 1, email: 'admin@rqbi.fr', roles: ['ROLE_ADMIN'] }
  return { wrapper, authStore }
}

describe('AdminBar', () => {
  it('shows "Éditer" when not editing', () => {
    const { wrapper } = mountBar(false)
    expect(wrapper.text()).toContain('Éditer')
  })

  it('shows "Terminer" when editing', () => {
    const { wrapper } = mountBar(true)
    expect(wrapper.text()).toContain('Terminer')
  })

  it('edit button emits toggleEditing', async () => {
    const { wrapper } = mountBar(false)
    const editBtn = wrapper.findAll('button').find(b => b.text().includes('Éditer'))!
    await editBtn.trigger('click')
    expect(wrapper.emitted('toggleEditing')).toHaveLength(1)
  })

  it('does not show Pages ▾ dropdown when not editing', () => {
    const { wrapper } = mountBar(false)
    const dropdownBtn = wrapper.findAll('button').find(b => b.text().includes('▾'))
    expect(dropdownBtn).toBeUndefined()
  })

  it('shows Pages ▾ dropdown when editing', () => {
    const { wrapper } = mountBar(true)
    const dropdownBtn = wrapper.findAll('button').find(b => b.text().includes('▾'))
    expect(dropdownBtn).toBeDefined()
  })

  it('shows change password button', () => {
    const { wrapper } = mountBar()
    expect(wrapper.text()).toContain('Mot de passe')
  })

  it('shows logout button', () => {
    const { wrapper } = mountBar()
    expect(wrapper.text()).toContain('Déconnexion')
  })

  it('logout button calls authStore.logout', async () => {
    const { wrapper, authStore } = mountBar()
    const logoutBtn = wrapper.findAll('button').find(b => b.text().includes('Déconnexion'))!
    await logoutBtn.trigger('click')
    expect(authStore.logout).toHaveBeenCalled()
  })

  it('change password button opens ChangePasswordModal', async () => {
    const { wrapper } = mountBar()
    expect(wrapper.find('.change-password-stub').exists()).toBe(false)
    const pwdBtn = wrapper.findAll('button').find(b => b.text().includes('Mot de passe'))!
    await pwdBtn.trigger('click')
    expect(wrapper.find('.change-password-stub').exists()).toBe(true)
  })

  it('shows ☰ Pages manager button even when not editing', () => {
    const { wrapper } = mountBar(false)
    const mgrBtn = wrapper.findAll('button').find(b => b.text().includes('☰'))
    expect(mgrBtn).toBeDefined()
  })

  it('☰ Pages button opens PagesManagerModal', async () => {
    const { wrapper } = mountBar(false)
    expect(wrapper.find('.pages-manager-stub').exists()).toBe(false)
    const mgrBtn = wrapper.findAll('button').find(b => b.text().includes('☰'))!
    await mgrBtn.trigger('click')
    expect(wrapper.find('.pages-manager-stub').exists()).toBe(true)
  })
})
