<!-- pages/frontend/ProductDetail.vue -->
<template>
  <FrontendLayout>
    <Loader :loading="loading" />
    
    <div class="product-detail">
      <div class="container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb" v-if="productData.product">
          <router-link to="/" class="breadcrumb-link">Home</router-link>
          <span class="breadcrumb-separator">/</span>
          <router-link to="/products" class="breadcrumb-link">Products</router-link>
          <span class="breadcrumb-separator">/</span>
          <router-link :to="`/products?category=${productData.product.category_id}`" class="breadcrumb-link">
            {{ productData.product.category_title }}
          </router-link>
          <span class="breadcrumb-separator">/</span>
          <span class="breadcrumb-current">{{ productData.product.name }}</span>
        </nav>

        <div class="product-layout" v-if="productData.product">
          <!-- Product Images -->
          <div class="product-gallery">
            <div class="main-image">
              <img :src="selectedImage || productData.product.main_image_url" :alt="productData.product.name" />
              <div class="image-badge" v-if="productData.product.isNew">New</div>
              <div class="image-badge sale" v-if="productData.product.onSale">Sale</div>
              <div class="image-badge out-of-stock" v-if="!productData.product.has_stock">Out of Stock</div>
            </div>
            <div class="image-thumbnails">
              <!-- Main image thumbnail -->
              <div class="thumbnail"
                   :class="{ active: selectedImage === productData.product.main_image_url }"
                   @click="selectMainImage">
                <img :src="productData.product.main_image_url" :alt="productData.product.name" />
              </div>
              <!-- Variant images -->
              <div v-for="(variant, variantIndex) in productData.variants" :key="variant.id">
                <div v-for="(image, imageIndex) in variant.images" 
                     :key="image.id"
                     class="thumbnail"
                     :class="{ active: selectedImage === image.url }"
                     @click="selectVariantImage(variant, image)">
                  <img :src="image.url" :alt="`${productData.product.name} - ${variant.color_name} view ${imageIndex + 1}`" />
                </div>
              </div>
            </div>
          </div>

          <!-- Product Info -->
          <div class="product-info">
            <div class="product-header">
              <h1 class="product-title">{{ productData.product.name }}</h1>
              <div class="product-rating">
                <div class="stars">
                  <span v-for="n in 5" :key="n" class="star" :class="{ filled: n <= productRating }">★</span>
                </div>
                <span class="rating-text">({{ productReviews.length }} reviews)</span>
              </div>
            </div>

            <div class="product-pricing">
              <div class="price-container">
                <!-- Updated: Use settingsStore.formatPrice() -->
                <span class="current-price">{{ formatPrice(selectedPrice || productData.product.price) }}</span>
                <!-- Updated: Use settingsStore.formatPrice() -->
                <span class="original-price" v-if="productData.product.originalPrice">
                  {{ formatPrice(productData.product.originalPrice) }}
                </span>
                <span class="discount-badge" v-if="productData.product.onSale">
                  Save {{ calculateDiscount() }}%
                </span>
              </div>
              <div class="vat-text">Inc. VAT</div>
            </div>

            <div class="product-description">
              <p>{{ productDescription }}</p>
            </div>

            <!-- Product Variants -->
            <div class="product-variants" v-if="productData.variants && productData.variants.length > 0">
              <!-- Color Variant -->
              <div class="variant-group">
                <label class="variant-label">Color:</label>
                <div class="variant-options">
                  <div
                    v-for="variant in productData.variants"
                    :key="variant.id"
                    class="variant-option color-option"
                    :class="{ 
                      active: selectedVariant.variantId === variant.id,
                      unavailable: !variant.has_stock 
                    }"
                    @click="selectVariant(variant)"
                    :title="variant.has_stock ? variant.color_name : 'Out of stock'"
                  >
                    <span class="color-swatch" :style="{ backgroundColor: variant.color }"></span>
                    <span class="color-name">{{ variant.color_name }}</span>
                  </div>
                </div>
              </div>

              <!-- Size Variant -->
              <div class="variant-group" v-if="selectedVariant.sizes && selectedVariant.sizes.length > 0">
                <label class="variant-label">Size:</label>
                <div class="variant-options">
                  <div
                    v-for="size in selectedVariant.sizes"
                    :key="size.id"
                    class="variant-option size-option"
                    :class="{ 
                      active: selectedVariant.sizeId === size.id,
                      unavailable: !size.has_stock 
                    }"
                    @click="selectSize(size)"
                    :title="size.has_stock ? `Size: ${size.size_title} (Stock: ${size.stock})` : 'Out of stock'"
                  >
                    {{ size.size_title }}
                  </div>
                </div>
                <a :href="productData.product.size_guide_url" target="_blank" class="size-guide-link" v-if="productData.product.size_guide_url">
                  Size Guide
                </a>
              </div>
            </div>

            <!-- Quantity & Add to Cart -->
            <div class="purchase-section">
              <div class="quantity-selector">
                <label class="quantity-label">Quantity:</label>
                <div class="quantity-control">
                  <button 
                    class="qty-btn" 
                    @click="decreaseQuantity"
                    :disabled="quantity <= 1"
                  >-</button>
                  <span class="quantity">{{ quantity }}</span>
                  <button 
                    class="qty-btn" 
                    @click="increaseQuantity"
                    :disabled="quantity >= maxQuantity"
                  >+</button>
                </div>
              </div>

              <div class="stock-status">
                <span v-if="isInStock" class="in-stock">✓ In Stock</span>
                <span v-else class="out-of-stock">✗ Out of Stock</span>
                <span class="stock-count" v-if="isInStock && selectedVariantStock < 10">
                  Only {{ selectedVariantStock }} left!
                </span>
              </div>

              <div class="action-buttons">
                <button 
                  class="btn btn-primary add-to-cart-btn"
                  @click="addToCart"
                  :disabled="!isInStock"
                >
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M3 3H5L5.4 5M7 13H17L21 5H5.4M7 13L5.4 5M7 13L4.7 15.3C4.3 15.7 4.6 16.4 5.2 16.4H17M17 13V16.4M9 19C9 19.6 8.6 20 8 20C7.4 20 7 19.6 7 19C7 18.4 7.4 18 8 18C8.6 18 9 18.4 9 19ZM17 19C17 19.6 16.6 20 16 20C15.4 20 15 19.6 15 19C15 18.4 15.4 18 16 18C16.6 18 17 18.4 17 19Z" 
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  Add to Cart
                </button>
                
                <!-- Direct Purchase Button -->
                <button 
                  class="btn btn-outline buy-now-btn"
                  @click="directPurchase"
                  :disabled="!isInStock"
                >
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M9 10L9 4L15 4L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M4 10H20V20C20 21.1046 19.1046 22 18 22H6C4.89543 22 4 21.1046 4 20V10Z" 
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  Buy Now
                </button>
              </div>
            </div>

            <!-- Trust Badges - Updated with settings -->
            <div class="trust-badges">
              <div class="trust-item">
                <div class="trust-icon">🚚</div>
                <div class="trust-text">
                  <strong>Free Delivery</strong>
                  <!-- Updated: Use settingsStore.formatPrice() -->
                  <span v-if="settingsStore.freeShippingThreshold > 0">
                    On orders over {{ settingsStore.formatPrice(settingsStore.freeShippingThreshold) }}
                  </span>
                  <span v-else>Free shipping available</span>
                </div>
              </div>
              <div class="trust-item">
                <div class="trust-icon">↩️</div>
                <div class="trust-text">
                  <strong>{{ settingsStore.settings.policies?.return_days || 30 }}-Day Returns</strong>
                  <span>Money back guarantee</span>
                </div>
              </div>
              <div class="trust-item">
                <div class="trust-icon">🔒</div>
                <div class="trust-text">
                  <strong>Secure Payment</strong>
                  <span>Your data is protected</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Product Tabs -->
        <div class="product-tabs" v-if="productData.product">
          <div class="tabs-header">
            <button 
              class="tab-btn"
              :class="{ active: activeTab === 'description' }"
              @click="activeTab = 'description'"
            >
              Description
            </button>
            <button 
              class="tab-btn"
              :class="{ active: activeTab === 'specifications' }"
              @click="activeTab = 'specifications'"
            >
              Specifications
            </button>
            <button 
              class="tab-btn"
              :class="{ active: activeTab === 'reviews' }"
              @click="activeTab = 'reviews'"
            >
              Reviews ({{ productReviews.length }})
            </button>
          </div>
          
          <div class="tabs-content">
            <!-- Description Tab -->
            <div v-if="activeTab === 'description'" class="tab-panel">
              <div class="description-content">
                <h3>Product Description</h3>
                <p>{{ productFullDescription }}</p>
                <div class="features-list">
                  <div v-for="feature in productFeatures" :key="feature" class="feature">
                    <span class="check-icon">✓</span>
                    {{ feature }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Specifications Tab -->
            <div v-if="activeTab === 'specifications'" class="tab-panel">
              <div class="specs-grid">
                <div class="spec-item">
                  <span class="spec-name">SKU:</span>
                  <span class="spec-value">{{ productData.product.sku }}</span>
                </div>
                <div class="spec-item">
                  <span class="spec-name">Gender:</span>
                  <span class="spec-value">{{ productData.product.gender }}</span>
                </div>
                <div class="spec-item">
                  <span class="spec-name">Brand:</span>
                  <span class="spec-value">{{ productData.product.brand_title }}</span>
                </div>
                <div class="spec-item">
                  <span class="spec-name">Category:</span>
                  <span class="spec-value">{{ productData.product.category_title }}</span>
                </div>
                <div class="spec-item" v-if="productData.product.purchase_price">
                  <span class="spec-name">Purchase Price:</span>
                  <!-- Updated: Use settingsStore.formatPrice() -->
                  <span class="spec-value">{{ formatPrice(productData.product.purchase_price) }}</span>
                </div>
                <div class="spec-item">
                  <span class="spec-name">Stock:</span>
                  <span class="spec-value">{{ productData.product.stock }} units</span>
                </div>
              </div>
            </div>

            <!-- Reviews Tab -->
            <div v-if="activeTab === 'reviews'" class="tab-panel">
              <div class="reviews-content">
                <div class="reviews-summary">
                  <div class="average-rating">
                    <div class="rating-score">{{ productRating.toFixed(1) }}</div>
                    <div class="stars-large">
                      <span v-for="n in 5" :key="n" class="star" :class="{ filled: n <= productRating }">★</span>
                    </div>
                    <div class="rating-count">{{ productReviews.length }} reviews</div>
                  </div>
                </div>
                
                <div class="reviews-list">
                  <div v-for="review in productReviews" :key="review.id" class="review">
                    <div class="review-header">
                      <div class="reviewer">{{ review.reviewer }}</div>
                      <div class="review-date">{{ review.date }}</div>
                    </div>
                    <div class="review-rating">
                      <span v-for="n in 5" :key="n" class="star" :class="{ filled: n <= review.rating }">★</span>
                    </div>
                    <p class="review-text">{{ review.comment }}</p>
                  </div>
                </div>
                
                <div v-if="productReviews.length === 0" class="no-reviews">
                  <p>No reviews yet. Be the first to review this product!</p>
                  <button class="btn btn-primary">Write a Review</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Related Products -->
        <div class="related-products" v-if="relatedProducts.length > 0">
          <h2 class="section-title">You May Also Like</h2>
          <div class="products-grid">
            <ProductCard 
              v-for="relatedProduct in relatedProducts" 
              :key="relatedProduct.id" 
              :product="relatedProduct"
              @add-to-cart="handleAddToCart"
              @buy-now="handleBuyNow"
            />
          </div>
        </div>
      </div>
    </div>
  </FrontendLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router' // Add router import
import { toast } from 'vue3-toastify'
import FrontendLayout from '@/layouts/FrontendLayout.vue'
import ProductCard from '@/components/frontend/ProductCard.vue'
import Loader from '@/components/frontend/Loader.vue'
import { useProductsStore } from '@/store/products'
import { useCartStore } from '@/store/cart'
import { useSettingsStore } from '@/store/settings' // Removed wishlist import

const route = useRoute()
const router = useRouter() // Add router
const productsStore = useProductsStore()
const cartStore = useCartStore()
const settingsStore = useSettingsStore()

const loading = ref(true)
const productData = ref({})
const selectedImage = ref('')
const selectedVariant = ref({
  variantId: null,
  variant: null,
  sizeId: null,
  size: null,
  sizes: []
})
const quantity = ref(1)
const activeTab = ref('description')
const relatedProducts = ref([])

// Sample reviews data
const sampleReviews = [
  {
    id: 1,
    reviewer: "John Construction",
    rating: 5,
    date: "2 weeks ago",
    comment: "Excellent quality! Very durable and keeps me dry in all weather conditions."
  },
  {
    id: 2,
    reviewer: "Sarah Builder",
    rating: 4,
    date: "1 month ago",
    comment: "Great quality and fits perfectly. The pockets are very useful for carrying tools."
  }
]

// Helper function to format price using settings store
const formatPrice = (amount) => {
  return settingsStore.formatPrice(amount)
}

// Computed properties
const selectedVariantStock = computed(() => {
  if (!selectedVariant.value.size) return 0
  return selectedVariant.value.size.stock || 0
})

const isInStock = computed(() => {
  return selectedVariantStock.value > 0
})

const maxQuantity = computed(() => {
  return selectedVariantStock.value
})

const selectedPrice = computed(() => {
  if (selectedVariant.value.size && selectedVariant.value.size.price) {
    return selectedVariant.value.size.price
  }
  return productData.value.product?.price || 0
})

const productRating = computed(() => {
  if (productReviews.value.length === 0) return 4.5
  const total = productReviews.value.reduce((sum, review) => sum + review.rating, 0)
  return total / productReviews.value.length
})

const productReviews = computed(() => {
  return sampleReviews
})

const productDescription = computed(() => {
  if (!productData.value.product?.description) return ''
  return productData.value.product.description.replace(/<[^>]*>/g, '').substring(0, 150) + '...'
})

const productFullDescription = computed(() => {
  if (!productData.value.product?.description) return ''
  let desc = productData.value.product.description.replace(/<[^>]*>/g, ' ')
  desc = desc.replace(/\s+/g, ' ').trim()
  return desc
})

const productFeatures = computed(() => {
  const features = []
  const description = productData.value.product?.description || ''
  
  const featureKeywords = [
    { keyword: 'fabric', text: 'Quality Fabric' },
    { keyword: 'cotton', text: 'Mixed Cotton' },
    { keyword: 'durable', text: 'Durable Construction' },
    { keyword: 'comfort', text: 'Comfortable Fit' }
  ]
  
  featureKeywords.forEach(feature => {
    if (description.toLowerCase().includes(feature.keyword)) {
      features.push(feature.text)
    }
  })
  
  return features
})

// Methods
const selectVariant = (variant) => {
  if (!variant.has_stock) {
    toast.warning('This color is out of stock')
    return
  }
  
  selectedVariant.value.variantId = variant.id
  selectedVariant.value.variant = variant
  selectedVariant.value.sizes = variant.sizes || []
  
  // Reset selected size
  selectedVariant.value.sizeId = null
  selectedVariant.value.size = null
  
  // Auto-select first available size
  if (variant.sizes && variant.sizes.length > 0) {
    const firstAvailableSize = variant.sizes.find(size => size.has_stock)
    if (firstAvailableSize) {
      selectSize(firstAvailableSize)
    }
  }
  
  // Set first variant image as selected if available
  if (variant.images && variant.images.length > 0) {
    selectedImage.value = variant.images[0].url
  } else {
    // Fallback to main product image
    selectedImage.value = productData.value.product.main_image_url
  }
}

const selectSize = (size) => {
  if (!size.has_stock) {
    toast.warning('This size is out of stock')
    return
  }
  
  selectedVariant.value.sizeId = size.id
  selectedVariant.value.size = size
  
  // Reset quantity if exceeds available stock
  if (quantity.value > size.stock) {
    quantity.value = size.stock
  }
}

const selectMainImage = () => {
  selectedImage.value = productData.value.product.main_image_url
}

const selectVariantImage = (variant, image) => {
  selectedImage.value = image.url
  if (selectedVariant.value.variantId !== variant.id) {
    selectVariant(variant)
  }
}

const increaseQuantity = () => {
  if (quantity.value < maxQuantity.value) {
    quantity.value++
  }
}

const decreaseQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--
  }
}

