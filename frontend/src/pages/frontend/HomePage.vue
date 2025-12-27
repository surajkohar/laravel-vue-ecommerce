<!-- pages/frontend/HomePage.vue -->
<template>
  <FrontendLayout>
    <Loader :loading="loading" />
    
    <!-- Hero Carousel -->
    <section class="hero-carousel">
      <div class="carousel-slides">
        <div 
          class="carousel-slide" 
          :class="{ active: currentSlide === index }" 
          v-for="(slide, index) in carouselSlides" 
          :key="index"
        >
          <div class="slide-image">
            <img :src="slide.image" :alt="slide.title" />
          </div>
          <div class="slide-overlay"></div>
          <div class="container">
            <div class="slide-content">
              <h1 class="slide-title">{{ slide.title }}</h1>
              <p class="slide-description">{{ slide.description }}</p>
              <div class="slide-buttons">
                <router-link to="/products" class="btn btn-primary">{{ slide.primaryButton }}</router-link>
                <router-link to="/products?category=safety" class="btn btn-outline">{{ slide.secondaryButton }}</router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Carousel Navigation -->
      <button class="carousel-nav carousel-prev" @click="prevSlide">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      <button class="carousel-nav carousel-next" @click="nextSlide">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      
      <div class="carousel-dots">
        <span 
          class="dot" 
          :class="{ active: currentSlide === index }" 
          v-for="(slide, index) in carouselSlides" 
          :key="index"
          @click="goToSlide(index)"
        ></span>
      </div>
    </section>

    <!-- Rest of your template remains the same -->
    <!-- Features Bar -->
    <section class="features-bar">
      <div class="container">
        <div class="features-grid">
          <div class="feature-item">
            <div class="feature-icon">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none">
                <path d="M5 8H19M5 8C3.89543 8 3 7.10457 3 6C3 4.89543 3.89543 4 5 4H19C20.1046 4 21 4.89543 21 6C21 7.10457 20.1046 8 19 8M5 8L5 18C5 19.1046 5.89543 20 7 20H17C18.1046 20 19 19.1046 19 18V8M9 12H15" stroke="#E31B23" stroke-width="2"/>
              </svg>
            </div>
            <div class="feature-text">
              <h4>FREE DELIVERY</h4>
              <p>On orders over £50</p>
            </div>
          </div>
          
          <div class="feature-item">
            <div class="feature-icon">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none">
                <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="#E31B23" stroke-width="2"/>
                <path d="M19.4 15C19.2669 15.3044 19.1346 15.6088 19 15.912C18.788 16.372 18.539 16.872 18.268 17.372C17.599 18.572 16.839 19.772 16.109 20.572C15.489 21.272 14.849 21.772 14.349 22.072C14.0815 22.2345 13.814 22.397 13.549 22.552C13.049 22.852 12.549 23.052 12.149 23.152C12.099 23.1653 12.0497 23.1773 12 23.188V23.188C11.9503 23.1773 11.901 23.1653 11.851 23.152C11.451 23.052 10.951 22.852 10.451 22.552C10.186 22.397 9.91854 22.2345 9.651 22.072C9.151 21.772 8.511 21.272 7.891 20.572C7.161 19.772 6.401 18.572 5.732 17.372C5.461 16.872 5.212 16.372 5 15.912C4.86545 15.6088 4.73309 15.3044 4.6 15C4.211 14.1 4 13.2 4 12.2C4 11.5565 4.1229 10.919 4.361 10.3212C4.5991 9.72345 4.94821 9.17686 5.3888 8.71216C5.8294 8.24745 6.35287 7.87367 6.929 7.61111C7.50514 7.34855 8.12303 7.20237 8.749 7.18005C9.375 7.20237 9.9929 7.34855 10.569 7.61111C11.1451 7.87367 11.6686 8.24745 12.1092 8.71216C12.5498 9.17686 12.8989 9.72345 13.137 10.3212C13.3751 10.919 13.498 11.5565 13.498 12.2C13.498 13.2 13.289 14.1 12.9 15" stroke="#E31B23" stroke-width="2"/>
              </svg>
            </div>
            <div class="feature-text">
              <h4>30+ YEARS EXPERIENCE</h4>
              <p>Trusted workwear supplier</p>
            </div>
          </div>
          
          <div class="feature-item">
            <div class="feature-icon">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none">
                <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#E31B23" stroke-width="2"/>
              </svg>
            </div>
            <div class="feature-text">
              <h4>QUALITY GUARANTEE</h4>
              <p>Premium products</p>
            </div>
          </div>
          
          <div class="feature-item">
            <div class="feature-icon">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none">
                <path d="M16 8C16 6.89543 15.1046 6 14 6H10C8.89543 6 8 6.89543 8 8M16 8C16 9.10457 15.1046 10 14 10H10C8.89543 10 8 9.10457 8 8M16 8V18C16 19.1046 15.1046 20 14 20H10C8.89543 20 8 19.1046 8 18V8M12 14V14.01M12 17V17.01" stroke="#E31B23" stroke-width="2"/>
              </svg>
            </div>
            <div class="feature-text">
              <h4>SECURE PAYMENT</h4>
              <p>Safe & encrypted</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Our Products Section -->
    <section class="our-products">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Our Products</h2>
          <p class="section-subtitle">Discover our wide range of quality workwear and safety equipment</p>
        </div>
        <div class="products-tabs">
          <div class="tab-buttons">
            <button class="tab-button active" data-tab="featured">Featured</button>
            <button class="tab-button" data-tab="trending">Trending</button>
            <button class="tab-button" data-tab="new-arrival">New Arrival</button>
          </div>
          <div class="tab-content">
            <div class="tab-pane active" id="featured">
              <div class="products-grid">
                <ProductCard 
                  v-for="product in featuredProducts" 
                  :key="product.id" 
                  :product="product"
                  @add-to-cart="handleAddToCart"
                  @quick-view="handleQuickView"
                />
              </div>
            </div>
            <div class="tab-pane" id="trending">
              <div class="products-grid">
                <ProductCard 
                  v-for="product in trendingProducts" 
                  :key="product.id" 
                  :product="product"
                  @add-to-cart="handleAddToCart"
                  @quick-view="handleQuickView"
                />
              </div>
            </div>
            <div class="tab-pane" id="new-arrival">
              <div class="products-grid">
                <ProductCard 
                  v-for="product in newArrivalProducts" 
                  :key="product.id" 
                  :product="product"
                  @add-to-cart="handleAddToCart"
                  @quick-view="handleQuickView"
                />
              </div>
            </div>
          </div>
          <div class="view-all-container">
            <router-link to="/products" class="view-all-link">View All Products</router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Our Best Sellers -->
    <section class="best-sellers">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Our Best Sellers</h2>
        </div>
        <div class="products-scroller">
          <button class="scroll-btn scroll-left" @click="scrollLeft">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
          <div class="products-scroll-container" ref="scrollContainer">
            <div class="products-scroll">
              <ProductCard 
                v-for="product in bestSellers" 
                :key="product.id" 
                :product="product"
                @add-to-cart="handleAddToCart"
                @quick-view="handleQuickView"
                class="scroll-item"
              />
            </div>
          </div>
          <button class="scroll-btn scroll-right" @click="scrollRight">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
        </div>
      </div>
    </section>

    <!-- Our Clients Say -->
    <section class="testimonials">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Our Clients Say</h2>
          <p class="section-subtitle">Hear what our customers have to say about our products and service</p>
        </div>
        <div class="testimonials-grid">
          <div class="testimonial-card" v-for="testimonial in testimonials" :key="testimonial.id">
            <div class="testimonial-content">
              <div class="quote-icon">"</div>
              <p class="testimonial-text">{{ testimonial.text }}</p>
              <div class="testimonial-author">
                <img :src="testimonial.avatar" :alt="testimonial.name" class="author-avatar" />
                <div class="author-info">
                  <h4>{{ testimonial.name }}</h4>
                  <p>{{ testimonial.position }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Brand Logos -->
    <section class="brands-section">
      <div class="container">
        <h2 class="section-title">Trusted Brands</h2>
        <div class="brands-scroller">
          <div class="brands-track">
            <div class="brand-logo" v-for="brand in brands" :key="brand">
              <span>{{ brand }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Quick View Modal -->
    <QuickViewModal 
      v-if="quickViewProduct"
      :product="quickViewProduct"
      @close="quickViewProduct = null"
    />
  </FrontendLayout>
</template>

<script setup>
import { ref, onMounted, nextTick, onUnmounted } from 'vue'
import { toast } from 'vue3-toastify'
import FrontendLayout from '@/layouts/FrontendLayout.vue'
import ProductCard from '@/components/frontend/ProductCard.vue'
import QuickViewModal from '@/components/frontend/QuickViewModal.vue'
import Loader from '@/components/frontend/Loader.vue'
import { useProductsStore } from '@/store/products'
import { useCartStore } from '@/store/cart'

const productsStore = useProductsStore()
const cartStore = useCartStore()

const loading = ref(true)
const featuredProducts = ref([])
const trendingProducts = ref([])
const newArrivalProducts = ref([])
const bestSellers = ref([])
const quickViewProduct = ref(null)
const scrollContainer = ref(null)
const currentSlide = ref(0)
const autoSlideInterval = ref(null)

// Carousel Data with WORKING images
const carouselSlides = ref([
  {
    title: "PREMIUM WORKWEAR & SAFETY EQUIPMENT",
    description: "Trusted by professionals across industries for over 30 years",
    primaryButton: "SHOP WORKWEAR",
    secondaryButton: "VIEW SAFETY GEAR",
    image: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTGgGMC9Qb5rVMA_padhX8p4eRCSW_aO58Vrw&s"
  },
  {
    title: "INDUSTRIAL SAFETY SOLUTIONS",
    description: "Complete range of PPE and safety equipment for all workplaces",
    primaryButton: "SHOP SAFETY GEAR",
    secondaryButton: "BULK ORDERS",
    image: "https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80"
  },
  {
    title: "CORPORATE & PROFESSIONAL WEAR",
    description: "Elevate your team's professional appearance with our corporate range",
    primaryButton: "SHOP CORPORATE",
    secondaryButton: "REQUEST CATALOGUE",
    image: "https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80"
  }
])

// Data
const testimonials = ref([
  {
    id: 1,
    text: "Excellent quality workwear that lasts. The customer service is outstanding and delivery is always prompt. Highly recommended for any business!",
    name: "John Smith",
    position: "Construction Manager",
    avatar: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80"
  },
  {
    id: 2,
    text: "We've been using Pinders for our company's workwear for over 5 years. The quality is consistently excellent and their range is perfect for our needs.",
    name: "Sarah Johnson",
    position: "Facilities Director",
    avatar: "https://images.unsplash.com/photo-1494790108755-2616b612b786?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80"
  },
  {
    id: 3,
    text: "Great products at competitive prices. The safety equipment meets all standards and our team feels protected and professional in their gear.",
    name: "Mike Thompson",
    position: "Safety Officer",
    avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80"
  }
])

const brands = ref(['DICKIES', 'SNICKERS', 'PORTWEST', 'HELLY HANSEN', 'REGATTA', 'CAT', 'DEWALT', '3M'])

// Methods
const handleAddToCart = (product) => {
  cartStore.addToCart(product)
  toast.success(`${product.name} added to cart!`, {
    position: "top-right",
    autoClose: 3000
  })
}

const handleQuickView = (product) => {
  quickViewProduct.value = product
}

const scrollLeft = () => {
  if (scrollContainer.value) {
    scrollContainer.value.scrollBy({ left: -300, behavior: 'smooth' })
  }
}

const scrollRight = () => {
  if (scrollContainer.value) {
    scrollContainer.value.scrollBy({ left: 300, behavior: 'smooth' })
  }
}

// Carousel Methods
const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % carouselSlides.value.length
}

const prevSlide = () => {
  currentSlide.value = (currentSlide.value - 1 + carouselSlides.value.length) % carouselSlides.value.length
}

const goToSlide = (index) => {
  currentSlide.value = index
}

const startAutoSlide = () => {
  autoSlideInterval.value = setInterval(() => {
    nextSlide()
  }, 5000) // Change slide every 5 seconds
}

const stopAutoSlide = () => {
  if (autoSlideInterval.value) {
    clearInterval(autoSlideInterval.value)
  }
}

// Tab switching functionality
const switchTab = (tabName) => {
  // Remove active class from all tabs and panes
  document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
  document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
  
  // Add active class to selected tab and pane
  document.querySelector(`.tab-button[data-tab="${tabName}"]`).classList.add('active');
  document.getElementById(tabName).classList.add('active');
}

// Lifecycle
// In HomePage.vue script section
onMounted(async () => {
  try {
    // Fetch featured products
    await productsStore.fetchFeaturedProducts()
    
    // If no featured products endpoint, use regular products
    if (productsStore.featuredProducts.length === 0) {
      await productsStore.fetchProducts(1)
      // Use first 12 products for different sections
      featuredProducts.value = productsStore.products.slice(0, 4)
      trendingProducts.value = productsStore.products.slice(4, 8)
      newArrivalProducts.value = productsStore.products.slice(8, 12)
      bestSellers.value = productsStore.products.slice(0, 6)
    } else {
      // Use featured products
      featuredProducts.value = productsStore.featuredProducts.slice(0, 4)
      trendingProducts.value = productsStore.featuredProducts.slice(4, 8)
      newArrivalProducts.value = productsStore.featuredProducts.slice(8, 12)
      bestSellers.value = productsStore.featuredProducts.slice(0, 6)
    }
    
    loading.value = false
    
    // Start auto slide
    startAutoSlide()
    
    // Add tab switching event listeners
    nextTick(() => {
      document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', () => {
          switchTab(button.dataset.tab);
        });
      });
    });
  } catch (error) {
    console.error('Error loading homepage:', error)
    loading.value = false
  }
})

