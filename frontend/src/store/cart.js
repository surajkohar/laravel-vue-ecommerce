import { defineStore } from 'pinia'
import { useAuthStore } from './auth'

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: [],
    loading: false
  }),

  getters: {
    totalItems: (state) => state.items.reduce((total, item) => total + item.quantity, 0),
    subtotal: (state) => state.items.reduce((total, item) => total + (item.price * item.quantity), 0),
    total: (state) => {
      const subtotal = state.items.reduce((total, item) => total + (item.price * item.quantity), 0)
      // Add shipping, taxes, etc. here if needed
      return subtotal
    },
    hasItems: (state) => state.items.length > 0
  },

  actions: {
async hydrate() {
      console.log('🛒 Hydrating cart...')
      const authStore = useAuthStore()
      if (authStore.isAuthenticated) {
        await this.loadCartFromBackend()
      } else {
        this.loadCartFromStorage()
      }
      console.log('🛒 Cart hydrated with items:', this.items.length)
    },

loadCartFromStorage() {
      const savedCart = localStorage.getItem('cart')
      if (savedCart) {
        try {
          this.items = JSON.parse(savedCart)
          console.log('🛒 Loaded cart from storage:', this.items)
        } catch (error) {
          console.error('🛒 Error parsing cart from storage:', error)
          this.items = []
        }
      } else {
        console.log('🛒 No cart found in storage')
        this.items = []
      }
    },

    async loadCartFromBackend() {
      // Implement backend cart loading
      this.loading = true
      try {
        // const response = await axios.get('/api/cart')
        // this.items = response.data.data
      } catch (error) {
        console.error('Error loading cart:', error)
      } finally {
        this.loading = false
      }
    },

    addToCart(product, quantity = 1, variant = null) {
      console.log('🛒 Adding to cart:', product.name, quantity)
      
      const existingItem = this.items.find(item => 
        item.id === product.id && 
        item.variant_id === (variant?.id || null)
      )

      if (existingItem) {
        existingItem.quantity += quantity
        console.log('🛒 Updated existing item quantity:', existingItem.quantity)
      } else {
        const newItem = {
          id: product.id,
          name: product.name,
          price: variant?.price || product.price,
          image: variant?.images?.[0]?.url || product.main_image_url || product.image,
          quantity,
          variant_id: variant?.id || null,
          variant_name: variant?.color_name || null,
          size: variant?.size || null,
          stock: variant?.stock || product.stock
        }
        this.items.push(newItem)
        console.log('🛒 Added new item to cart:', newItem)
      }

      this.persistCart()
    },


    removeFromCart(itemId, variantId = null) {
      this.items = this.items.filter(item => 
        !(item.id === itemId && item.variant_id === variantId)
      )
      this.persistCart()
    },

    updateQuantity(itemId, variantId, quantity) {
      const item = this.items.find(item => 
        item.id === itemId && item.variant_id === variantId
      )
      if (item) {
        item.quantity = Math.max(0, quantity)
        if (item.quantity === 0) {
          this.removeFromCart(itemId, variantId)
        } else {
          this.persistCart()
        }
      }
    },

    clearCart() {
      this.items = []
      this.persistCart()
    },

 persistCart() {
      console.log('🛒 Persisting cart with items:', this.items)
      const authStore = useAuthStore()
      if (authStore.isAuthenticated) {
        this.syncCartWithBackend()
      } else {
        localStorage.setItem('cart', JSON.stringify(this.items))
        console.log('🛒 Cart saved to localStorage')
      }
    },

    async syncCartWithBackend() {
      // Implement backend cart sync
      try {
        // await axios.put('/api/cart', { items: this.items })
      } catch (error) {
        console.error('Error syncing cart:', error)
      }
    }
  }
})