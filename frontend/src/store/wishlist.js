import { defineStore } from 'pinia'
import { useAuthStore } from './auth'
import api from '@/services/api'

export const useWishlistStore = defineStore('wishlist', {
  state: () => ({
    items: [],
    loading: false,
    wishlistId: null
  }),

  getters: {
    totalItems: (state) => state.items.length,
    hasItems: (state) => state.items.length > 0,
    isInWishlist: (state) => (productId, variantId = null, sizeId = null) => {
      return state.items.some(item => {
        return item.id === productId &&
          item.variant_id === variantId &&
          item.size_id === sizeId
      })
    }
  },

  actions: {
    async loadWishlist() {
      this.loading = true
      try {
        const response = await api.get('/frontend/wishlist')
        if (response.data.status) {
          this.items = response.data.data.items
          this.wishlistId = response.data.data.wishlist_id
        }
      } catch (error) {
        console.error('Error loading wishlist:', error)
      } finally {
        this.loading = false
      }
    },

    async toggleItem(product) {
      try {
        const payload = {
          product_id: product.id,
          variant_id: product.variant_id || null,
          size_id: product.size_id || null
        }

        const response = await api.post('/frontend/wishlist/add', payload)
        
        if (response.data.status) {
          if (response.data.data.action === 'added') {
            // Add to local state
            this.items.push({
              id: product.id,
              name: product.name,
              price: product.price,
              image: product.image || product.main_image_url,
              variant_id: product.variant_id,
              variant_name: product.variant_name,
              size_id: product.size_id,
              size_title: product.size_title,
              stock: product.stock,
              has_stock: product.has_stock,
              added_at: new Date().toISOString(),
              wishlist_item_id: response.data.data.wishlist_item_id
            })
          } else {
            // Remove from local state
            this.items = this.items.filter(item => 
              !(item.id === product.id &&
                item.variant_id === product.variant_id &&
                item.size_id === product.size_id)
            )
          }
          return response.data.data.in_wishlist
        }
        return false
      } catch (error) {
        console.error('Error toggling wishlist item:', error)
        return false
      }
    },

    async checkStatus(productId, variantId = null, sizeId = null) {
      try {
        const response = await api.get('/frontend/wishlist/check', {
          params: {
            product_id: productId,
            variant_id: variantId,
            size_id: sizeId
          }
        })
        return response.data.data.in_wishlist
      } catch (error) {
        console.error('Error checking wishlist status:', error)
        return false
      }
    },

    async removeItem(itemId) {
      try {
        const response = await api.delete(`/frontend/wishlist/${itemId}`)
        if (response.data.status) {
          // Remove from local state
          this.items = this.items.filter(item => item.wishlist_item_id !== itemId)
          return true
        }
        return false
      } catch (error) {
        console.error('Error removing wishlist item:', error)
        return false
      }
    },

    async clearWishlist() {
      try {
        const response = await api.delete('/frontend/wishlist/clear')
        if (response.data.status) {
          this.items = []
          return true
        }
        return false
      } catch (error) {
        console.error('Error clearing wishlist:', error)
        return false
      }
    },

    async moveToCart(itemId) {
      try {
        const response = await api.post(`/frontend/wishlist/move-to-cart/${itemId}`)
        if (response.data.status) {
          // Remove from local state
          this.items = this.items.filter(item => item.wishlist_item_id !== itemId)
          return response.data.data
        }
        return null
      } catch (error) {
        console.error('Error moving item to cart:', error)
        return null
      }
    }
  }
})