// Cleanup
onUnmounted(() => {
  stopAutoSlide()
})
</script>

<style scoped>
/* Hero Carousel */
.hero-carousel {
  position: relative;
  height: 600px;
  overflow: hidden;
  background: #000;
}

.carousel-slides {
  position: relative;
  height: 100%;
  width: 100%;
}

.carousel-slide {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  transition: opacity 0.8s ease;
  display: flex;
  align-items: center;
}

.carousel-slide.active {
  opacity: 1;
}

.slide-image {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

.slide-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.slide-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.4);
  z-index: 1;
}

.slide-content {
  position: relative;
  z-index: 2;
  color: white;
  max-width: 600px;
  padding-left: 50px;
  transform: translateX(-50px);
  opacity: 0;
  transition: all 0.8s ease 0.3s;
}

.carousel-slide.active .slide-content {
  transform: translateX(0);
  opacity: 1;
}

.slide-title {
  font-size: 3.2rem;
  font-weight: 800;
  margin-bottom: 1.5rem;
  text-transform: uppercase;
  letter-spacing: 2px;
  line-height: 1.1;
}

.slide-description {
  font-size: 1.3rem;
  margin-bottom: 2.5rem;
  opacity: 0.9;
  font-weight: 400;
}

.slide-buttons {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.btn {
  display: inline-block;
  padding: 12px 30px;
  font-weight: 600;
  text-transform: uppercase;
  text-decoration: none;
  border-radius: 4px;
  transition: all 0.3s ease;
  font-size: 0.9rem;
  letter-spacing: 0.5px;
}

.btn-primary {
  background: var(--primary-red);
  color: white;
  border: 2px solid var(--primary-red);
}

.btn-primary:hover {
  background: #c00;
  border-color: #c00;
}

.btn-outline {
  background: transparent;
  color: white;
  border: 2px solid white;
}

.btn-outline:hover {
  background: white;
  color: var(--primary-black);
}

/* Carousel Navigation */
.carousel-nav {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(255, 255, 255, 0.2);
  border: none;
  border-radius: 50%;
  width: 50px;
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  z-index: 3;
  color: white;
}

.carousel-nav:hover {
  background: var(--primary-red);
}

.carousel-prev {
  left: 20px;
}

.carousel-next {
  right: 20px;
}

.carousel-dots {
  position: absolute;
  bottom: 30px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 10px;
  z-index: 3;
}

.dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.5);
  cursor: pointer;
  transition: background 0.3s ease;
}

