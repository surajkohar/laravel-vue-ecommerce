<!-- components/frontend/ProductCard.vue -->
<template>
  <div class="product-card">
    <!-- Product Link -->
    <router-link 
      :to="`/product/${product.id}`" 
      class="product-link"
    >
      <div class="product-image">
        <img :src="product.main_image_url || product.image" :alt="product.name" />
        <div class="product-badges">
          <span v-if="product.stock > 0" class="badge in-stock">IN STOCK</span>
          <span v-else class="badge out-of-stock">OUT OF STOCK</span>
          <span v-if="isOnSale" class="badge sale">SALE</span>
          <span v-if="product.isNew" class="badge new">NEW</span>
        </div>
      </div>
      
      <div class="product-info">
        <h3 class="product-name">{{ product.name }}</h3>
        <p class="product-brand">{{ product.brand || product.category }}</p>
        
        <div class="product-price">
          <span class="current-price">£{{ product.price }}</span>
          <span v-if="isOnSale" class="original-price">£{{ product.originalPrice || product.purchase_price }}</span>
        </div>
      </div>
    </router-link>
    
    <!-- Actions -->
    <div class="product-actions">
      <button 
        class="action-btn wishlist-btn" 
        @click.stop="toggleWishlist" 
        :class="{ 'in-wishlist': isInWishlist }"
        :title="isInWishlist ? 'Remove from wishlist' : 'Add to wishlist'"
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
          <path d="M19.5 12.572L12 20L4.5 12.572C3.605 11.677 3 10.391 3 9C3 6.791 4.791 5 7 5C8.309 5 9.5 5.671 10.214 6.714L12 9L13.786 6.714C14.5 5.671 15.691 5 17 5C19.209 5 21 6.791 21 9C21 10.391 20.395 11.677 19.5 12.572Z" 
                :stroke="isInWishlist ? '#E31B23' : 'currentColor'" 
                :fill="isInWishlist ? '#E31B23' : 'none'"
                stroke-width="2"/>
        </svg>
      </button>
      <button 
        class="action-btn quick-view-btn" 
        @click.stop="emitQuickView"
        title="Quick view"
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
          <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="currentColor" stroke-width="2"/>
          <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="currentColor" stroke-width="2"/>
        </svg>
      </button>
    </div>
    
    <!-- Add to Cart -->
    <div class="product-actions-bottom">
      <button 
        class="btn btn-secondary add-to-cart-btn" 
        @click.stop="addToCart"
        :disabled="product.stock === 0"
        :title="product.stock > 0 ? 'Add to cart' : 'Out of stock'"
      >
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
          <path d="M3 3H5L5.4 5M7 13H17L21 5H5.4M7 13L5.4 5M7 13L4.7 15.3C4.3 15.7 4.6 16.4 5.2 16.4H17M17 13V16.4M9 19C9 19.6 8.6 20 8 20C7.4 20 7 19.6 7 19C7 18.4 7.4 18 8 18C8.6 18 9 18.4 9 19ZM17 19C17 19.6 16.6 20 16 20C15.4 20 15 19.6 15 19C15 18.4 15.4 18 16 18C16.6 18 17 18.4 17 19Z" 
                stroke="currentColor" stroke-width="2"/>
        </svg>
        {{ product.stock > 0 ? 'ADD TO CART' : 'OUT OF STOCK' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useWishlistStore } from '@/store/wishlist'
import { toast } from 'vue3-toastify'

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['add-to-cart', 'quick-view'])

const wishlistStore = useWishlistStore()

const isOnSale = computed(() => {
  return (props.product.originalPrice && parseFloat(props.product.originalPrice) > parseFloat(props.product.price)) ||
         (props.product.purchase_price && parseFloat(props.product.purchase_price) > parseFloat(props.product.price))
})

const isInWishlist = computed(() => {
  return wishlistStore.isInWishlist(props.product.id)
})

