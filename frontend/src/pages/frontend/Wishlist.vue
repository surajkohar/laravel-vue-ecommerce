<template>
  <FrontendLayout>
    <div class="wishlist-page">
      <div class="container">
        <!-- Page Header -->
        <div class="page-header">
          <div class="breadcrumb">
            <router-link to="/" class="breadcrumb-link">Home</router-link>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">My Wishlist</span>
          </div>
          <h1 class="page-title">My Wishlist</h1>
          <p class="page-subtitle">Your saved items for later</p>
          
          <div class="wishlist-stats" v-if="wishlistStore.hasItems">
            <div class="stats-badge">
              <span class="stats-count">{{ wishlistStore.totalItems }}</span>
              <span class="stats-label">Saved Items</span>
            </div>
            <div class="stats-badge">
              <span class="stats-count">{{ inStockCount }}</span>
              <span class="stats-label">In Stock</span>
            </div>
            <div class="stats-badge highlight">
              <span class="stats-count">£{{ totalValue.toFixed(2) }}</span>
              <span class="stats-label">Total Value</span>
            </div>
          </div>
        </div>

        <div class="wishlist-layout">
          <!-- Main Wishlist Content -->
          <div class="wishlist-main">
            <!-- Wishlist Items Section -->
            <div class="wishlist-section" v-if="wishlistStore.hasItems">
              <div class="section-header">
                <div class="section-title">
                  <h2>Saved Items</h2>
                  <p class="section-subtitle">{{ wishlistStore.totalItems }} items in your wishlist</p>
                </div>
                <div class="section-actions">
                  <button 
                    class="btn btn-outline" 
                    @click="selectAllItems"
                    :disabled="selectedItems.length === wishlistStore.totalItems"
                  >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="margin-right: 8px;">
                      <path d="M9 11L12 14L22 4M21 12V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H16" 
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Select All
                  </button>
                  <button 
                    class="btn btn-primary" 
                    @click="addSelectedToCart"
                    :disabled="selectedItems.length === 0 || !hasSelectedInStock"
                  >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="margin-right: 8px;">
                      <path d="M3 3H5L5.4 5M7 13H17L21 5H5.4M7 13L5.4 5M7 13L4.7 15.3C4.3 15.7 4.6 16.4 5.2 16.4H17M17 13V16.4M9 19C9 19.6 8.6 20 8 20C7.4 20 7 19.6 7 19C7 18.4 7.4 18 8 18C8.6 18 9 18.4 9 19ZM17 19C17 19.6 16.6 20 16 20C15.4 20 15 19.6 15 19C15 18.4 15.4 18 16 18C16.6 18 17 18.4 17 19Z" 
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Add Selected ({{ selectedInStockCount }})
                  </button>
                </div>
              </div>
              
              <div class="wishlist-items">
                <div 
                  v-for="item in wishlistStore.items" 
                  :key="item.id" 
                  class="wishlist-item"
                  :class="{ 'selected': selectedItems.includes(item.id) }"
                >
                  <div class="item-selection">
                    <input 
                      type="checkbox" 
                      :id="`item-${item.id}`"
                      :value="item.id"
                      v-model="selectedItems"
                      class="selection-checkbox"
                    >
                    <label :for="`item-${item.id}`" class="selection-label"></label>
                  </div>
                  
                  <div class="item-image">
                    <img :src="item.image" :alt="item.name" @error="handleImageError" />
                    <div class="item-badges">
                      <span class="badge new" v-if="item.is_new">NEW</span>
                      <span class="badge sale" v-if="item.on_sale">SALE</span>
                      <span class="badge out-of-stock" v-if="item.stock === 0">OUT OF STOCK</span>
                    </div>
                    <button class="quick-view-btn" @click="handleQuickView(item)" title="Quick View">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="currentColor" stroke-width="2"/>
                        <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="currentColor" stroke-width="2"/>
                      </svg>
                    </button>
                  </div>
                  
                  <div class="item-details">
                    <div class="item-header">
                      <h3 class="item-name">{{ item.name }}</h3>
                      <button class="remove-btn" @click="removeItem(item)" title="Remove from wishlist">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                          <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                      </button>
                    </div>
                    
                    <div class="item-meta">
                      <span class="item-brand" v-if="item.brand">{{ item.brand.name || item.brand }}</span>
                      <span class="item-category" v-if="item.category">{{ item.category.name || item.category }}</span>
                    </div>
                    
                    <div class="item-stock" :class="{ 'in-stock': item.stock > 0, 'low-stock': item.stock > 0 && item.stock < 10, 'out-of-stock': item.stock === 0 }">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" style="margin-right: 6px;">
                        <circle cx="12" cy="12" r="10" :stroke="item.stock > 0 ? '#22c55e' : '#ef4444'" stroke-width="2"/>
                        <path v-if="item.stock > 0" d="M8 12L11 15L16 9" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path v-else d="M15 9L9 15M9 9L15 15" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                      {{ getStockStatus(item) }}
                    </div>

                    <div class="item-price">
                      <div class="price-main">
                        <span class="current-price">£{{ item.price }}</span>
                        <span class="original-price" v-if="item.originalPrice">£{{ item.originalPrice }}</span>
                      </div>
                      <span class="added-date">Added {{ formatDate(item.addedAt) }}</span>
                    </div>
                  </div>

                  <div class="item-actions">
                    <button 
                      class="btn btn-primary add-to-cart-btn" 
                      @click="addToCart(item)"
                      :disabled="!item.stock || item.stock === 0"
                    >
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="margin-right: 8px;">
                        <path d="M3 3H5L5.4 5M7 13H17L21 5H5.4M7 13L5.4 5M7 13L4.7 15.3C4.3 15.7 4.6 16.4 5.2 16.4H17M17 13V16.4M9 19C9 19.6 8.6 20 8 20C7.4 20 7 19.6 7 19C7 18.4 7.4 18 8 18C8.6 18 9 18.4 9 19ZM17 19C17 19.6 16.6 20 16 20C15.4 20 15 19.6 15 19C15 18.4 15.4 18 16 18C16.6 18 17 18.4 17 19Z" 
                              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                      {{ item.stock > 0 ? 'Add to Cart' : 'Out of Stock' }}
                    </button>
                    
                    <div class="secondary-actions">
                      <button class="compare-btn" @click="addToCompare(item)" title="Add to Compare">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                          <path d="M8 6H21M8 12H21M8 18H21M3 6H3.01M3 12H3.01M3 18H3.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Compare
                      </button>
                      <button class="share-btn" @click="shareItem(item)" title="Share Product">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                          <path d="M4 12V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V12M16 6L12 2M12 2L8 6M12 2V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Share
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty Wishlist State -->
            <div class="empty-state" v-else-if="!wishlistStore.loading">
              <div class="empty-state-content">
                <div class="empty-icon">
                  <svg width="120" height="120" viewBox="0 0 24 24" fill="none">
                    <path d="M12 21.35L10.55 20.03C5.4 15.36 2 12.28 2 8.5C2 5.42 4.42 3 7.5 3C9.24 3 10.91 3.81 12 5.09C13.09 3.81 14.76 3 16.5 3C19.58 3 22 5.42 22 8.5C22 12.28 18.6 15.36 13.45 20.04L12 21.35Z" 
                          stroke="var(--primary-red)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="var(--primary-red)" fill-opacity="0.1"/>
                  </svg>
                </div>
                <h3>Your wishlist is empty</h3>
                <p>Start adding items you love to your wishlist. They'll be saved here for easy access later.</p>
                <div class="empty-actions">
                  <router-link to="/products" class="btn btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="margin-right: 8px;">
                      <path d="M6 2L3 6V20C3 20.5304 3.21071 21.0391 3.58579 21.4142C3.96086 21.7893 4.46957 22 5 22H19C19.5304 22 20.0391 21.7893 20.4142 21.4142C20.7893 21.0391 21 20.5304 21 20V6L18 2H6Z" 
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M16 10C16 11.0609 15.5786 12.0783 14.8284 12.8284C14.0783 13.5786 13.0609 14 12 14C10.9391 14 9.92172 13.5786 9.17157 12.8284C8.42143 12.0783 8 11.0609 8 10" 
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Start Shopping
                  </router-link>
                  <router-link to="/categories" class="btn btn-outline">
                    Browse Categories
                  </router-link>
                </div>
              </div>
            </div>

            <!-- Loading State -->
            <div class="loading-state" v-if="wishlistStore.loading">
              <div class="spinner"></div>
              <p>Loading your wishlist...</p>
            </div>

            <!-- Benefits Section -->
            <div class="benefits-section" v-if="!wishlistStore.hasItems && !wishlistStore.loading">
              <h3>Why Create a Wishlist?</h3>
              <div class="benefits-grid">
                <div class="benefit-card">
                  <div class="benefit-icon">💝</div>
                  <h4>Save Favorites</h4>
                  <p>Keep all your favorite items in one place for easy access</p>
                </div>
                <div class="benefit-card">
                  <div class="benefit-icon">🔔</div>
                  <h4>Price Alerts</h4>
                  <p>Get notified when items you love go on sale</p>
                </div>
                <div class="benefit-card">
                  <div class="benefit-icon">📦</div>
                  <h4>Stock Updates</h4>
                  <p>Know when out-of-stock items become available again</p>
                </div>
                <div class="benefit-card">
                  <div class="benefit-icon">🛒</div>
                  <h4>Quick Cart</h4>
                  <p>Add multiple items to your cart with one click</p>
                </div>
              </div>
            </div>

            <!-- Recommendations Section -->
            <div class="recommendations-section" v-if="wishlistStore.hasItems">
              <div class="section-header">
                <div class="section-title">
                  <h2>You Might Also Like</h2>
                  <p class="section-subtitle">Based on your wishlist items</p>
                </div>
                <router-link to="/products" class="view-all-link">
                  View All Products
                </router-link>
              </div>
              <div class="recommendations-grid">
                <ProductCard 
                  v-for="product in recommendedProducts" 
                  :key="product.id"
                  :product="product"
                  class="recommendation-card"
                />
              </div>
            </div>
          </div>

          <!-- Wishlist Sidebar -->
          <div class="wishlist-sidebar" v-if="wishlistStore.hasItems">
            <div class="summary-card">
              <div class="summary-header">
                <h3 class="summary-title">Wishlist Summary</h3>
                <button class="clear-wishlist-btn" @click="clearWishlist" title="Clear entire wishlist">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <path d="M3 6H5H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" 
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </button>
              </div>
              
              <div class="summary-stats">
                <div class="stat-item">
                  <span class="stat-label">Total Items</span>
                  <span class="stat-value">{{ wishlistStore.totalItems }}</span>
                </div>
                <div class="stat-item">
                  <span class="stat-label">Selected Items</span>
                  <span class="stat-value">{{ selectedItems.length }}</span>
                </div>
                <div class="stat-item">
                  <span class="stat-label">In Stock</span>
                  <span class="stat-value success">{{ inStockCount }}</span>
                </div>
                <div class="stat-item">
                  <span class="stat-label">Out of Stock</span>
                  <span class="stat-value warning">{{ outOfStockCount }}</span>
                </div>
              </div>

              <div class="summary-divider"></div>

              <div class="summary-total">
                <span class="total-label">Total Value</span>
                <span class="total-amount">£{{ totalValue.toFixed(2) }}</span>
              </div>

              <div class="selected-value" v-if="selectedItems.length > 0">
                <span class="selected-label">Selected Value</span>
                <span class="selected-amount">£{{ selectedValue.toFixed(2) }}</span>
              </div>

              <button 
                class="btn btn-primary add-all-btn" 
                @click="addAllToCart"
                :disabled="inStockCount === 0"
              >
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" style="margin-right: 8px;">
                  <path d="M3 3H5L5.4 5M7 13H17L21 5H5.4M7 13L5.4 5M7 13L4.7 15.3C4.3 15.7 4.6 16.4 5.2 16.4H17M17 13V16.4M9 19C9 19.6 8.6 20 8 20C7.4 20 7 19.6 7 19C7 18.4 7.4 18 8 18C8.6 18 9 18.4 9 19ZM17 19C17 19.6 16.6 20 16 20C15.4 20 15 19.6 15 19C15 18.4 15.4 18 16 18C16.6 18 17 18.4 17 19Z" 
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Add All to Cart
              </button>

              <router-link to="/products" class="btn btn-outline continue-shopping">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="margin-right: 8px;">
                  <path d="M19 12H5M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Continue Shopping
              </router-link>

              <div class="wishlist-tips">
                <h4>Wishlist Tips</h4>
                <div class="tip-item">
                  <div class="tip-icon">⭐</div>
                  <div class="tip-text">Items are saved for 90 days</div>
                </div>
                <div class="tip-item">
                  <div class="tip-icon">🔔</div>
                  <div class="tip-text">Get sale alerts for wishlist items</div>
                </div>
                <div class="tip-item">
                  <div class="tip-icon">📱</div>
                  <div class="tip-text">Access your wishlist on any device</div>
                </div>
              </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions-card">
              <h4>Quick Actions</h4>
              <div class="action-buttons">
                <button class="action-btn" @click="exportWishlist">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M21 15V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V15" 
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 15V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  Export List
                </button>
                <button class="action-btn" @click="shareWishlist">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M4 12V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V12M16 6L12 2M12 2L8 6M12 2V15" 
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  Share List
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick View Modal -->
      <QuickViewModal 
        v-if="quickViewProduct"
        :product="quickViewProduct"
        @close="quickViewProduct = null"
      />
    </div>
  </FrontendLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useWishlistStore } from '@/store/wishlist'
