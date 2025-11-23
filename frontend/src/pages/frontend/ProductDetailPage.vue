<template>
  <FrontendLayout>
    <div class="product-detail-page" v-if="product">
      <div class="container">
        <!-- ... keep existing breadcrumb ... -->

        <div class="product-detail">
          <!-- Product Images - UPDATED SECTION -->
          <div class="product-gallery">
            <div class="main-image">
              <img 
                :src="selectedImage || product.main_image_url" 
                :alt="product.name" 
                @error="handleImageError"
              />
            </div>
            <div class="image-thumbnails" v-if="allImages.length > 1">
              <div 
                v-for="(image, index) in allImages" 
                :key="index"
                class="thumbnail"
                :class="{ active: selectedImage === image.url }"
                @click="selectedImage = image.url"
              >
                <img 
                  :src="image.url" 
                  :alt="`${product.name} ${index + 1}`"
                  @error="handleThumbnailError(index)"
                />
              </div>
            </div>
          </div>

          <!-- ... rest of your existing HTML ... -->
        </div>
      </div>
    </div>
  </FrontendLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useProductsStore } from '@/store/products'
import { useCartStore } from '@/store/cart'

const route = useRoute()
const router = useRouter()
const productsStore = useProductsStore()
const cartStore = useCartStore()

const product = ref(null)
const loading = ref(true)
const selectedImage = ref(null)
const selectedVariant = ref(null)
const selectedSize = ref(null)
const quantity = ref(1)
const activeTab = ref('description')
const imageErrors = ref(new Set())

// Computed properties
const allImages = computed(() => {
  const images = []
  
  // Add main image
  if (product.value?.main_image_url && !imageErrors.value.has('main')) {
    images.push({ 
      url: product.value.main_image_url, 
      type: 'main'
    })
  }
  
  // Add variant images
  product.value?.variants?.forEach(variant => {
    variant.images?.forEach((image, index) => {
      const imageKey = `variant-${variant.id}-${index}`
      if (!imageErrors.value.has(imageKey)) {
        images.push({ 
          url: image.url, 
          variant: variant.color_name
        })
      }
    })
  })
  
  // If no images, add a placeholder
  if (images.length === 0 && product.value?.name) {
    images.push({
      url: generatePlaceholder(product.value.name),
      type: 'main',
      isPlaceholder: true
    })
  }
  
  return images
})

// ... keep your existing computed properties and methods ...

// ADD THESE NEW METHODS:
const handleImageError = () => {
  imageErrors.value.add('main')
  if (product.value?.name) {
    selectedImage.value = generatePlaceholder(product.value.name)
  }
}

const handleThumbnailError = (index) => {
  imageErrors.value.add(`thumbnail-${index}`)
}

const generatePlaceholder = (name) => {
  const colors = ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#00f2fe']
  const color = colors[name.length % colors.length]
  return `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="300" height="300" viewBox="0 0 300 300"><rect width="300" height="300" fill="${color}" opacity="0.2"/><text x="150" y="150" font-family="Arial" font-size="14" fill="%23666" text-anchor="middle" dominant-baseline="middle">${name}</text></svg>`
}

// ... rest of your existing script ...
</script>


<style scoped>
.product-detail-page {
  padding: 2rem 0;
}

.breadcrumb {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 2rem;
  color: #666;
}

.breadcrumb a {
  color: #667eea;
  text-decoration: none;
}

.breadcrumb a:hover {
  text-decoration: underline;
}

.product-detail {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
  margin-bottom: 3rem;
}

