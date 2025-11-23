import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: null,
    user: null,
    permissions: [],
    isAdmin: false,
    _hydrated: false
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
  },
  actions: {
    async hydrate() {
      if (this._hydrated) return;
      
      this.token = localStorage.getItem('auth_token')
      this.user = JSON.parse(localStorage.getItem('auth_user') || 'null')
      this.permissions = JSON.parse(localStorage.getItem('auth_permissions') || '[]')
      this.isAdmin = localStorage.getItem('auth_is_admin') === 'true'
      
      this._hydrated = true
    },
    login(payload) {
      this.token = payload.token
      this.user = payload.user
      this.permissions = payload.permissions || []
      this.isAdmin = payload.user?.is_admin === 1
      
      this.persist()
      this._hydrated = true
    },
    logout() {
      this.token = null
      this.user = null
      this.permissions = []
      this.isAdmin = false
      this.clearStorage()
      this._hydrated = true
    },

    updateProfile(payload) {
      this.user = payload.user;
      localStorage.setItem('auth_user', JSON.stringify(payload.user));
      this.isAdmin = payload.user?.is_admin === 1;
      this._hydrated = true;
    },

    persist() {
      localStorage.setItem('auth_token', this.token)
      localStorage.setItem('auth_user', JSON.stringify(this.user))
      localStorage.setItem('auth_permissions', JSON.stringify(this.permissions))
      localStorage.setItem('auth_is_admin', this.isAdmin)
    },
    clearStorage() {
      localStorage.removeItem('auth_token')
      localStorage.removeItem('auth_user')
      localStorage.removeItem('auth_permissions')
      localStorage.removeItem('auth_is_admin')
    },
    hasPermission(permission) {
      if (!this._hydrated) this.hydrate()
      return this.permissions.includes(permission)
    }
  }
})