import { useCartStore } from '@/store/cart'
import FrontendLayout from '@/layouts/FrontendLayout.vue'
import ProductCard from '@/components/frontend/ProductCard.vue'
import QuickViewModal from '@/components/frontend/QuickViewModal.vue'

const wishlistStore = useWishlistStore()
const cartStore = useCartStore()

const selectedItems = ref([])
const quickViewProduct = ref(null)

// Computed properties
const hasSelectedInStock = computed(() => {
  return selectedItems.value.some(itemId => {
    const item = wishlistStore.items.find(item => item.id === itemId)
    return item?.stock > 0
  })
})

const inStockCount = computed(() => {
  return wishlistStore.items.filter(item => item.stock > 0).length
})

const outOfStockCount = computed(() => {
  return wishlistStore.items.filter(item => item.stock === 0).length
})

const selectedInStockCount = computed(() => {
  return selectedItems.value.filter(itemId => {
    const item = wishlistStore.items.find(item => item.id === itemId)
    return item?.stock > 0
  }).length
})

const totalValue = computed(() => {
  return wishlistStore.items.reduce((total, item) => {
    return total + parseFloat(item.price || 0)
  }, 0)
})

const selectedValue = computed(() => {
  return selectedItems.value.reduce((total, itemId) => {
    const item = wishlistStore.items.find(item => item.id === itemId)
    return total + (item ? parseFloat(item.price || 0) : 0)
  }, 0)
})

