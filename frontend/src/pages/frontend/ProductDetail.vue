<!-- pages/frontend/ProductDetail.vue -->
<template>
  <FrontendLayout>
    <Loader :loading="loading" />
    
    <div class="product-detail">
      <div class="container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
          <router-link to="/" class="breadcrumb-link">Home</router-link>
          <span class="breadcrumb-separator">/</span>
          <router-link to="/products" class="breadcrumb-link">Products</router-link>
          <span class="breadcrumb-separator">/</span>
          <router-link :to="`/products?category=${product.category}`" class="breadcrumb-link">{{ product.category }}</router-link>
          <span class="breadcrumb-separator">/</span>
          <span class="breadcrumb-current">{{ product.name }}</span>
        </nav>

        <div class="product-layout">
          <!-- Product Images -->
          <div class="product-gallery">
            <div class="main-image">
              <img :src="selectedImage || product.mainImage" :alt="product.name" />
              <div class="image-badge" v-if="product.isNew">New</div>
              <div class="image-badge sale" v-if="product.onSale">Sale</div>
            </div>
            <div class="image-thumbnails">
              <div 
                v-for="(image, index) in product.images" 
                :key="index"
                class="thumbnail"
                :class="{ active: selectedImage === image }"
                @click="selectedImage = image"
              >
                <img :src="image" :alt="`${product.name} view ${index + 1}`" />
              </div>
            </div>
          </div>

          <!-- Product Info -->
          <div class="product-info">
            <div class="product-header">
              <h1 class="product-title">{{ product.name }}</h1>
              <div class="product-rating">
                <div class="stars">
                  <span v-for="n in 5" :key="n" class="star" :class="{ filled: n <= product.rating }">★</span>
                </div>
                <span class="rating-text">({{ product.reviewCount }} reviews)</span>
              </div>
            </div>

            <div class="product-pricing">
              <div class="price-container">
                <span class="current-price">£{{ product.price }}</span>
                <span class="original-price" v-if="product.originalPrice">£{{ product.originalPrice }}</span>
                <span class="discount-badge" v-if="product.onSale">Save {{ calculateDiscount() }}%</span>
              </div>
              <div class="vat-text">Inc. VAT</div>
            </div>

            <div class="product-description">
              <p>{{ product.description }}</p>
            </div>

            <!-- Product Variants -->
            <div class="product-variants">
              <!-- Color Variant -->
              <div class="variant-group" v-if="product.colors && product.colors.length > 0">
                <label class="variant-label">Color:</label>
                <div class="variant-options">
                  <div
                    v-for="color in product.colors"
                    :key="color.id"
                    class="variant-option color-option"
                    :class="{ 
                      active: selectedVariant.color === color.id,
                      unavailable: !color.inStock 
                    }"
                    @click="selectColor(color)"
                    :title="color.inStock ? color.name : 'Out of stock'"
                  >
                    <span class="color-swatch" :style="{ backgroundColor: color.hex }"></span>
                    <span class="color-name">{{ color.name }}</span>
                  </div>
                </div>
              </div>

              <!-- Size Variant -->
              <div class="variant-group" v-if="product.sizes && product.sizes.length > 0">
                <label class="variant-label">Size:</label>
                <div class="variant-options">
                  <div
                    v-for="size in availableSizes"
                    :key="size.id"
                    class="variant-option size-option"
                    :class="{ 
                      active: selectedVariant.size === size.id,
                      unavailable: !size.inStock 
                    }"
                    @click="selectSize(size)"
                    :title="size.inStock ? size.name : 'Out of stock'"
                  >
                    {{ size.name }}
                  </div>
                </div>
                <a href="#" class="size-guide-link">Size Guide</a>
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
                
                <button class="btn btn-outline wishlist-btn">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M19.5 12.572L12 20L4.5 12.572C3.605 11.677 3 10.391 3 9C3 6.791 4.791 5 7 5C8.309 5 9.5 5.671 10.214 6.714L12 9L13.786 6.714C14.5 5.671 15.691 5 17 5C19.209 5 21 6.791 21 9C21 10.391 20.395 11.677 19.5 12.572Z" 
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  Wishlist
                </button>
              </div>
            </div>

            <!-- Trust Badges -->
            <div class="trust-badges">
              <div class="trust-item">
                <div class="trust-icon">🚚</div>
                <div class="trust-text">
                  <strong>Free Delivery</strong>
                  <span>On orders over £50</span>
                </div>
              </div>
              <div class="trust-item">
                <div class="trust-icon">↩️</div>
                <div class="trust-text">
                  <strong>30-Day Returns</strong>
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
              <div class="description-content">
                <h3>Product Description</h3>
                <p>{{ product.fullDescription }}</p>
                <div class="features-list">
                  <div v-for="feature in product.features" :key="feature" class="feature">
                    <span class="check-icon">✓</span>
                    {{ feature }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Specifications Tab -->
            <div v-if="activeTab === 'specifications'" class="tab-panel">
              <div class="specs-grid">
                <div v-for="spec in product.specifications" :key="spec.name" class="spec-item">
                  <span class="spec-name">{{ spec.name }}:</span>
                  <span class="spec-value">{{ spec.value }}</span>
                </div>
              </div>
            </div>

            <!-- Reviews Tab -->
            <div v-if="activeTab === 'reviews'" class="tab-panel">
              <div class="reviews-content">
                <div class="reviews-summary">
                  <div class="average-rating">
                    <div class="rating-score">{{ product.rating }}</div>
                    <div class="stars-large">
                      <span v-for="n in 5" :key="n" class="star" :class="{ filled: n <= product.rating }">★</span>
                    </div>
                    <div class="rating-count">{{ product.reviewCount }} reviews</div>
                  </div>
                </div>
                
                <div class="reviews-list">
                  <div v-for="review in product.reviews" :key="review.id" class="review">
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
              </div>
            </div>
          </div>
        </div>

        <!-- Related Products -->
        <div class="related-products">
          <h2 class="section-title">You May Also Like</h2>
          <div class="products-grid">
            <ProductCard 
              v-for="relatedProduct in relatedProducts" 
              :key="relatedProduct.id" 
              :product="relatedProduct"
              @add-to-cart="handleAddToCart"
            />
          </div>
        </div>
      </div>
    </div>
  </FrontendLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { toast } from 'vue3-toastify'
import FrontendLayout from '@/layouts/FrontendLayout.vue'
import ProductCard from '@/components/frontend/ProductCard.vue'
import Loader from '@/components/frontend/Loader.vue'
import { useCartStore } from '@/store/cart'

const route = useRoute()
const cartStore = useCartStore()

const loading = ref(true)
const product = ref({})
const selectedImage = ref('')
const selectedVariant = ref({
  color: null,
  size: null
})
const quantity = ref(1)
const activeTab = ref('description')

// Sample product data - in real app, this would come from API
const sampleProduct = {
  id: 1,
  name: "Professional Work Jacket - Hi-Vis Orange",
  category: "workwear",
  description: "High-visibility professional work jacket with multiple pockets and waterproof construction.",
  fullDescription: "This professional work jacket is designed for maximum visibility and comfort in demanding work environments. Features include waterproof construction, multiple utility pockets, reinforced stitching, and breathable fabric for all-day comfort.",
  price: 49.99,
  originalPrice: 64.99,
  onSale: true,
  isNew: true,
  rating: 4.5,
  reviewCount: 124,
  mainImage: "https://images.unsplash.com/photo-1551488831-00ddcb6c6bd3?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80",
  images: [
    "https://images.unsplash.com/photo-1551488831-00ddcb6c6bd3?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80",
    "https://images.unsplash.com/photo-1591047139829-d91aecb6caea?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80",
    "https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80"
  ],
  colors: [
    { id: 'orange', name: 'Hi-Vis Orange', hex: '#FF6B00', inStock: true },
    { id: 'yellow', name: 'Hi-Vis Yellow', hex: '#FFD600', inStock: true },
    { id: 'red', name: 'Hi-Vis Red', hex: '#FF3D00', inStock: false }
  ],
  sizes: [
    { id: 's', name: 'S', inStock: true, stock: 15 },
    { id: 'm', name: 'M', inStock: true, stock: 25 },
    { id: 'l', name: 'L', inStock: true, stock: 8 },
    { id: 'xl', name: 'XL', inStock: true, stock: 12 },
    { id: 'xxl', name: 'XXL', inStock: false, stock: 0 }
  ],
  features: [
    "Waterproof and windproof construction",
    "Multiple utility pockets for tools",
    "Reflective stripes for enhanced visibility",
    "Breathable fabric with ventilation",
    "Reinforced stitching for durability",
    "Adjustable cuffs and hem"
  ],
  specifications: [
    { name: 'Material', value: 'Polyester with PVC coating' },
    { name: 'Color', value: 'Hi-Vis Orange/Yellow' },
    { name: 'Sizes', value: 'S-XXL' },
    { name: 'Waterproof', value: 'Yes' },
    { name: 'Breathable', value: 'Yes' },
    { name: 'Pockets', value: '6' },
    { name: 'Certification', value: 'EN ISO 20471' }
  ],
  reviews: [
    {
      id: 1,
      reviewer: "John Construction",
      rating: 5,
      date: "2 weeks ago",
      comment: "Excellent jacket! Very durable and keeps me dry in all weather conditions. The visibility is fantastic."
    },
    {
      id: 2,
      reviewer: "Sarah Builder",
      rating: 4,
      date: "1 month ago",
      comment: "Great quality and fits perfectly. The pockets are very useful for carrying tools. Highly recommended!"
    }
  ]
}

const tabs = [
  { id: 'description', label: 'Description' },
  { id: 'specifications', label: 'Specifications' },
  { id: 'reviews', label: `Reviews (${sampleProduct.reviewCount})` }
]

const relatedProducts = ref([
  {
    id: 2,
    name: "Work Trousers - Multi-Pocket",
    price: 39.99,
    image: "https://images.unsplash.com/photo-1586790170083-2f9ceadc732d?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80",
    category: "workwear"
  },
  {
    id: 3,
    name: "Safety Helmet - Vented",
    price: 24.99,
    image: "https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80",
    category: "safety"
  },
  {
    id: 4,
    name: "Steel Toe Boots - Waterproof",
    price: 89.99,
    image: "https://images.unsplash.com/photo-1542281286-9e0a16bb7366?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80",
    category: "footwear"
  }
])

// Computed properties
const availableSizes = computed(() => {
  if (!selectedVariant.value.color) return sampleProduct.sizes
  // In real app, filter sizes based on selected color availability
  return sampleProduct.sizes
})

const selectedVariantStock = computed(() => {
  if (!selectedVariant.value.size) return 0
  const size = sampleProduct.sizes.find(s => s.id === selectedVariant.value.size)
  return size ? size.stock : 0
})

const isInStock = computed(() => {
  return selectedVariantStock.value > 0
})

const maxQuantity = computed(() => {
  return selectedVariantStock.value
})

const calculateDiscount = () => {
  const discount = ((sampleProduct.originalPrice - sampleProduct.price) / sampleProduct.originalPrice) * 100
  return Math.round(discount)
}

// Methods
const selectColor = (color) => {
  if (!color.inStock) return
  selectedVariant.value.color = color.id
}

const selectSize = (size) => {
  if (!size.inStock) return
  selectedVariant.value.size = size.id
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

const addToCart = () => {
  if (!isInStock.value) {
    toast.error('This product is out of stock!')
    return
  }

  const variant = {
    color: selectedVariant.value.color,
    size: selectedVariant.value.size
  }

  cartStore.addToCart(sampleProduct, quantity.value, variant)
  toast.success('Product added to cart!')
  
  // Reset quantity
  quantity.value = 1
}

const handleAddToCart = (product) => {
  cartStore.addToCart(product)
  toast.success('Product added to cart!')
}

// Lifecycle
onMounted(async () => {
  // Simulate API call
  setTimeout(() => {
    product.value = sampleProduct
    selectedImage.value = sampleProduct.mainImage
    // Set default variants
    if (sampleProduct.colors.length > 0) {
      selectedVariant.value.color = sampleProduct.colors[0].id
    }
    if (sampleProduct.sizes.length > 0) {
      selectedVariant.value.size = sampleProduct.sizes[0].id
    }
    loading.value = false
  }, 1000)
})
</script>

<style scoped>
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
</style>