const calculateDiscount = () => {
  const product = productData.value.product
  if (!product.originalPrice || !product.price) return 0
  const discount = ((parseFloat(product.originalPrice) - parseFloat(product.price)) / parseFloat(product.originalPrice)) * 100
  return Math.round(discount)
}

const addToCart = () => {
  if (!isInStock.value) {
    toast.error('This product is out of stock!')
    return
  }

  // Prepare cart item object
  const cartItem = {
    id: productData.value.product.id,
    name: productData.value.product.name,
    price: selectedPrice.value,
    image: selectedImage.value || productData.value.product.main_image_url,
    quantity: quantity.value,
    variant_id: selectedVariant.value.variantId,
    variant_name: selectedVariant.value.variant?.color_name || null,
    size_id: selectedVariant.value.size?.id || null,
    size_title: selectedVariant.value.size?.size_title || null,
    stock: selectedVariantStock.value
  }

  console.log('🛒 Adding to cart:', cartItem)
  
  // Add to cart using the store's method with proper parameters
  cartStore.addToCart(
    productData.value.product,  // product object
    quantity.value,             // quantity
    selectedVariant.value.variant, // variant object
    selectedVariant.value.size     // size object
  )
  
  toast.success(`${productData.value.product.name} added to cart!`)
  
  // Reset quantity
  quantity.value = 1
}