const recommendedProducts = computed(() => {
  if (wishlistStore.items.length === 0) return []
  
  // Mock recommended products - in real app, this would come from an API
  return [
    {
      id: 101,
      name: 'Classic Polo Shirt',
      price: 29.99,
      image: '/images/products/polo-classic.jpg',
      stock: 15,
      brand: 'Pinders',
      is_new: true
    },
    {
      id: 102,
      name: 'Workwear Jacket',
      price: 79.99,
      image: '/images/products/jacket-workwear.jpg',
      stock: 8,
      brand: 'Dickies',
      on_sale: true
    },
    {
      id: 103,
      name: 'Safety Boots',
      price: 59.99,
      image: '/images/products/boots-safety.jpg',
      stock: 12,
      brand: 'CAT'
    },
    {
      id: 104,
      name: 'Hi-Vis Vest',
      price: 19.99,
      image: '/images/products/vest-hivis.jpg',
      stock: 25,
      brand: 'Portwest',
      is_new: true
    }
  ]
})

// Methods
const handleImageError = (event) => {
  event.target.src = '/images/placeholder-product.jpg'
}

const getStockStatus = (item) => {
  if (!item.stock || item.stock === 0) return 'Out of Stock'
  if (item.stock > 10) return 'In Stock'
  if (item.stock > 0) return `Only ${item.stock} left`
  return 'In Stock'
}

