<!-- components/frontend/ProductCard.vue -->
<template>
  <div class="product-card" @click="goToProductDetails">
    <div class="product-image">
      <img :src="product.main_image_url" :alt="product.name" />
      <div class="product-badges">
        <span v-if="product.isNew" class="badge new">New</span>
        <span v-if="product.onSale" class="badge sale">Sale</span>
        <!-- FIX: Use computed property to check stock -->
        <span v-if="!hasStock" class="badge out-of-stock">Out of Stock</span>
      </div>
      
      <!-- Quick View Button -->
      <button class="quick-view-btn" @click.stop="openQuickView" title="Quick View">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
          <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" 
                stroke="currentColor" stroke-width="2"/>
          <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
        </svg>
      </button>
      
      <!-- Wishlist Button -->
      <button class="wishlist-btn" @click.stop="toggleWishlist" :title="isInWishlist ? 'Remove from Wishlist' : 'Add to Wishlist'">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" :class="{ 'filled': isInWishlist }">
          <path d="M19.5 12.572L12 20L4.5 12.572C3.605 11.677 3 10.391 3 9C3 6.791 4.791 5 7 5C8.309 5 9.5 5.671 10.214 6.714L12 9L13.786 6.714C14.5 5.671 15.691 5 17 5C19.209 5 21 6.791 21 9C21 10.391 20.395 11.677 19.5 12.572Z" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
    </div>
    
    <div class="product-info">
      <h3 class="product-name">{{ product.name }}</h3>
      <p class="product-category">{{ product.category_title }}</p>
      <p class="product-brand" v-if="product.brand_title">{{ product.brand_title }}</p>
      
      <div class="product-price">
        <!-- Display price range if min and max prices differ -->
        <template v-if="product.min_price && product.max_price && product.min_price !== product.max_price">
          <span class="price-range">From {{ formatPrice(product.min_price) }}</span>
          <span v-if="product.min_price !== product.price" class="original-price">
            {{ formatPrice(product.max_price) }}
          </span>
        </template>
        <template v-else>
          <span class="current-price">{{ formatPrice(product.price) }}</span>
          <span v-if="product.original_price" class="original-price">
            {{ formatPrice(product.original_price) }}
          </span>
        </template>
      </div>
      
      <div class="product-stock">
        <!-- FIX: Use computed property to check stock -->
        <span v-if="hasStock" class="in-stock">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          In Stock
        </span>
        <span v-else class="out-of-stock">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Out of Stock
        </span>
        <span v-if="hasStock && product.stock < 10 && product.stock > 0" class="low-stock">
          Only {{ product.stock }} left
        </span>
      </div>
      
      <!-- Add to Cart Button -->
      <button 
        class="add-to-cart-btn" 
        @click.stop="addToCartHandler"
        :disabled="!hasStock"
        :class="{ 'disabled': !hasStock }"
        :title="hasStock ? 'Add to Cart' : 'Out of Stock'"
      >
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
          <path d="M3 3H5L5.4 5M7 13H17L21 5H5.4M7 13L5.4 5M7 13L4.7 15.3C4.3 15.7 4.6 16.4 5.2 16.4H17M17 13V16.4M9 19C9 19.6 8.6 20 8 20C7.4 20 7 19.6 7 19C7 18.4 7.4 18 8 18C8.6 18 9 18.4 9 19ZM17 19C17 19.6 16.6 20 16 20C15.4 20 15 19.6 15 19C15 18.4 15.4 18 16 18C16.6 18 17 18.4 17 19Z" 
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        {{ hasStock ? 'Add to Cart' : 'Out of Stock' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { toast } from 'vue3-toastify'
import { useCartStore } from '@/store/cart'
import { useWishlistStore } from '@/store/wishlist'
import { useSettingsStore } from '@/store/settings'

const router = useRouter()
const cartStore = useCartStore()
const wishlistStore = useWishlistStore()
const settingsStore = useSettingsStore()

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

console.log('ProductCard product prop:', props.product);
const emit = defineEmits(['add-to-cart', 'quick-view'])

// Format price using settings store
const formatPrice = (amount) => {
  return settingsStore.formatPrice(amount)
}

// DEBUG: Log product details to see what's happening
console.log('Product details:', {
  name: props.product.name,
  has_stock: props.product.has_stock,
  stock: props.product.stock,
  variants: props.product.variants,
  variantsCount: props.product.variants?.length || 0,
})

// FIXED: Comprehensive stock check
const hasStock = computed(() => {
  const product = props.product
  
  // If product has no variants, use the simple check
  if (!product.variants || !Array.isArray(product.variants) || product.variants.length === 0) {
    return product.has_stock === true || product.stock > 0
  }
  
  // For products with variants, check if ANY variant has stock
  const anyVariantHasStock = product.variants.some(variant => {
    // Check variant.has_stock property
    if (variant.has_stock !== undefined) {
      return variant.has_stock === true
    }
    
    // Check if variant has sizes with stock
    if (variant.sizes && Array.isArray(variant.sizes)) {
      return variant.sizes.some(size => {
        return (size.has_stock === true) || 
               (size.stock !== undefined && size.stock > 0)
      })
    }
    
    // If variant has no sizes array, check if it has any stock property
    return (variant.stock !== undefined && variant.stock > 0)
  })
  
  console.log(`Stock check for ${product.name}:`, {
    anyVariantHasStock,
    variants: product.variants.map(v => ({
      color_name: v.color_name,
      has_stock: v.has_stock,
      sizes: v.sizes?.map(s => ({ size_title: s.size_title, has_stock: s.has_stock, stock: s.stock }))
    }))
  })
  
  return anyVariantHasStock
})

// Check if product is in wishlist
const isInWishlist = computed(() => {
  return wishlistStore.isInWishlist(props.product.id)
})

// Navigate to product details page
const goToProductDetails = () => {
  // Try slug first
  if (props.product.slug) {
    router.push(`/products/${props.product.slug}`)
  } else {
    // Fallback to ID route
    router.push(`/products/id/${props.product.id}`)
  }
}

// Open quick view modal
const openQuickView = () => {
  emit('quick-view', props.product)
}

// Add to cart handler
const addToCartHandler = () => {
  if (!hasStock.value) {
    toast.warning(`${props.product.name} is out of stock!`)
    return
  }
  
  cartStore.addToCart(props.product)
  toast.success(`${props.product.name} added to cart!`)
  
  // Emit event to parent if needed
  emit('add-to-cart', props.product)
}

// Toggle wishlist
const toggleWishlist = async () => {
  try {
    // Prepare product data for wishlist
    const productData = {
      id: props.product.id,
      name: props.product.name,
      price: props.product.price,
      image: props.product.main_image_url || props.product.image,
      variant_id: props.product.variant_id || null,
      size_id: props.product.size_id || null,
      variant_name: props.product.variant_name || null,
      size_title: props.product.size_title || null,
      stock: props.product.stock || 0
    }
    
    // Use toggleItem method
    const added = await wishlistStore.toggleItem(productData)
    
    if (added) {
      toast.success(`${props.product.name} added to wishlist!`)
    } else {
      toast.info(`${props.product.name} removed from wishlist!`)
    }
  } catch (error) {
    console.error('Error toggling wishlist:', error)
    toast.error('Failed to update wishlist')
  }
}
</script>

<style scoped>
/* Keep your existing styles, just add low-stock style */
.product-card {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid var(--border-color);
  transition: all 0.3s ease;
  position: relative;
  cursor: pointer;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.product-image {
  position: relative;
  aspect-ratio: 1;
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
  top: 8px;
  left: 8px;
  display: flex;
  flex-direction: column;
  gap: 4px;
  z-index: 2;
}

.badge {
  padding: 3px 8px;
  border-radius: 3px;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  color: white;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
  display: inline-block;
  line-height: 1;
  transition: all 0.2s ease;
}

/* New badge - Your brand red */
.badge.new {
  background: #ef4444;
  border: 1px solid #dc2626;
}

/* Sale badge - Green for positive */
.badge.sale {
  background: #22c55e;
  border: 1px solid #16a34a;
}

/* Out of Stock badge - Darker red for warning */
.badge.out-of-stock {
  background: #dc2626;
  border: 1px solid #b91c1c;
  color: white;
}

/* Minimal alternative - Even smaller */
.badge.minimal {
  padding: 2px 6px;
  font-size: 9px;
  font-weight: 600;
  border-radius: 2px;
}

/* Hover effects - subtle */
.product-card:hover .badge {
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.25);
}

/* Ensure they don't overlap with action buttons */
@media (max-width: 768px) {
  .product-badges {
    top: 6px;
    left: 6px;
    gap: 3px;
  }
  
  .badge {
    padding: 2px 6px;
    font-size: 9px;
  }
}

/* Optional: Compact single-line layout */
.product-badges.compact {
  flex-direction: row;
  flex-wrap: wrap;
  gap: 4px;
  max-width: 70%; /* Prevent covering too much of the image */
}

/* For really tight spaces, use abbreviated text */
.badge.abbreviated {
  padding: 2px 5px;
  font-size: 9px;
}

/* If you want icons-only badges for super compact look */
.badge.icon-only {
  width: 20px;
  height: 20px;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  font-size: 9px;
}

/* Alternative: Corner badges (like a ribbon) */
.badge.corner {
  position: absolute;
  top: -5px;
  left: -5px;
  padding: 12px 5px 5px 12px;
  clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%, 0 30%, 30% 0);
}

.badge.corner.new {
  background: #ef4444;
}

.badge.corner.out-of-stock {
  background: #dc2626;
}

/* Hover effects */
.product-card:hover .badge {
  transform: translateY(-1px);
  box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
}

/* Animation for badges */
.badge {
  transition: all 0.3s ease;
}

/* Action buttons overlay */
.quick-view-btn,
.wishlist-btn {
  position: absolute;
  background: white;
  border: none;
  border-radius: 50%;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  opacity: 0;
  transform: translateY(10px);
  transition: all 0.3s ease;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  z-index: 2;
}

.quick-view-btn {
  top: 10px;
  right: 10px;
}

.wishlist-btn {
  top: 56px; /* 10px + 36px + 10px gap */
  right: 10px;
}

.product-card:hover .quick-view-btn,
.product-card:hover .wishlist-btn {
  opacity: 1;
  transform: translateY(0);
}

.quick-view-btn:hover {
  background: var(--primary-red);
  color: white;
}

.wishlist-btn:hover {
  background: var(--primary-red);
  color: white;
}

.wishlist-btn .filled {
  fill: var(--primary-red);
}

.product-info {
  padding: 1rem;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}

.product-name {
  font-size: 0.95rem;
  font-weight: 600;
  color: var(--primary-black);
  margin-bottom: 0.25rem;
  line-height: 1.3;
  min-height: 2.4em;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}

.product-category {
  color: var(--gray-medium);
  font-size: 0.8rem;
  margin-bottom: 0.25rem;
}

.product-brand {
  color: var(--primary-red);
  font-size: 0.8rem;
  font-weight: 500;
  margin-bottom: 0.5rem;
}

.product-price {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
  flex-wrap: wrap;
}

.current-price {
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--primary-red);
}

