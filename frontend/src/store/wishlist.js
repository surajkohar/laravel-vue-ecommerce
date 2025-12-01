// store/wishlist.js
import { defineStore } from 'pinia'
import { useAuthStore } from './auth'

export const useWishlistStore = defineStore('wishlist', {
  state: () => ({
    items: [],
    loading: false
  }),

  getters: {
    totalItems: (state) => state.items.length,
    hasItems: (state) => state.items.length > 0,
    isInWishlist: (state) => (productId) => {
      return state.items.some(item => item.id === productId)
    }
  },

  actions: {
    async hydrate() {
      const authStore = useAuthStore()
      if (authStore.isAuthenticated) {
        await this.loadWishlistFromBackend()
      } else {
        this.loadWishlistFromStorage()
      }
    },

    loadWishlistFromStorage() {
      const savedWishlist = localStorage.getItem('wishlist')
      if (savedWishlist) {
        try {
          this.items = JSON.parse(savedWishlist)
        } catch (error) {
          console.error('Error parsing wishlist from storage:', error)
          this.items = []
        }
      }
    },

    async loadWishlistFromBackend() {
      // Implement backend wishlist loading
      this.loading = true
      try {
        // const response = await axios.get('/api/wishlist')
        // this.items = response.data.data
      } catch (error) {
        console.error('Error loading wishlist:', error)
      } finally {
        this.loading = false
      }
    },

    addToWishlist(product) {
      if (this.isInWishlist(product.id)) {
        this.removeFromWishlist(product.id)
        return false
      }

      this.items.push({
        id: product.id,
        name: product.name,
        price: product.price,
        image: product.main_image_url || product.image,
        category: product.category,
        brand: product.brand,
        stock: product.stock,
        addedAt: new Date().toISOString()
      })

      this.persistWishlist()
      return true
    },

    removeFromWishlist(productId) {
      this.items = this.items.filter(item => item.id !== productId)
      this.persistWishlist()
    },

    clearWishlist() {
      this.items = []
      this.persistWishlist()
    },

    persistWishlist() {
      const authStore = useAuthStore()
      if (authStore.isAuthenticated) {
        this.syncWishlistWithBackend()
      } else {
        localStorage.setItem('wishlist', JSON.stringify(this.items))
      }
    },

    async syncWishlistWithBackend() {
      // Implement backend wishlist sync
      try {
        // await axios.put('/api/wishlist', { items: this.items })
      } catch (error) {
        console.error('Error syncing wishlist:', error)
      }
    }
  }
})