// Direct purchase function
const directPurchase = async () => {
  if (!isInStock.value) {
    toast.error('This product is out of stock!')
    return
  }

  try {
    // Prepare product data for direct checkout
    const productDataForCheckout = {
      product_id: productData.value.product.id,
      quantity: quantity.value,
      variant_id: selectedVariant.value.variantId || null,
      size_id: selectedVariant.value.sizeId || null,
      price: selectedPrice.value,
      name: productData.value.product.name,
      image: selectedImage.value || productData.value.product.main_image_url,
      variant_name: selectedVariant.value.variant?.color_name || null,
      size_title: selectedVariant.value.size?.size_title || null
    }

    // Store the product data for direct checkout
    sessionStorage.setItem('direct_checkout_product', JSON.stringify(productDataForCheckout))
    
    // Navigate to checkout page
    router.push('/checkout?type=direct')
    
  } catch (error) {
    console.error('Error initiating direct purchase:', error)
    toast.error('Failed to process direct purchase')
  }
}

const handleAddToCart = (product) => {
  cartStore.addToCart({
    id: product.id,
    name: product.name,
    price: product.price,
    image: product.main_image_url,
    quantity: 1
  })
  toast.success(`${product.name} added to cart!`)
}

// Handle Buy Now from related products
const handleBuyNow = (product) => {
  // For related products, we need to fetch the full product details first
  router.push(`/products/${product.slug || product.id}`)
}

