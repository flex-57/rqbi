import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount, flushPromises } from '@vue/test-utils'

vi.mock('../composables/api', () => ({
  default: { post: vi.fn() },
}))

import api from '../composables/api'
import ChangePasswordModal from '../components/ChangePasswordModal.vue'

const mountModal = () => mount(ChangePasswordModal)

describe('ChangePasswordModal', () => {
  beforeEach(() => {
    vi.mocked(api.post).mockReset()
  })

  it('shows 3 password fields and save/cancel buttons', () => {
    const wrapper = mountModal()
    expect(wrapper.findAll('input[type="password"]')).toHaveLength(3)
    expect(wrapper.find('button[type="submit"]').exists()).toBe(true)
    expect(wrapper.find('.btn-ghost').exists()).toBe(true)
  })

  it('cancel button emits close', async () => {
    const wrapper = mountModal()
    await wrapper.find('.btn-ghost').trigger('click')
    expect(wrapper.emitted('close')).toHaveLength(1)
  })

  it('shows error when current password is empty', async () => {
    const wrapper = mountModal()
    await wrapper.find('form').trigger('submit')
    await flushPromises()
    expect(wrapper.text()).toContain('Le mot de passe actuel est obligatoire')
    expect(api.post).not.toHaveBeenCalled()
  })

  it('shows error when new password is empty', async () => {
    const wrapper = mountModal()
    const inputs = wrapper.findAll('input[type="password"]')
    await inputs[0].setValue('adminpass')
    await wrapper.find('form').trigger('submit')
    await flushPromises()
    expect(wrapper.text()).toContain('Le nouveau mot de passe est obligatoire')
    expect(api.post).not.toHaveBeenCalled()
  })

  it('shows error when new password is too short', async () => {
    const wrapper = mountModal()
    const inputs = wrapper.findAll('input[type="password"]')
    await inputs[0].setValue('adminpass')
    await inputs[1].setValue('short')
    await inputs[2].setValue('short')
    await wrapper.find('form').trigger('submit')
    await flushPromises()
    expect(wrapper.text()).toContain('au moins 8 caractères')
    expect(api.post).not.toHaveBeenCalled()
  })

  it('shows error when confirmation does not match', async () => {
    const wrapper = mountModal()
    const inputs = wrapper.findAll('input[type="password"]')
    await inputs[0].setValue('adminpass')
    await inputs[1].setValue('newpassword123')
    await inputs[2].setValue('differentpassword')
    await wrapper.find('form').trigger('submit')
    await flushPromises()
    expect(wrapper.text()).toContain('ne correspondent pas')
    expect(api.post).not.toHaveBeenCalled()
  })

  it('calls api.post with correct payload on valid submit', async () => {
    vi.mocked(api.post).mockResolvedValue({})
    const wrapper = mountModal()
    const inputs = wrapper.findAll('input[type="password"]')
    await inputs[0].setValue('admin')
    await inputs[1].setValue('newpassword123')
    await inputs[2].setValue('newpassword123')
    await wrapper.find('form').trigger('submit')
    await flushPromises()
    expect(api.post).toHaveBeenCalledWith('/api/auth/change-password', {
      current_password: 'admin',
      new_password: 'newpassword123',
      new_password_confirmation: 'newpassword123',
    })
  })

  it('shows success message after successful save', async () => {
    vi.mocked(api.post).mockResolvedValue({})
    const wrapper = mountModal()
    const inputs = wrapper.findAll('input[type="password"]')
    await inputs[0].setValue('admin')
    await inputs[1].setValue('newpassword123')
    await inputs[2].setValue('newpassword123')
    await wrapper.find('form').trigger('submit')
    await flushPromises()
    expect(wrapper.text()).toContain('Mot de passe modifié avec succès')
  })

  it('emits close 1500ms after success', async () => {
    vi.useFakeTimers()
    vi.mocked(api.post).mockResolvedValue({})
    const wrapper = mountModal()
    const inputs = wrapper.findAll('input[type="password"]')
    await inputs[0].setValue('admin')
    await inputs[1].setValue('newpassword123')
    await inputs[2].setValue('newpassword123')
    await wrapper.find('form').trigger('submit')
    await flushPromises()
    expect(wrapper.emitted('close')).toBeFalsy()
    vi.advanceTimersByTime(1500)
    expect(wrapper.emitted('close')).toHaveLength(1)
    vi.useRealTimers()
  })

  it('shows server error message when API returns error', async () => {
    vi.mocked(api.post).mockRejectedValue({
      response: { data: { error: 'Mot de passe actuel incorrect' } },
    })
    const wrapper = mountModal()
    const inputs = wrapper.findAll('input[type="password"]')
    await inputs[0].setValue('wrongpassword')
    await inputs[1].setValue('newpassword123')
    await inputs[2].setValue('newpassword123')
    await wrapper.find('form').trigger('submit')
    await flushPromises()
    expect(wrapper.text()).toContain('Mot de passe actuel incorrect')
  })
})