.original-price {
  font-size: 0.9rem;
  color: var(--gray-medium);
  text-decoration: line-through;
}

.price-range {
  font-size: 1rem;
  font-weight: 700;
  color: var(--primary-red);
}

.product-stock {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 0.75rem;
  font-size: 0.8rem;
}

.in-stock {
  color: #22c55e;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.out-of-stock {
  color: #ef4444;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.low-stock {
  color: #f59e0b;
  font-weight: 500;
  font-size: 0.75rem;
}

.add-to-cart-btn {
  margin-top: auto;
  width: 100%;
  padding: 0.75rem;
  background: var(--primary-red);
  color: white;
  border: none;
  border-radius: 4px;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.add-to-cart-btn:hover:not(.disabled) {
  background: #c00;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(227, 27, 35, 0.3);
}

.add-to-cart-btn.disabled {
  background: var(--gray-medium);
  cursor: not-allowed;
  opacity: 0.7;
}

/* Responsive */
@media (max-width: 768px) {
  .quick-view-btn,
  .wishlist-btn {
    opacity: 1;
    transform: translateY(0);
  }
  
  .product-name {
    font-size: 0.9rem;
  }
  
  .add-to-cart-btn {
    padding: 0.6rem;
    font-size: 0.85rem;
  }
}

@media (max-width: 480px) {
  .product-card {
    border-radius: 6px;
  }
  
  .product-info {
    padding: 0.75rem;
  }
  
  .product-name {
    font-size: 0.85rem;
  }
  
  .current-price {
    font-size: 1rem;
  }
}
</style>