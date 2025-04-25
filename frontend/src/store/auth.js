import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('auth_token') || null,
    user: JSON.parse(localStorage.getItem('auth_user')) || null,
  }),
  actions: {
    login(payload) {
      this.token = payload.token
      this.user = payload.user
      localStorage.setItem('auth_token', payload.token)
      localStorage.setItem('auth_user', JSON.stringify(payload.user))
    },
    logout() {
      this.token = null
      this.user = null
      localStorage.removeItem('auth_token')
      localStorage.removeItem('auth_user')
    },
  },
})