.dot.active {
  background: white;
}

/* Rest of your CSS remains the same */
/* Features Bar */
.features-bar {
  background: var(--primary-white);
  padding: 40px 0;
  border-bottom: 1px solid var(--border-color);
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 2rem;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  text-align: left;
}

.feature-icon {
  flex-shrink: 0;
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f8f8f8;
  border-radius: 8px;
}

.feature-text h4 {
  color: var(--primary-black);
  font-size: 1.1rem;
  font-weight: 700;
  margin-bottom: 0.25rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.feature-text p {
  color: var(--gray-medium);
  margin: 0;
  font-size: 0.9rem;
  font-weight: 400;
}

/* Our Products Section */
.our-products {
  padding: 80px 0;
  background: var(--gray-light);
}

.section-header {
  text-align: center;
  margin-bottom: 3rem;
}

.section-title {
  font-size: 2.5rem;
  font-weight: 800;
  margin-bottom: 1rem;
  color: var(--primary-black);
  text-transform: uppercase;
  letter-spacing: 1px;
  position: relative;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 80px;
  height: 3px;
  background: var(--primary-red);
}

.section-subtitle {
  font-size: 1.1rem;
  color: var(--gray-medium);
  max-width: 600px;
  margin: 0 auto;
}

.products-tabs {
  margin-bottom: 2rem;
}

.tab-buttons {
  display: flex;
  justify-content: center;
  margin-bottom: 2rem;
  gap: 1rem;
}

.tab-button {
  background: transparent;
  border: 2px solid var(--border-color);
  padding: 0.75rem 1.5rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  cursor: pointer;
  transition: all 0.3s ease;
  border-radius: 4px;
}

.tab-button.active {
  background: var(--primary-red);
  border-color: var(--primary-red);
  color: white;
}

.tab-button:hover:not(.active) {
  background: var(--gray-light);
}

.tab-pane {
  display: none;
}

.tab-pane.active {
  display: block;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.view-all-container {
  text-align: center;
  margin-top: 2rem;
}

.view-all-link {
  display: inline-block;
  background: var(--primary-red);
  color: white;
  padding: 0.75rem 2rem;
  text-decoration: none;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-radius: 4px;
  transition: background 0.3s ease;
}

.view-all-link:hover {
  background: #c00;
}

/* Best Sellers */
.best-sellers {
  padding: 80px 0;
  background: white;
}

.products-scroller {
  position: relative;
  display: flex;
  align-items: center;
  gap: 1rem;
}

.scroll-btn {
  background: var(--primary-white);
  border: 2px solid var(--border-color);
  border-radius: 50%;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  flex-shrink: 0;
  z-index: 2;
}

.scroll-btn:hover {
  background: var(--primary-red);
  border-color: var(--primary-red);
  color: white;
}

.products-scroll-container {
  flex: 1;
  overflow: hidden;
}

.products-scroll {
  display: flex;
  gap: 1.5rem;
  padding: 1rem 0;
  transition: transform 0.3s ease;
}

.scroll-item {
  flex: 0 0 250px;
}

/* Testimonials */
.testimonials {
  padding: 80px 0;
  background: var(--gray-light);
}

.testimonials-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 2rem;
}

.testimonial-card {
  background: white;
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  position: relative;
}

.quote-icon {
  font-size: 4rem;
  color: var(--primary-red);
  line-height: 1;
  margin-bottom: 1rem;
  font-family: serif;
}

.testimonial-text {
  font-size: 1rem;
  line-height: 1.6;
  color: var(--gray-dark);
  margin-bottom: 1.5rem;
  font-style: italic;
}

.testimonial-author {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.author-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;
}

.author-info h4 {
  font-size: 1rem;
  font-weight: 600;
  margin-bottom: 0.25rem;
  color: var(--primary-black);
}

.author-info p {
  font-size: 0.9rem;
  color: var(--gray-medium);
  margin: 0;
}

/* Brands Section */
.brands-section {
  padding: 60px 0;
  background: white;
}

.brands-scroller {
  overflow: hidden;
  position: relative;
}

.brands-track {
  display: flex;
  gap: 3rem;
  animation: scrollBrands 30s linear infinite;
}

.brand-logo {
  padding: 1.5rem 2.5rem;
  background: var(--gray-light);
  border-radius: 8px;
  font-weight: 800;
  color: var(--primary-black);
  text-transform: uppercase;
  letter-spacing: 1px;
  flex-shrink: 0;
  font-size: 1.1rem;
  transition: all 0.3s ease;
}

.brand-logo:hover {
  background: var(--primary-red);
  color: white;
}

@keyframes scrollBrands {
  0% {
    transform: translateX(0);
  }
  100% {
    transform: translateX(-50%);
  }
}

/* Responsive Design */
@media (max-width: 1024px) {
  .hero-carousel {
    height: 500px;
  }
  
  .slide-title {
    font-size: 2.5rem;
  }
  
  .products-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .hero-carousel {
    height: 400px;
  }

  .slide-content {
    padding-left: 20px;
    padding-right: 20px;
    text-align: center;
    max-width: 100%;
  }

  .slide-title {
    font-size: 2rem;
  }

  .slide-description {
    font-size: 1.1rem;
  }

  .slide-buttons {
    flex-direction: column;
    align-items: center;
  }

  .carousel-nav {
    width: 40px;
    height: 40px;
  }

  .carousel-prev {
    left: 10px;
  }

  .carousel-next {
    right: 10px;
  }

  .features-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }

  .feature-item {
    text-align: center;
    flex-direction: column;
    gap: 1rem;
  }

  .tab-buttons {
    flex-direction: column;
    align-items: center;
  }

  .products-grid {
    grid-template-columns: 1fr;
  }

  .testimonials-grid {
    grid-template-columns: 1fr;
  }

  .section-title {
    font-size: 2rem;
  }

  .scroll-btn {
    display: none;
  }

  .products-scroll {
    overflow-x: auto;
    padding-bottom: 1rem;
  }

  .scroll-item {
    flex: 0 0 250px;
  }
}

@media (max-width: 480px) {
  .slide-title {
    font-size: 1.8rem;
  }

  .brand-logo {
    padding: 1rem 1.5rem;
    font-size: 0.9rem;
  }

  .testimonial-card {
    padding: 1.5rem;
  }
  
  .container {
    padding-left: 10px;
    padding-right: 10px;
  }
}

/* Reduced side padding for containers */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding-left: 15px;
  padding-right: 15px;
}
</style>