<!-- components/frontend/ProductCard.vue -->
<template>
  <div class="product-card">
    <div class="product-image">
      <img :src="product.main_image_url" :alt="product.name" />
      <div class="product-badges">
        <span v-if="product.stock > 0" class="badge in-stock">IN STOCK</span>
        <span v-else class="badge out-of-stock">OUT OF STOCK</span>
        <span v-if="isOnSale" class="badge sale">SALE</span>
      </div>
      <div class="product-actions">
        <button class="action-btn wishlist-btn" @click.stop="toggleWishlist" :class="{ 'in-wishlist': isInWishlist }">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
            <path d="M19.5 12.572L12 20L4.5 12.572C3.605 11.677 3 10.391 3 9C3 6.791 4.791 5 7 5C8.309 5 9.5 5.671 10.214 6.714L12 9L13.786 6.714C14.5 5.671 15.691 5 17 5C19.209 5 21 6.791 21 9C21 10.391 20.395 11.677 19.5 12.572Z" 
                  :stroke="isInWishlist ? '#E31B23' : 'currentColor'" 
                  :fill="isInWishlist ? '#E31B23' : 'none'"
                  stroke-width="2"/>
          </svg>
        </button>
        <button class="action-btn quick-view-btn" @click.stop="emitQuickView">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
            <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="currentColor" stroke-width="2"/>
            <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="currentColor" stroke-width="2"/>
          </svg>
        </button>
      </div>
    </div>
    
    <div class="product-info">
      <h3 class="product-name">{{ product.name }}</h3>
      <p class="product-brand">{{ product.brand }}</p>
      
      <div class="product-price">
        <span class="current-price">£{{ product.price }}</span>
        <span v-if="isOnSale" class="original-price">£{{ product.purchase_price }}</span>
      </div>
      
      <div class="product-actions-bottom">
        <button 
          class="add-to-cart-btn" 
          @click.stop="addToCart"
          :disabled="product.stock === 0"
        >
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <path d="M3 3H5L5.4 5M7 13H17L21 5H5.4M7 13L5.4 5M7 13L4.7 15.3C4.3 15.7 4.6 16.4 5.2 16.4H17M17 13V16.4M9 19C9 19.6 8.6 20 8 20C7.4 20 7 19.6 7 19C7 18.4 7.4 18 8 18C8.6 18 9 18.4 9 19ZM17 19C17 19.6 16.6 20 16 20C15.4 20 15 19.6 15 19C15 18.4 15.4 18 16 18C16.6 18 17 18.4 17 19Z" 
                  stroke="currentColor" stroke-width="2"/>
          </svg>
          {{ product.stock > 0 ? 'ADD TO CART' : 'OUT OF STOCK' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['add-to-cart', 'quick-view'])

const isOnSale = computed(() => {
  return props.product.purchase_price && parseFloat(props.product.purchase_price) > parseFloat(props.product.price)
})

const isInWishlist = computed(() => {
  return false
})

const emitQuickView = () => {
  emit('quick-view', props.product)
}

const addToCart = () => {
  if (props.product.stock > 0) {
    emit('add-to-cart', props.product)
  }
}

const toggleWishlist = () => {
  console.log('Toggle wishlist for:', props.product.id)
}
</script>

<style scoped>
.product-card {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  position: relative;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.product-image {
  position: relative;
  height: 200px;
  overflow: hidden;
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
  color: white;
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
}

.product-card:hover .product-actions {
  opacity: 1;
  transform: translateX(0);
}

.action-btn {
  width: 32px;
  height: 32px;
  background: white;
  border: none;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.action-btn:hover {
  background: var(--primary-red);
  color: white;
  transform: scale(1.1);
}

.action-btn.in-wishlist {
  color: var(--primary-red);
}

.product-info {
  padding: 1rem;
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
}

.add-to-cart-btn {
  flex: 1;
  padding: 10px;
  background: var(--primary-black);
  color: white;
  border: none;
  border-radius: 4px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s ease;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

.add-to-cart-btn:hover:not(:disabled) {
  background: var(--primary-red);
}

.add-to-cart-btn:disabled {
  background: var(--gray-medium);
  cursor: not-allowed;
}

/* Responsive */
@media (max-width: 768px) {
  .product-actions {
    opacity: 1;
    transform: translateX(0);
  }
}
</style>