const emitQuickView = () => {
  emit('quick-view', props.product)
}

const addToCart = () => {
  if (props.product.stock > 0) {
    emit('add-to-cart', props.product)
    toast.success('Added to cart!')
  } else {
    toast.error('Product is out of stock')
  }
}

const toggleWishlist = () => {
  const wasAdded = wishlistStore.addToWishlist(props.product)
  
  if (wasAdded) {
    toast.success('Added to wishlist!')
  } else {
    toast.info('Removed from wishlist')
  }
}
</script>

<style scoped src="@/assets/styles/frontend.css"></style>

<style scoped>
.product-card {
  background: var(--primary-white);
  border-radius: 8px;
  overflow: hidden;
  box-shadow: var(--shadow-light);
  transition: all 0.3s ease;
  position: relative;
  display: flex;
  flex-direction: column;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-medium);
}

/* Make the product link cover the entire card content except actions */
.product-link {
  text-decoration: none;
  color: inherit;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.product-link:hover {
  text-decoration: none;
}

.product-image {
  position: relative;
  height: 200px;
  overflow: hidden;
  cursor: pointer;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.product-card:hover .product-image img {
  transform: scale(1.05);
}

.product-badges {
  position: absolute;
  top: 10px;
  left: 10px;
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.badge {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 0.65rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.badge.in-stock {
  background: #d4edda;
  color: #155724;
}

.badge.out-of-stock {
  background: #f8d7da;
  color: #721c24;
}

.badge.sale {
  background: var(--primary-red);
  color: var(--primary-white);
}

.badge.new {
  background: #007bff;
  color: var(--primary-white);
}

.product-actions {
  position: absolute;
  top: 10px;
  right: 10px;
  display: flex;
  flex-direction: column;
  gap: 5px;
  opacity: 0;
  transform: translateX(10px);
  transition: all 0.3s ease;
  z-index: 2; /* Ensure actions are above the link */
}

.product-card:hover .product-actions {
  opacity: 1;
  transform: translateX(0);
}

.action-btn {
  width: 32px;
  height: 32px;
  background: var(--primary-white);
  border: none;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: var(--shadow-light);
  color: var(--primary-black);
}

.action-btn:hover {
  background: var(--primary-red);
  color: var(--primary-white);
  transform: scale(1.1);
}

.action-btn.in-wishlist {
  color: var(--primary-red);
}

.product-info {
  padding: 1rem;
  flex: 1;
  display: flex;
  flex-direction: column;
  cursor: pointer;
}

.product-name {
  font-size: 0.9rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: var(--primary-black);
  line-height: 1.3;
  height: 2.6em;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}

.product-brand {
  color: var(--gray-medium);
  font-size: 0.8rem;
  margin-bottom: 0.75rem;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.product-price {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.75rem;
  margin-top: auto; /* Push price to bottom */
}

.current-price {
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--primary-red);
}

.original-price {
  font-size: 0.85rem;
  color: var(--gray-medium);
  text-decoration: line-through;
}

.product-actions-bottom {
  display: flex;
  gap: 0.5rem;
  padding: 0 1rem 1rem;
  margin-top: auto; /* Ensure it stays at bottom */
}

.add-to-cart-btn {
  flex: 1;
  padding: 10px;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

.add-to-cart-btn:disabled {
  background: var(--gray-medium);
  cursor: not-allowed;
}

/* Ensure proper cursor for clickable areas */
.product-link,
.product-image,
.product-info {
  cursor: pointer;
}

/* Responsive */
@media (max-width: 768px) {
  .product-actions {
    opacity: 1;
    transform: translateX(0);
  }
  
  .product-image {
    height: 180px;
  }
}

@media (max-width: 480px) {
  .product-image {
    height: 160px;
  }
  
  .product-info {
    padding: 0.75rem;
  }
  
  .product-actions-bottom {
    padding: 0 0.75rem 0.75rem;
  }
}
</style>