const fetchProduct = async () => {
  loading.value = true
  try {
    const slug = route.params.slug
    const id = route.params.id
    
    let data
    
    if (slug) {
      if (slug.startsWith('product-')) {
        const productId = slug.replace('product-', '')
        data = await productsStore.fetchProductById(productId)
      } else {
        data = await productsStore.fetchProductBySlug(slug)
      }
    } else if (id) {
      data = await productsStore.fetchProductById(id)
    }
    
    if (data && data.product) {
      productData.value = data
      
      // Set default selected image
      selectedImage.value = data.product.main_image_url
      
      // Set default variant if available
      if (data.variants && data.variants.length > 0) {
        const firstVariant = data.variants[0]
        selectVariant(firstVariant)
      }
      
      // Fetch related products
      if (data.related_products) {
        relatedProducts.value = data.related_products
      }
    }
  } catch (error) {
    console.error('Error fetching product:', error)
    toast.error('Failed to load product details')
  } finally {
    loading.value = false
  }
}

// Watch for route changes
watch(() => route.params.slug, () => {
  fetchProduct()
})
watch(() => route.params.id, () => {
  fetchProduct()
})

// Lifecycle - Ensure settings are loaded
onMounted(async () => {
  // Load settings if not already loaded
  if (!settingsStore.isLoaded && !settingsStore.isLoading) {
    await settingsStore.fetchSettings()
  }
  
  fetchProduct()
})
</script>

