<template>
  <FrontendLayout>
    <div class="product-detail-page">
      <div class="container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
          <router-link to="/" class="breadcrumb-link">Home</router-link>
          <span class="breadcrumb-separator">/</span>
          <router-link to="/products" class="breadcrumb-link">Productsss</router-link>
          <span class="breadcrumb-separator">/</span>
          <router-link :to="`/categories/${product.category?.slug}`" class="breadcrumb-link" v-if="product.category">
            {{ product.category.name }}
          </router-link>
          <span class="breadcrumb-separator" v-if="product.category">/</span>
          <span class="breadcrumb-current">{{ product.name }}</span>
        </div>

        <div class="product-detail-layout">
          <!-- Product Gallery -->
          <div class="product-gallery">
            <div class="main-image">
              <img :src="selectedImage || product.main_image" :alt="product.name" />
              <div class="image-badges">
                <span class="badge new" v-if="product.is_new">NEW</span>
                <span class="badge sale" v-if="product.on_sale">SALE</span>
                <span class="badge out-of-stock" v-if="product.stock === 0">OUT OF STOCK</span>
              </div>
            </div>
            <div class="image-thumbnails" v-if="product.images && product.images.length > 1">
              <div 
                v-for="(image, index) in product.images" 
                :key="index"
                class="thumbnail"
                :class="{ active: selectedImage === image }"
                @click="selectedImage = image"
              >
                <img :src="image" :alt="`${product.name} - Image ${index + 1}`" />
              </div>
            </div>
          </div>

          <!-- Product Info -->
          <div class="product-info">
            <div class="product-header">
              <h1 class="product-title">{{ product.name }}</h1>
              <div class="product-meta">
                <span class="product-sku">SKU: {{ product.sku }}</span>
                <span class="product-rating">
                  ⭐⭐⭐⭐⭐ (4.8)
                  <span class="review-count">(128 reviews)</span>
                </span>
              </div>
            </div>

            <div class="product-price">
              <span class="current-price">£{{ product.price }}</span>
              <span class="original-price" v-if="product.original_price">£{{ product.original_price }}</span>
              <span class="discount-badge" v-if="product.original_price">
                Save {{ calculateDiscount(product.original_price, product.price) }}%
              </span>
            </div>

            <div class="product-description">
              <p>{{ product.description }}</p>
            </div>

            <!-- Variants -->
            <div class="product-variants" v-if="product.variants && product.variants.length">
              <div class="variant-option" v-for="option in variantOptions" :key="option.name">
                <label class="variant-label">{{ option.name }}:</label>
                <div class="variant-choices">
                  <button
                    v-for="value in option.values"
                    :key="value"
                    class="variant-choice"
                    :class="{ 
                      selected: selectedVariants[option.name] === value,
                      unavailable: !isVariantAvailable(option.name, value)
                    }"
                    @click="selectVariant(option.name, value)"
                    :disabled="!isVariantAvailable(option.name, value)"
                  >
                    {{ value }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Quantity & Add to Cart -->
            <div class="purchase-section">
              <div class="quantity-section">
                <label class="quantity-label">Quantity:</label>
                <div class="quantity-control">
                  <button class="qty-btn" @click="decreaseQuantity" :disabled="quantity <= 1">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                      <path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                  </button>
                  <span class="quantity">{{ quantity }}</span>
                  <button class="qty-btn" @click="increaseQuantity" :disabled="quantity >= availableStock">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                      <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                  </button>
                </div>
                <div class="stock-info" :class="{ 'low-stock': availableStock < 10 }">
                  {{ availableStock < 10 ? `Only ${availableStock} left in stock` : 'In stock' }}
                </div>
              </div>

              <div class="action-buttons">
                <button 
                  class="btn btn-primary add-to-cart-btn"
                  @click="addToCart"
                  :disabled="availableStock === 0"
                >
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M3 3H5L5.4 5M7 13H17L21 5H5.4M7 13L5.4 5M7 13L4.7 15.3C4.3 15.7 4.6 16.4 5.2 16.4H17M17 13V16.4M9 19C9 19.6 8.6 20 8 20C7.4 20 7 19.6 7 19C7 18.4 7.4 18 8 18C8.6 18 9 18.4 9 19ZM17 19C17 19.6 16.6 20 16 20C15.4 20 15 19.6 15 19C15 18.4 15.4 18 16 18C16.6 18 17 18.4 17 19Z" 
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  Add to Cart
                </button>
                <button 
                  class="btn btn-outline wishlist-btn"
                  @click="toggleWishlist"
                  :class="{ 'in-wishlist': isInWishlist }"
                >
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M12 21.35L10.55 20.03C5.4 15.36 2 12.28 2 8.5C2 5.42 4.42 3 7.5 3C9.24 3 10.91 3.81 12 5.09C13.09 3.81 14.76 3 16.5 3C19.58 3 22 5.42 22 8.5C22 12.28 18.6 15.36 13.45 20.04L12 21.35Z" 
                          :fill="isInWishlist ? 'var(--primary-red)' : 'none'"
                          stroke="var(--primary-red)" 
                          stroke-width="2" 
                          stroke-linecap="round" 
                          stroke-linejoin="round"/>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Product Features -->
            <div class="product-features">
              <div class="feature-item">
                <div class="feature-icon">🚚</div>
                <div class="feature-text">
                  <strong>Free Delivery</strong>
                  <span>On orders over £50</span>
                </div>
              </div>
              <div class="feature-item">
                <div class="feature-icon">↩️</div>
                <div class="feature-text">
                  <strong>30-Day Returns</strong>
                  <span>Money back guarantee</span>
                </div>
              </div>
              <div class="feature-item">
                <div class="feature-icon">🔒</div>
                <div class="feature-text">
                  <strong>Secure Payment</strong>
                  <span>100% protected</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Product Tabs -->
        <div class="product-tabs">
          <div class="tabs-header">
            <button 
              v-for="tab in tabs" 
              :key="tab.id"
              class="tab-btn"
              :class="{ active: activeTab === tab.id }"
              @click="activeTab = tab.id"
            >
              {{ tab.label }}
            </button>
          </div>
          <div class="tabs-content">
            <!-- Description Tab -->
            <div v-if="activeTab === 'description'" class="tab-panel">
              <div class="description-content" v-html="product.full_description"></div>
            </div>

            <!-- Specifications Tab -->
            <div v-if="activeTab === 'specifications'" class="tab-panel">
              <div class="specs-grid">
                <div class="spec-item" v-for="spec in product.specifications" :key="spec.name">
                  <span class="spec-name">{{ spec.name }}:</span>
                  <span class="spec-value">{{ spec.value }}</span>
                </div>
              </div>
            </div>

            <!-- Reviews Tab -->
            <div v-if="activeTab === 'reviews'" class="tab-panel">
              <div class="reviews-summary">
                <div class="rating-overview">
                  <div class="average-rating">
                    <span class="rating-score">4.8</span>
                    <div class="rating-stars">⭐⭐⭐⭐⭐</div>
                    <span class="rating-count">Based on 128 reviews</span>
                  </div>
                  <div class="rating-bars">
                    <div class="rating-bar" v-for="n in 5" :key="n">
                      <span class="star-count">{{ 6 - n }}⭐</span>
                      <div class="bar">
                        <div class="bar-fill" :style="{ width: getRatingPercentage(6 - n) + '%' }"></div>
                      </div>
                      <span class="percentage">{{ getRatingPercentage(6 - n) }}%</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="reviews-list">
                <div class="review-item" v-for="review in product.reviews" :key="review.id">
                  <div class="review-header">
                    <div class="reviewer-info">
                      <span class="reviewer-name">{{ review.user_name }}</span>
                      <div class="review-rating">
                        <span class="stars">⭐</span>
                        <span class="rating">{{ review.rating }}/5</span>
                      </div>
                    </div>
                    <span class="review-date">{{ formatDate(review.created_at) }}</span>
                  </div>
                  <p class="review-content">{{ review.comment }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Related Products -->
        <div class="related-products" v-if="relatedProducts.length">
          <h2 class="section-title">You May Also Like</h2>
          <div class="products-grid">
            <ProductCard 
              v-for="product in relatedProducts" 
              :key="product.id"
              :product="product"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Quick View Modal (for related products) -->
    <QuickViewModal 
      v-if="quickViewProduct"
      :product="quickViewProduct"
      @close="quickViewProduct = null"
    />
  </FrontendLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useCartStore } from '@/store/cart'
import { useWishlistStore } from '@/store/wishlist'
import FrontendLayout from '@/layouts/FrontendLayout.vue'
import ProductCard from '@/components/frontend/ProductCard.vue'
import QuickViewModal from '@/components/frontend/QuickViewModal.vue'

const route = useRoute()
const cartStore = useCartStore()
const wishlistStore = useWishlistStore()

const product = ref({})
const selectedImage = ref('')
const selectedVariants = ref({})
const quantity = ref(1)
const activeTab = ref('description')
const quickViewProduct = ref(null)

// Mock product data
const productData = {
  id: 1,
  name: 'Professional Work Jacket',
  sku: 'PWJ-2024-BLK',
  price: 89.99,
  original_price: 119.99,
  description: 'High-quality professional work jacket designed for durability and comfort in demanding work environments.',
  full_description: '<p>This professional work jacket is engineered for maximum durability and comfort. Made with premium materials that withstand harsh working conditions while providing excellent breathability and mobility.</p><p><strong>Features:</strong></p><ul><li>Water-resistant outer shell</li><li>Multiple utility pockets</li><li>Reinforced stitching</li><li>Adjustable cuffs and hem</li><li>Breathable lining</li></ul>',
  main_image: '/images/products/jacket-main.jpg',
  images: [
    '/images/products/jacket-main.jpg',
    '/images/products/jacket-1.jpg',
    '/images/products/jacket-2.jpg',
    '/images/products/jacket-3.jpg'
  ],
  category: {
    id: 1,
    name: 'Workwear',
    slug: 'workwear'
  },
  stock: 25,
  is_new: true,
  on_sale: true,
  variants: [
    {
      id: 1,
      color: 'Black',
      size: 'M',
      stock: 10,
      price: 89.99
    },
    {
      id: 2,
      color: 'Black',
      size: 'L',
      stock: 8,
      price: 89.99
    },
    {
      id: 3,
      color: 'Navy',
      size: 'M',
      stock: 7,
      price: 89.99
    }
  ],
  specifications: [
    { name: 'Material', value: 'Polyester-Cotton Blend' },
    { name: 'Color', value: 'Black, Navy' },
    { name: 'Sizes', value: 'S, M, L, XL, XXL' },
    { name: 'Care', value: 'Machine Washable' },
    { name: 'Water Resistance', value: 'Yes' },
    { name: 'Breathable', value: 'Yes' }
  ],
  reviews: [
    {
      id: 1,
      user_name: 'John D.',
      rating: 5,
      comment: 'Excellent quality and perfect fit. Very comfortable for long work days.',
      created_at: '2024-01-15'
    },
    {
      id: 2,
      user_name: 'Sarah M.',
      rating: 4,
      comment: 'Great jacket, keeps me dry and warm. Pockets are very useful.',
      created_at: '2024-01-10'
    }
  ]
}

const tabs = [
  { id: 'description', label: 'Description' },
  { id: 'specifications', label: 'Specifications' },
  { id: 'reviews', label: 'Reviews' }
]

const relatedProducts = ref([
  {
    id: 2,
    name: 'Work Trousers',
    price: 49.99,
    image: '/images/products/trousers.jpg',
    stock: 15,
    brand: 'Dickies'
  },
  {
    id: 3,
    name: 'Safety Boots',
    price: 79.99,
    image: '/images/products/boots.jpg',
    stock: 12,
    brand: 'CAT',
    on_sale: true
  },
  {
    id: 4,
    name: 'Hi-Vis Vest',
    price: 19.99,
    image: '/images/products/vest.jpg',
    stock: 30,
    brand: 'Portwest'
  },
  {
    id: 5,
    name: 'Work Gloves',
    price: 14.99,
    image: '/images/products/gloves.jpg',
    stock: 25,
    brand: 'Mechanix'
  }
])

// Computed properties
const variantOptions = computed(() => {
  const options = {}
  productData.variants.forEach(variant => {
    if (!options.color) options.color = { name: 'Color', values: new Set() }
    if (!options.size) options.size = { name: 'Size', values: new Set() }
    
    options.color.values.add(variant.color)
    options.size.values.add(variant.size)
  })

  return Object.values(options).map(option => ({
    name: option.name,
    values: Array.from(option.values)
  }))
})

const selectedVariant = computed(() => {
  return productData.variants.find(variant => 
    variant.color === selectedVariants.value.Color && 
    variant.size === selectedVariants.value.Size
  )
})

const availableStock = computed(() => {
  return selectedVariant.value ? selectedVariant.value.stock : productData.stock
})

const isInWishlist = computed(() => {
  return wishlistStore.isInWishlist(productData.id)
})

// Methods
const calculateDiscount = (original, current) => {
  return Math.round(((original - current) / original) * 100)
}

const selectVariant = (option, value) => {
  selectedVariants.value[option] = value
}

const isVariantAvailable = (option, value) => {
  return productData.variants.some(variant => 
    variant[option.toLowerCase()] === value && variant.stock > 0
  )
}

const increaseQuantity = () => {
  if (quantity.value < availableStock.value) {
    quantity.value++
  }
}

const decreaseQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--
  }
}

