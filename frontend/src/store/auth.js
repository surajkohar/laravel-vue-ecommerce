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
    userFirstName: (state) => state.user?.name?.split(' ')[0] || 'User',
    userEmail: (state) => state.user?.email || '',
  },
  actions: {
    hydrate() {
      if (this._hydrated) {
        console.log('🔐 Auth already hydrated');
        return;
      }
      
      console.log('🔐 Hydrating auth...');
      
      try {
        this.token = localStorage.getItem('auth_token');
        this.user = JSON.parse(localStorage.getItem('auth_user') || 'null');
        this.permissions = JSON.parse(localStorage.getItem('auth_permissions') || '[]');
        this.isAdmin = localStorage.getItem('auth_is_admin') === 'true';
        
        console.log('🔐 Auth hydrated:', {
          hasToken: !!this.token,
          hasUser: !!this.user,
          user: this.user,
          permissions: this.permissions,
          isAdmin: this.isAdmin
        });
        
        this._hydrated = true;
      } catch (error) {
        console.error('🔐 Error hydrating auth:', error);
        // Reset to default state on error
        this.token = null;
        this.user = null;
        this.permissions = [];
        this.isAdmin = false;
        this._hydrated = true;
      }
    },
    
    login(payload) {
      console.log('🔐 Logging in user:', payload.user?.email);
      
      this.token = payload.token;
      this.user = payload.user;
      this.permissions = payload.permissions || [];
      this.isAdmin = payload.user?.is_admin === 1;
      
      this.persist();
      this._hydrated = true;
      
      console.log('🔐 User logged in successfully');
    },
    
    logout() {
      console.log('🔐 Logging out user');
      
      this.token = null;
      this.user = null;
      this.permissions = [];
      this.isAdmin = false;
      this.clearStorage();
      this._hydrated = true;
      
      console.log('🔐 User logged out successfully');
    },

    updateProfile(payload) {
      console.log('🔐 Updating user profile');
      
      this.user = payload.user;
      localStorage.setItem('auth_user', JSON.stringify(payload.user));
      this.isAdmin = payload.user?.is_admin === 1;
      this._hydrated = true;
    },

    persist() {
      console.log('🔐 Persisting auth state to localStorage');
      
      localStorage.setItem('auth_token', this.token);
      localStorage.setItem('auth_user', JSON.stringify(this.user));
      localStorage.setItem('auth_permissions', JSON.stringify(this.permissions));
      localStorage.setItem('auth_is_admin', this.isAdmin);
    },
    
    clearStorage() {
      console.log('🔐 Clearing auth data from localStorage');
      
      localStorage.removeItem('auth_token');
      localStorage.removeItem('auth_user');
      localStorage.removeItem('auth_permissions');
      localStorage.removeItem('auth_is_admin');
    },
    
    hasPermission(permission) {
      if (!this._hydrated) this.hydrate();
      return this.permissions.includes(permission);
    }
  }
});