<style scoped>
/* Add styles for Buy Now button */
.buy-now-btn {
  background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
  color: white;
  border: none;
}

.buy-now-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
  transform: translateY(-1px);
}

.buy-now-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.action-buttons {
  display: flex;
  gap: 12px;
  margin-top: 20px;
}

.add-to-cart-btn,
.buy-now-btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 20px;
  font-weight: 600;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.product-detail {
  padding: 2rem 0 4rem;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  min-height: 100vh;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Breadcrumb */
.breadcrumb {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 2rem;
  font-size: 14px;
}

.breadcrumb-link {
  color: var(--primary-red);
  text-decoration: none;
  transition: color 0.3s ease;
}

.breadcrumb-link:hover {
  color: #c00;
}

.breadcrumb-separator {
  color: var(--gray-medium);
}

.breadcrumb-current {
  color: var(--primary-black);
  font-weight: 500;
}

/* Product Layout */
.product-layout {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  margin-bottom: 4rem;
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  padding: 2rem;
}

/* Product Gallery */
.product-gallery {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.main-image {
  position: relative;
  border-radius: 12px;
  overflow: hidden;
  background: #f8f9fa;
  aspect-ratio: 1;
}

.main-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.image-badge {
  position: absolute;
  top: 12px;
  left: 12px;
  background: var(--primary-red);
  color: white;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
}

.image-badge.sale {
  background: #22c55e;
  top: 50px;
}

.image-badge.out-of-stock {
  background: #6b7280;
  top: 50px;
}

.image-thumbnails {
  display: flex;
  gap: 1rem;
  overflow-x: auto;
  padding: 8px 0;
}

.thumbnail {
  width: 80px;
  height: 80px;
  border-radius: 8px;
  overflow: hidden;
  cursor: pointer;
  border: 2px solid transparent;
  transition: all 0.3s ease;
  flex-shrink: 0;
}

.thumbnail.active {
  border-color: var(--primary-red);
}

.thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Product Info */
.product-info {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.product-header {
  border-bottom: 1px solid var(--border-color);
  padding-bottom: 1rem;
}

.product-title {
  font-size: 2rem;
  font-weight: 800;
  color: var(--primary-black);
  margin: 0 0 1rem 0;
  line-height: 1.2;
}

.product-rating {
  display: flex;
  align-items: center;
  gap: 8px;
}

.stars {
  display: flex;
  gap: 2px;
}

.star {
  color: #ddd;
  font-size: 16px;
}

.star.filled {
  color: #ffc107;
}

.rating-text {
  color: var(--gray-medium);
  font-size: 14px;
}

/* Pricing */
.product-pricing {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.price-container {
  display: flex;
  align-items: center;
  gap: 12px;
}

.current-price {
  font-size: 2rem;
  font-weight: 800;
  color: var(--primary-red);
}

.original-price {
  font-size: 1.2rem;
  color: var(--gray-medium);
  text-decoration: line-through;
}

.discount-badge {
  background: #22c55e;
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 700;
}

.vat-text {
  color: var(--gray-medium);
  font-size: 14px;
}

/* Product Description */
.product-description p {
  color: var(--gray-dark);
  line-height: 1.6;
  margin: 0;
}

/* Product Variants */
.product-variants {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.variant-group {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.variant-label {
  font-weight: 600;
  color: var(--primary-black);
  font-size: 14px;
}

.variant-options {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.variant-option {
  padding: 10px 16px;
  border: 2px solid var(--border-color);
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 14px;
  font-weight: 500;
}

.variant-option.active {
  border-color: var(--primary-red);
  background: #fef2f2;
}

.variant-option.unavailable {
  opacity: 0.5;
  cursor: not-allowed;
  text-decoration: line-through;
}

.color-option {
  display: flex;
  align-items: center;
  gap: 8px;
}

.color-swatch {
  width: 20px;
  height: 20px;
  border-radius: 4px;
  border: 2px solid #f0f0f0;
}

.size-option {
  min-width: 50px;
  text-align: center;
}

.size-guide-link {
  color: var(--primary-red);
  text-decoration: none;
  font-size: 14px;
  align-self: flex-start;
}

.size-guide-link:hover {
  text-decoration: underline;
}

/* Purchase Section */
.purchase-section {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  padding: 1.5rem;
  background: #fafafa;
  border-radius: 12px;
}

.quantity-selector {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.quantity-label {
  font-weight: 600;
  color: var(--primary-black);
  min-width: 80px;
}

.quantity-control {
  display: flex;
  align-items: center;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  overflow: hidden;
}

.qty-btn {
  background: white;
  border: none;
  padding: 12px 16px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 44px;
}

.qty-btn:hover:not(:disabled) {
  background: var(--gray-light);
}

.qty-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.quantity {
  padding: 12px 20px;
  font-weight: 600;
  min-width: 60px;
  text-align: center;
  background: white;
  border-left: 1px solid var(--border-color);
  border-right: 1px solid var(--border-color);
}

.stock-status {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 14px;
}

.in-stock {
  color: #22c55e;
  font-weight: 600;
}

.out-of-stock {
  color: #ef4444;
  font-weight: 600;
}

.stock-count {
  color: var(--primary-red);
  font-weight: 600;
}

.action-buttons {
  display: flex;
  gap: 12px;
}

.btn {
  flex: 1;
  padding: 16px 24px;
  border: none;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-size: 15px;
}

.btn-primary {
  background: var(--primary-red);
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #c00;
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(227, 27, 35, 0.3);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.btn-outline {
  background: white;
  color: var(--primary-black);
  border: 2px solid var(--border-color);
}

.btn-outline:hover {
  border-color: var(--primary-red);
  color: var(--primary-red);
  background: #fef2f2;
}

/* Trust Badges */
.trust-badges {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 1rem;
  padding-top: 1.5rem;
  border-top: 1px solid var(--border-color);
}

.trust-item {
  display: flex;
  align-items: center;
  gap: 12px;
}

.trust-icon {
  font-size: 1.5rem;
}

.trust-text {
  display: flex;
  flex-direction: column;
}

.trust-text strong {
  color: var(--primary-black);
  font-size: 13px;
}

.trust-text span {
  color: var(--gray-medium);
  font-size: 12px;
}

/* Product Tabs */
.product-tabs {
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  margin-bottom: 4rem;
  overflow: hidden;
}

.tabs-header {
  display: flex;
  border-bottom: 1px solid var(--border-color);
}

.tab-btn {
  flex: 1;
  padding: 1.5rem 2rem;
  background: none;
  border: none;
  font-size: 16px;
  font-weight: 600;
  color: var(--gray-medium);
  cursor: pointer;
  transition: all 0.3s ease;
  border-bottom: 3px solid transparent;
}

.tab-btn.active {
  color: var(--primary-red);
  border-bottom-color: var(--primary-red);
  background: #fef2f2;
}

.tab-btn:hover {
  color: var(--primary-red);
  background: #fef2f2;
}

.tabs-content {
  padding: 2rem;
}

.tab-panel {
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.description-content h3 {
  color: var(--primary-black);
  margin-bottom: 1rem;
}

.features-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-top: 1.5rem;
}

.feature {
  display: flex;
  align-items: center;
  gap: 8px;
  color: var(--gray-dark);
}

.check-icon {
  color: #22c55e;
  font-weight: 700;
}

.specs-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.spec-item {
  display: flex;
  justify-content: space-between;
  padding: 12px 0;
  border-bottom: 1px solid var(--border-color);
}

.spec-name {
  font-weight: 600;
  color: var(--primary-black);
}

.spec-value {
  color: var(--gray-dark);
}

/* Reviews Tab */
.reviews-content {
  padding: 1rem 0;
}

.reviews-summary {
  display: flex;
  align-items: center;
  gap: 2rem;
  margin-bottom: 2rem;
  padding: 2rem;
  background: #fafafa;
  border-radius: 12px;
}

.average-rating {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.rating-score {
  font-size: 3rem;
  font-weight: 800;
  color: var(--primary-black);
}

.stars-large {
  display: flex;
  gap: 4px;
}

.stars-large .star {
  font-size: 24px;
}

.rating-count {
  color: var(--gray-medium);
  font-size: 14px;
}

.reviews-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.review {
  padding: 1.5rem;
  border: 1px solid var(--border-color);
  border-radius: 12px;
}

.review-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.reviewer {
  font-weight: 600;
  color: var(--primary-black);
}

.review-date {
  color: var(--gray-medium);
  font-size: 14px;
}

.review-rating {
  margin-bottom: 8px;
}

.review-text {
  color: var(--gray-dark);
  line-height: 1.6;
  margin: 0;
}

.no-reviews {
  text-align: center;
  padding: 3rem;
  color: var(--gray-medium);
}

/* Related Products */
.related-products {
  margin-top: 4rem;
}

.section-title {
  font-size: 1.8rem;
  font-weight: 800;
  color: var(--primary-black);
  margin-bottom: 2rem;
  text-align: center;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 2rem;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .product-layout {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .product-gallery {
    order: 1;
  }
  
  .product-info {
    order: 2;
  }
}

@media (max-width: 768px) {
  .product-layout {
    padding: 1.5rem;
  }
  
  .product-title {
    font-size: 1.6rem;
  }
  
  .action-buttons {
    flex-direction: column;
  }
  
  .tabs-header {
    flex-direction: column;
  }
  
  .tab-btn {
    padding: 1rem 1.5rem;
  }
  
  .tabs-content {
    padding: 1.5rem;
  }
  
  .reviews-summary {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }
  
  .trust-badges {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .container {
    padding: 0 15px;
  }
  
  .product-layout {
    padding: 1rem;
    margin-bottom: 2rem;
  }
  
  .variant-options {
    justify-content: center;
  }
  
  .quantity-selector {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .products-grid {
    grid-template-columns: 1fr;
  }
}

.multi-size-selection {
  margin: 1.5rem 0;
  padding: 1.5rem;
  background: #fafafa;
  border-radius: 12px;
}

.selection-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.btn-add-size {
  background: var(--primary-red);
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
}

.size-quantity-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px;
  margin-bottom: 10px;
  background: white;
  border-radius: 8px;
  border: 1px solid var(--border-color);
}

.row-controls {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
}

.size-select {
  padding: 10px;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  min-width: 200px;
}

.btn-remove {
  background: none;
  border: none;
  color: #ef4444;
  font-size: 1.5rem;
  cursor: pointer;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}

.btn-remove:hover {
  background: #fee2e2;
}

.row-total {
  font-weight: 700;
  color: var(--primary-red);
  min-width: 80px;
  text-align: right;
}
</style>