const formatDate = (dateString) => {
  if (!dateString) return 'recently'
  
  const date = new Date(dateString)
  const now = new Date()
  const diffTime = Math.abs(now - date)
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  
  if (diffDays === 1) return 'yesterday'
  if (diffDays < 7) return `${diffDays} days ago`
  if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`
  return `${Math.floor(diffDays / 30)} months ago`
}

const addToCart = async (item) => {
  try {
    // Pass the correct data structure
    await cartStore.addToCart({
      id: item.id,
      name: item.name,
      price: item.price,
      image: item.image || item.main_image_url,
      quantity: 1,
      stock: item.stock
    })
    // Show success message (you can use toast here)
  } catch (error) {
    console.error('Error adding to cart:', error)
  }
}

const removeItem = (item) => {
  if (confirm(`Remove "${item.name}" from your wishlist?`)) {
    wishlistStore.removeFromWishlist(item.id)
    const index = selectedItems.value.indexOf(item.id)
    if (index > -1) {
      selectedItems.value.splice(index, 1)
    }
  }
}

const clearWishlist = () => {
  if (confirm('Clear your entire wishlist?')) {
    wishlistStore.clearWishlist()
    selectedItems.value = []
  }
}

const selectAllItems = () => {
  selectedItems.value = wishlistStore.items.map(item => item.id)
}

const addSelectedToCart = async () => {
  const selectedProducts = wishlistStore.items.filter(item => 
    selectedItems.value.includes(item.id) && item.stock > 0
  )
  
  if (selectedProducts.length === 0) return

  try {
    for (const product of selectedProducts) {
      await cartStore.addToCart({
        product_id: product.id,
        quantity: 1,
        product: product
      })
    }
    // Show success message
  } catch (error) {
    console.error('Error adding selected items to cart:', error)
  }
}

const addAllToCart = async () => {
  const inStockProducts = wishlistStore.items.filter(item => item.stock > 0)
  
  if (inStockProducts.length === 0) return

  try {
    for (const product of inStockProducts) {
      await cartStore.addToCart({
        product_id: product.id,
        quantity: 1,
        product: product
      })
    }
    // Show success message
  } catch (error) {
    console.error('Error adding all items to cart:', error)
  }
}

const handleQuickView = (product) => {
  quickViewProduct.value = product
}

const addToCompare = (item) => {
  // Implement compare functionality
  console.log('Add to compare:', item)
}

const shareItem = (item) => {
  // Implement share functionality
  console.log('Share item:', item)
}

const exportWishlist = () => {
  // Implement export functionality
  console.log('Export wishlist')
}

const shareWishlist = () => {
  // Implement share wishlist functionality
  console.log('Share wishlist')
}

// Lifecycle
onMounted(() => {
  wishlistStore.loadWishlist()
})
</script>

<style scoped src="@/assets/styles/frontend.css"></style>

<style scoped>
.wishlist-page {
  min-height: 100vh;
  background: linear-gradient(135deg, var(--gray-light) 0%, #f1f5f9 100%);
  padding: 2rem 0 4rem;
}

/* Page Header Improvements */
.page-header {
  text-align: center;
  margin-bottom: 3rem;
}

.wishlist-stats {
  display: flex;
  justify-content: center;
  gap: 1.5rem;
  margin-top: 2rem;
}

.stats-badge {
  background: var(--primary-white);
  padding: 1rem 1.5rem;
  border-radius: 12px;
  box-shadow: var(--shadow-light);
  text-align: center;
  min-width: 120px;
}

.stats-badge.highlight {
  background: linear-gradient(135deg, var(--primary-red), #ff6b6b);
  color: var(--primary-white);
}

.stats-count {
  display: block;
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 0.25rem;
}

.stats-label {
  font-size: 0.85rem;
  opacity: 0.8;
}

/* Wishlist Items Redesign */
.wishlist-item {
  display: grid;
  grid-template-columns: auto 120px 1fr auto;
  gap: 1.5rem;
  align-items: start;
  padding: 1.5rem 2rem;
  border-bottom: 1px solid #f5f5f5;
  transition: all 0.3s ease;
  position: relative;
  background: var(--primary-white);
}

.wishlist-item:hover {
  background: #fafafa;
  transform: translateY(-2px);
  box-shadow: var(--shadow-medium);
}

.wishlist-item.selected {
  background: #fef2f2;
  border-left: 4px solid var(--primary-red);
}

.wishlist-item:last-child {
  border-bottom: none;
}

/* Item Selection */
.item-selection {
  display: flex;
  align-items: flex-start;
  padding-top: 0.5rem;
}

.selection-checkbox {
  display: none;
}

.selection-label {
  width: 20px;
  height: 20px;
  border: 2px solid var(--border-color);
  border-radius: 4px;
  cursor: pointer;
  position: relative;
  transition: all 0.3s ease;
}

.selection-checkbox:checked + .selection-label {
  background: var(--primary-red);
  border-color: var(--primary-red);
}

.selection-checkbox:checked + .selection-label::after {
  content: '✓';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: var(--primary-white);
  font-size: 12px;
  font-weight: bold;
}

/* Item Image */
.item-image {
  position: relative;
  width: 120px;
  height: 120px;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: var(--shadow-light);
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-badges {
  position: absolute;
  top: 8px;
  left: 8px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.badge {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
}

.badge.new {
  background: #22c55e;
  color: var(--primary-white);
}

.badge.sale {
  background: var(--primary-red);
  color: var(--primary-white);
}

.badge.out-of-stock {
  background: #6b7280;
  color: var(--primary-white);
}

.quick-view-btn {
  position: absolute;
  bottom: 8px;
  right: 8px;
  background: var(--primary-white);
  border: none;
  border-radius: 6px;
  padding: 6px;
  cursor: pointer;
  opacity: 0;
  transition: all 0.3s ease;
  box-shadow: var(--shadow-light);
}

.item-image:hover .quick-view-btn {
  opacity: 1;
}

/* Item Details */
.item-details {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.item-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1rem;
}

.item-name {
  margin: 0;
  color: var(--primary-black);
  font-size: 1.1rem;
  font-weight: 600;
  line-height: 1.3;
  flex: 1;
}

.remove-btn {
  background: none;
  border: none;
  color: var(--gray-medium);
  cursor: pointer;
  padding: 6px;
  border-radius: 6px;
  transition: all 0.3s ease;
  flex-shrink: 0;
}

.remove-btn:hover {
  background: #fee;
  color: var(--primary-red);
}

.item-meta {
  display: flex;
  gap: 12px;
}

.item-brand,
.item-category {
  background: var(--gray-light);
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  color: var(--gray-medium);
  font-weight: 500;
}

.item-stock {
  display: flex;
  align-items: center;
  font-size: 13px;
  font-weight: 500;
  padding: 4px 8px;
  border-radius: 4px;
  width: fit-content;
}

.item-stock.in-stock {
  background: #f0fdf4;
  color: #166534;
}

.item-stock.low-stock {
  background: #fef3f2;
  color: #b91c1c;
}

.item-stock.out-of-stock {
  background: #f3f4f6;
  color: #6b7280;
}

.item-price {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
}

.price-main {
  display: flex;
  align-items: center;
  gap: 8px;
}

.current-price {
  font-size: 1.3rem;
  font-weight: 700;
  color: var(--primary-red);
}

.original-price {
  font-size: 1rem;
  color: var(--gray-medium);
  text-decoration: line-through;
}

.added-date {
  font-size: 0.8rem;
  color: var(--gray-medium);
  font-style: italic;
}

/* Item Actions */
.item-actions {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  min-width: 200px;
}

.add-to-cart-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 10px 16px;
  font-weight: 600;
  font-size: 14px;
}

.secondary-actions {
  display: flex;
  gap: 0.5rem;
}

.compare-btn,
.share-btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 8px 12px;
  background: var(--primary-white);
  border: 1px solid var(--border-color);
  border-radius: 6px;
  color: var(--gray-medium);
  font-size: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.compare-btn:hover,
.share-btn:hover {
  border-color: var(--primary-red);
  color: var(--primary-red);
  background: #fef2f2;
}

/* Benefits Section */
.benefits-section {
  background: var(--primary-white);
  border-radius: 16px;
  box-shadow: var(--shadow-medium);
  padding: 3rem 2rem;
  text-align: center;
}

.benefits-section h3 {
  color: var(--primary-black);
  margin-bottom: 2rem;
  font-size: 1.5rem;
}

.benefits-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
  max-width: 1000px;
  margin: 0 auto;
}

.benefit-card {
  padding: 2rem 1.5rem;
  border-radius: 12px;
  background: #f8fafc;
  transition: transform 0.3s ease;
}

.benefit-card:hover {
  transform: translateY(-5px);
}

.benefit-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.benefit-card h4 {
  color: var(--primary-black);
  margin-bottom: 0.5rem;
  font-size: 1.1rem;
}

.benefit-card p {
  color: var(--gray-medium);
  font-size: 0.9rem;
  line-height: 1.5;
}

/* Sidebar Improvements */
.summary-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.clear-wishlist-btn {
  background: none;
  border: none;
  color: var(--gray-medium);
  cursor: pointer;
  padding: 6px;
  border-radius: 6px;
  transition: all 0.3s ease;
}

.clear-wishlist-btn:hover {
  background: #fee;
  color: var(--primary-red);
}

.summary-stats {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.stat-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  border-bottom: 1px solid #f5f5f5;
}

.stat-label {
  color: var(--gray-medium);
  font-size: 14px;
}

.stat-value {
  font-weight: 600;
  color: var(--primary-black);
}

.stat-value.success {
  color: #22c55e;
}

.stat-value.warning {
  color: #ef4444;
}

.selected-value {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 0;
  background: #fef2f2;
  margin: 1rem -2rem;
  padding: 1rem 2rem;
  border-radius: 8px;
}

.selected-label {
  color: var(--primary-black);
  font-weight: 600;
}

.selected-amount {
  font-size: 1.2rem;
  font-weight: 700;
  color: var(--primary-red);
}

.add-all-btn,
.continue-shopping {
  width: 100%;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.wishlist-tips {
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid var(--border-color);
}

.wishlist-tips h4 {
  color: var(--primary-black);
  margin-bottom: 1rem;
  font-size: 1rem;
}

.tip-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem 0;
}

.tip-icon {
  font-size: 1.2rem;
  flex-shrink: 0;
}

.tip-text {
  color: var(--gray-medium);
  font-size: 0.85rem;
}

/* Quick Actions */
.quick-actions-card {
  background: var(--primary-white);
  border-radius: 12px;
  box-shadow: var(--shadow-light);
  padding: 1.5rem;
  margin-top: 1.5rem;
}

.quick-actions-card h4 {
  color: var(--primary-black);
  margin-bottom: 1rem;
  font-size: 1rem;
}

.action-buttons {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 12px;
  background: var(--primary-white);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  color: var(--gray-dark);
  font-size: 14px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.action-btn:hover {
  border-color: var(--primary-red);
  color: var(--primary-red);
  background: #fef2f2;
}

/* Empty State Improvements */
.empty-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
  flex-wrap: wrap;
}

/* Recommendations */
.recommendation-card {
  transform: scale(0.95);
  transition: transform 0.3s ease;
}

.recommendation-card:hover {
  transform: scale(1);
}

/* Responsive Design */
@media (max-width: 1024px) {
  .wishlist-layout {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .wishlist-sidebar {
    position: static;
  }
}

@media (max-width: 768px) {
  .wishlist-stats {
    flex-direction: column;
    align-items: center;
    gap: 1rem;
  }
  
  .stats-badge {
    width: 100%;
    max-width: 200px;
  }
  
  .wishlist-item {
    grid-template-columns: 1fr;
    gap: 1rem;
    padding: 1.5rem;
    text-align: center;
  }
  
  .item-selection {
    position: absolute;
    top: 1rem;
    left: 1rem;
  }
  
  .item-header {
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
  }
  
  .item-price {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .item-actions {
    min-width: auto;
  }
  
  .benefits-grid {
    grid-template-columns: 1fr;
  }
  
  .empty-actions {
    flex-direction: column;
    align-items: center;
  }
  
  .empty-actions .btn {
    width: 100%;
    max-width: 250px;
  }
}

@media (max-width: 480px) {
  .secondary-actions {
    flex-direction: column;
  }
  
  .action-buttons {
    flex-direction: column;
  }
}
</style>