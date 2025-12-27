<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="quick-view-modal">
      <button class="close-btn" @click="$emit('close')">✕</button>
      
      <div class="modal-content" v-if="product">
        <div class="product-images">
          <img :src="product.main_image_url" :alt="product.name" />
          <div class="product-badges">
            <span v-if="product.isNew" class="badge new">New</span>
            <span v-if="product.onSale" class="badge sale">Sale</span>
            <span v-if="!product.has_stock" class="badge out-of-stock">Out of Stock</span>
          </div>
        </div>
        
        <div class="product-details">
          <div class="product-header">
            <h2>{{ product.name }}</h2>
            <div class="product-price">£{{ product.price }}</div>
          </div>
          
          <div class="product-meta">
            <span v-if="product.brand_title" class="product-brand">
              Brand: {{ product.brand_title }}
            </span>
            <span v-if="product.category_title" class="product-category">
              Category: {{ product.category_title }}
            </span>
          </div>
          
          <div class="stock-status" :class="{ 'in-stock': product.has_stock, 'out-of-stock': !product.has_stock }">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
              <circle cx="12" cy="12" r="10" :stroke="product.has_stock ? '#22c55e' : '#ef4444'" stroke-width="2"/>
              <path v-if="product.has_stock" d="M8 12L11 15L16 9" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path v-else d="M15 9L9 15M9 9L15 15" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            {{ product.has_stock ? 'In Stock' : 'Out of Stock' }}
          </div>
          
          <div class="product-description">
            <h4>Description</h4>
            <p>{{ truncateDescription(product.description) }}</p>
          </div>
          
          <!-- Show this message if product has variants -->
          <div class="variant-notice" v-if="product.variant_count > 0 || product.has_variants">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
              <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" 
                    stroke="currentColor" stroke-width="2"/>
              <path d="M12 8V12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              <path d="M12 16H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <div>
              <strong>Multiple Options Available</strong>
              <p>View full details to select your preferred color and size</p>
            </div>
          </div>
          
          <div class="quick-actions">
            <!-- Only View Details button, no Add to Cart -->
            <button class="view-details-btn" @click="viewProductDetails">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="margin-right: 8px;">
                <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" 
                      stroke="currentColor" stroke-width="2"/>
                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
              </svg>
              View Full Details
            </button>
          </div>
          
          <!-- Optional: Add to Wishlist button -->
          <div class="secondary-actions">
            <button class="wishlist-btn" @click="toggleWishlist">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" :class="{ 'filled': isInWishlist }">
                <path d="M19.5 12.572L12 20L4.5 12.572C3.605 11.677 3 10.391 3 9C3 6.791 4.791 5 7 5C8.309 5 9.5 5.671 10.214 6.714L12 9L13.786 6.714C14.5 5.671 15.691 5 17 5C19.209 5 21 6.791 21 9C21 10.391 20.395 11.677 19.5 12.572Z" 
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              {{ isInWishlist ? 'Remove from Wishlist' : 'Add to Wishlist' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useWishlistStore } from '@/store/wishlist'

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['close'])
const router = useRouter()
const wishlistStore = useWishlistStore()

// Check if product is in wishlist
const isInWishlist = computed(() => {
  return wishlistStore.isInWishlist(props.product.id)
})

// Truncate description for modal view
const truncateDescription = (description) => {
  if (!description) return 'No description available.'
  // Remove HTML tags and truncate
  const plainText = description.replace(/<[^>]*>/g, ' ')
  if (plainText.length > 200) {
    return plainText.substring(0, 200) + '...'
  }
  return plainText
}

// Navigate to full product details page
const viewProductDetails = () => {
  emit('close')
  if (props.product.slug) {
    router.push(`/products/${props.product.slug}`)
  } else {
    router.push(`/products/id/${props.product.id}`)
  }
}

// Toggle wishlist
const toggleWishlist = async () => {
  try {
    const productData = {
      id: props.product.id,
      name: props.product.name,
      price: props.product.price,
      image: props.product.main_image_url,
      stock: props.product.stock || 0
    }
    
    await wishlistStore.toggleItem(productData)
  } catch (error) {
    console.error('Error toggling wishlist:', error)
  }
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.quick-view-modal {
  background: white;
  border-radius: 16px;
  max-width: 800px;
  width: 100%;
  max-height: 90vh;
  overflow: hidden;
  position: relative;
}

.close-btn {
  position: absolute;
  top: 15px;
  right: 15px;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  cursor: pointer;
  z-index: 10;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
}

.modal-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  height: 100%;
}

.product-images {
  background: #f8f9fa;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  position: relative;
}

.product-images img {
  width: 100%;
  max-height: 400px;
  object-fit: contain;
  border-radius: 8px;
}

.product-badges {
  position: absolute;
  top: 20px;
  left: 20px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.badge {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}

.badge.new {
  background: #10b981;
  color: white;
}

.badge.sale {
  background: #ef4444;
  color: white;
}

.badge.out-of-stock {
  background: #6b7280;
  color: white;
}

.product-details {
  padding: 2rem;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
}

.product-header {
  margin-bottom: 1rem;
}

.product-header h2 {
  margin: 0 0 0.5rem 0;
  color: #1f2937;
  font-size: 1.5rem;
  font-weight: 600;
}

.product-price {
  font-size: 1.5rem;
  font-weight: 700;
  color: #dc2626;
}

.product-meta {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 1rem;
}

.product-brand,
.product-category {
  color: #6b7280;
  font-size: 0.9rem;
}

.stock-status {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  border-radius: 8px;
  margin-bottom: 1.5rem;
  font-weight: 500;
}

.stock-status.in-stock {
  background: #f0fdf4;
  color: #166534;
}

.stock-status.out-of-stock {
  background: #fef2f2;
  color: #991b1b;
}

.product-description {
  margin-bottom: 1.5rem;
}

.product-description h4 {
  margin: 0 0 0.5rem 0;
  color: #374151;
  font-size: 1rem;
  font-weight: 600;
}

.product-description p {
  color: #6b7280;
  line-height: 1.6;
  margin: 0;
}

.variant-notice {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 16px;
  background: #fef3c7;
  border: 1px solid #fbbf24;
  border-radius: 8px;
  margin-bottom: 1.5rem;
}

.variant-notice svg {
  color: #d97706;
  flex-shrink: 0;
}

.variant-notice strong {
  color: #92400e;
  display: block;
  margin-bottom: 4px;
}

.variant-notice p {
  color: #92400e;
  margin: 0;
  font-size: 0.9rem;
  opacity: 0.9;
}

.quick-actions {
  margin-bottom: 1rem;
}

.view-details-btn {
  width: 100%;
  padding: 14px 24px;
  background: #3b82f6;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.view-details-btn:hover {
  background: #2563eb;
}

.secondary-actions {
  display: flex;
  justify-content: center;
}

.wishlist-btn {
  background: none;
  border: 1px solid #d1d5db;
  color: #4b5563;
  padding: 10px 20px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s;
}

.wishlist-btn:hover {
  background: #f9fafb;
  border-color: #9ca3af;
}

.wishlist-btn svg.filled path {
  fill: currentColor;
}

/* Responsive */
@media (max-width: 768px) {
  .modal-content {
    grid-template-columns: 1fr;
    max-height: 80vh;
    overflow-y: auto;
  }
  
  .product-images {
    padding: 1rem;
    max-height: 300px;
  }
  
  .product-details {
    padding: 1.5rem;
  }
}
</style>