const addToCart = () => {
  const cartItem = {
    product_id: productData.id,
    quantity: quantity.value,
    product: {
      ...productData,
      selectedVariant: selectedVariant.value
    }
  }
  
  cartStore.addToCart(cartItem)
}

const toggleWishlist = () => {
  wishlistStore.addToWishlist(productData)
}

const getRatingPercentage = (rating) => {
  const percentages = { 5: 75, 4: 15, 3: 5, 2: 3, 1: 2 }
  return percentages[rating] || 0
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-GB')
}

// Lifecycle
onMounted(() => {
  // In real app, fetch product by ID from route params
  product.value = productData
  selectedImage.value = productData.main_image
  
  // Set default variants
  if (productData.variants && productData.variants.length) {
    const firstVariant = productData.variants[0]
    selectedVariants.value = {
      Color: firstVariant.color,
      Size: firstVariant.size
    }
  }
})
</script>

<style scoped src="@/assets/styles/frontend.css"></style>

<style scoped>
.product-detail-page {
  padding: 2rem 0 4rem;
}

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

.product-detail-layout {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  margin-bottom: 4rem;
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
  background: var(--gray-light);
}

.main-image img {
  width: 100%;
  height: 500px;
  object-fit: cover;
}

.image-badges {
  position: absolute;
  top: 1rem;
  left: 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.badge {
  padding: 6px 12px;
  border-radius: 4px;
  font-size: 0.75rem;
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

.image-thumbnails {
  display: flex;
  gap: 0.75rem;
  overflow-x: auto;
}

.thumbnail {
  width: 80px;
  height: 80px;
  border-radius: 8px;
  overflow: hidden;
  cursor: pointer;
  border: 2px solid transparent;
  transition: border-color 0.3s ease;
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
  margin: 0 0 0.5rem 0;
  color: var(--primary-black);
  font-size: 2rem;
  font-weight: 700;
  line-height: 1.2;
}

.product-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
}

.product-sku {
  color: var(--gray-medium);
  font-size: 0.9rem;
}

.product-rating {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9rem;
}

.review-count {
  color: var(--gray-medium);
}

.product-price {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
}

.current-price {
  font-size: 2rem;
  font-weight: 700;
  color: var(--primary-red);
}

.original-price {
  font-size: 1.5rem;
  color: var(--gray-medium);
  text-decoration: line-through;
}

.discount-badge {
  background: var(--primary-red);
  color: var(--primary-white);
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 0.8rem;
  font-weight: 600;
}

.product-description p {
  color: var(--gray-dark);
  line-height: 1.6;
  margin: 0;
}

/* Variants */
.product-variants {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.variant-option {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.variant-label {
  font-weight: 600;
  color: var(--primary-black);
}

.variant-choices {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.variant-choice {
  padding: 8px 16px;
  border: 2px solid var(--border-color);
  border-radius: 6px;
  background: var(--primary-white);
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 500;
}

.variant-choice:hover:not(.unavailable) {
  border-color: var(--primary-red);
}

.variant-choice.selected {
  background: var(--primary-red);
  border-color: var(--primary-red);
  color: var(--primary-white);
}

.variant-choice.unavailable {
  opacity: 0.5;
  cursor: not-allowed;
  text-decoration: line-through;
}

/* Purchase Section */
.purchase-section {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  padding: 1.5rem;
  background: #f8fafc;
  border-radius: 12px;
}

.quantity-section {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
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
  background: var(--primary-white);
  border: none;
  padding: 8px 12px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.qty-btn:hover:not(:disabled) {
  background: var(--gray-light);
}

.qty-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.quantity {
  padding: 8px 16px;
  font-weight: 600;
  min-width: 40px;
  text-align: center;
  background: #f8f9fa;
}

.stock-info {
  font-size: 0.9rem;
  font-weight: 500;
}

.stock-info.low-stock {
  color: #ef4444;
}

.action-buttons {
  display: flex;
  gap: 1rem;
}

.add-to-cart-btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 24px;
  font-size: 1.1rem;
  font-weight: 600;
}

.wishlist-btn {
  padding: 12px;
  width: 54px;
}

.wishlist-btn.in-wishlist {
  background: #fee;
  border-color: var(--primary-red);
  color: var(--primary-red);
}

/* Product Features */
.product-features {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  padding: 1.5rem 0;
  border-top: 1px solid var(--border-color);
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.feature-icon {
  font-size: 1.5rem;
}

.feature-text {
  display: flex;
  flex-direction: column;
}

.feature-text strong {
  color: var(--primary-black);
  font-size: 0.9rem;
}

.feature-text span {
  color: var(--gray-medium);
  font-size: 0.8rem;
}

/* Product Tabs */
.product-tabs {
  background: var(--primary-white);
  border-radius: 12px;
  box-shadow: var(--shadow-light);
  margin-bottom: 4rem;
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
  font-weight: 600;
  color: var(--gray-medium);
  cursor: pointer;
  transition: all 0.3s ease;
  border-bottom: 3px solid transparent;
}

.tab-btn.active {
  color: var(--primary-red);
  border-bottom-color: var(--primary-red);
}

.tab-btn:hover {
  color: var(--primary-red);
}

.tabs-content {
  padding: 2rem;
}

.tab-panel {
  min-height: 200px;
}

/* Description */
.description-content {
  line-height: 1.7;
  color: var(--gray-dark);
}

.description-content :deep(h3) {
  color: var(--primary-black);
  margin: 1.5rem 0 0.5rem 0;
}

.description-content :deep(ul) {
  padding-left: 1.5rem;
  margin: 1rem 0;
}

.description-content :deep(li) {
  margin: 0.5rem 0;
}

/* Specifications */
.specs-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.spec-item {
  display: flex;
  justify-content: space-between;
  padding: 0.75rem 0;
  border-bottom: 1px solid var(--border-color);
}

.spec-name {
  font-weight: 500;
  color: var(--gray-dark);
}

.spec-value {
  color: var(--primary-black);
  font-weight: 600;
}

/* Reviews */
.reviews-summary {
  margin-bottom: 2rem;
  padding: 1.5rem;
  background: #f8fafc;
  border-radius: 8px;
}

.rating-overview {
  display: grid;
  grid-template-columns: auto 1fr;
  gap: 2rem;
  align-items: center;
}

.average-rating {
  text-align: center;
}

.rating-score {
  display: block;
  font-size: 3rem;
  font-weight: 700;
  color: var(--primary-black);
}

.rating-stars {
  margin: 0.5rem 0;
}

.rating-count {
  color: var(--gray-medium);
  font-size: 0.9rem;
}

.rating-bars {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.rating-bar {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.star-count {
  font-size: 0.9rem;
  min-width: 40px;
}

.bar {
  flex: 1;
  height: 8px;
  background: var(--border-color);
  border-radius: 4px;
  overflow: hidden;
}

.bar-fill {
  height: 100%;
  background: var(--primary-red);
  border-radius: 4px;
  transition: width 0.3s ease;
}

.percentage {
  font-size: 0.9rem;
  color: var(--gray-medium);
  min-width: 40px;
}

.reviews-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.review-item {
  padding: 1.5rem;
  border: 1px solid var(--border-color);
  border-radius: 8px;
}

.review-header {
  display: flex;
  justify-content: space-between;
  align-items: start;
  margin-bottom: 0.75rem;
}

.reviewer-info {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.reviewer-name {
  font-weight: 600;
  color: var(--primary-black);
}

.review-rating {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.review-date {
  color: var(--gray-medium);
  font-size: 0.9rem;
}

.review-content {
  color: var(--gray-dark);
  line-height: 1.6;
  margin: 0;
}

/* Related Products */
.related-products {
  margin-top: 4rem;
}

.section-title {
  text-align: center;
  margin-bottom: 2rem;
  color: var(--primary-black);
  font-size: 1.8rem;
  font-weight: 700;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 2rem;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .product-detail-layout {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .rating-overview {
    grid-template-columns: 1fr;
    text-align: center;
  }
}

@media (max-width: 768px) {
  .product-meta {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .action-buttons {
    flex-direction: column;
  }
  
  .tabs-header {
    flex-direction: column;
  }
  
  .tab-btn {
    text-align: left;
  }
  
  .quantity-section {
    flex-direction: column;
    align-items: flex-start;
  }
}

@media (max-width: 480px) {
  .product-title {
    font-size: 1.5rem;
  }
  
  .product-price {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .variant-choices {
    justify-content: center;
  }
  
  .product-features {
    grid-template-columns: 1fr;
  }
}
</style>