.product-gallery {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.main-image {
  width: 100%;
  height: 500px;
  border-radius: 12px;
  overflow: hidden;
}

.main-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.image-thumbnails {
  display: flex;
  gap: 0.5rem;
  overflow-x: auto;
}

.thumbnail {
  width: 80px;
  height: 80px;
  border-radius: 8px;
  overflow: hidden;
  cursor: pointer;
  border: 2px solid transparent;
  flex-shrink: 0;
}

.thumbnail.active {
  border-color: #667eea;
}

.thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-info {
  padding: 1rem;
}

.product-title {
  font-size: 2rem;
  margin-bottom: 1rem;
  color: #333;
}

.product-meta {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
}

.brand, .sku {
  color: #666;
  font-size: 0.9rem;
}

.rating {
  color: #ffc107;
}

.rating-count {
  color: #666;
  font-size: 0.9rem;
}

.product-price {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.current-price {
  font-size: 2rem;
  font-weight: bold;
  color: #2c5530;
}

.original-price {
  font-size: 1.2rem;
  color: #666;
  text-decoration: line-through;
}

.discount {
  background: #dc3545;
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 0.8rem;
  font-weight: bold;
}

.product-description {
  margin-bottom: 2rem;
  line-height: 1.6;
}

.product-variants {
  margin-bottom: 2rem;
}

.variant-option, .size-option {
  margin-bottom: 1.5rem;
}

.variant-option h4, .size-option h4 {
  margin-bottom: 0.5rem;
  color: #333;
}

.color-options, .size-options {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.color-option {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.color-option.active {
  border-color: #667eea;
  background: #f0f4ff;
}

.color-option.unavailable {
  opacity: 0.5;
  cursor: not-allowed;
}

.color-swatch {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  border: 1px solid #ddd;
}

.size-option-item {
  padding: 8px 16px;
  border: 1px solid #ddd;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s ease;
  text-align: center;
  min-width: 50px;
}

.size-option-item.active {
  border-color: #667eea;
  background: #667eea;
  color: white;
}

.size-option-item.unavailable {
  opacity: 0.5;
  cursor: not-allowed;
  text-decoration: line-through;
}

.add-to-cart-section {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
  align-items: center;
}

.quantity-selector {
  display: flex;
  align-items: center;
  border: 1px solid #ddd;
  border-radius: 6px;
  overflow: hidden;
}

.quantity-selector button {
  padding: 10px 15px;
  border: none;
  background: #f8f9fa;
  cursor: pointer;
  font-size: 1.1rem;
}

.quantity-selector span {
  padding: 10px 20px;
  background: white;
  min-width: 50px;
  text-align: center;
}

.add-to-cart-btn {
  flex: 1;
  padding: 12px 24px;
  background: #4CAF50;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s ease;
}

.add-to-cart-btn:hover:not(:disabled) {
  background: #45a049;
}

.add-to-cart-btn:disabled {
  background: #6c757d;
  cursor: not-allowed;
}

.wishlist-btn {
  padding: 12px 16px;
  background: #f8f9fa;
  border: 1px solid #ddd;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.wishlist-btn:hover {
  background: #e9ecef;
}

.product-details {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.detail-item {
  display: flex;
  gap: 0.5rem;
}

.in-stock {
  color: #28a745;
}

.low-stock {
  color: #ffc107;
}

.out-of-stock {
  color: #dc3545;
}

.product-tabs {
  margin-bottom: 3rem;
}

.tabs-header {
  display: flex;
  border-bottom: 1px solid #ddd;
  margin-bottom: 2rem;
}

.tabs-header button {
  padding: 1rem 2rem;
  background: none;
  border: none;
  cursor: pointer;
  border-bottom: 2px solid transparent;
  transition: all 0.3s ease;
}

.tabs-header button.active {
  border-bottom-color: #667eea;
  color: #667eea;
  font-weight: 600;
}

.tab-panel {
  padding: 1rem 0;
}

.specs-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.spec-item {
  padding: 0.5rem 0;
  border-bottom: 1px solid #eee;
}

.loading-state, .error-state {
  text-align: center;
  padding: 4rem 2rem;
}

.error-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.back-btn {
  display: inline-block;
  padding: 10px 20px;
  background: #667eea;
  color: white;
  text-decoration: none;
  border-radius: 6px;
  margin-top: 1rem;
}

/* Responsive */
@media (max-width: 768px) {
  .product-detail {
    grid-template-columns: 1fr;
    gap: 2rem;
  }

  .main-image {
    height: 300px;
  }

  .add-to-cart-section {
    flex-direction: column;
    align-items: stretch;
  }

  .tabs-header {
    flex-direction: column;
  }

  .tabs-header button {
    text-align: left;
  }
}
</style>