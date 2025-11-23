<!-- components/frontend/Navbar.vue -->
<template>
  <header class="header">
    <!-- Top Announcement Bar -->
    <div class="announcement-bar">
      <div class="container">
        <p>🚚 FREE DELIVERY ON ORDERS OVER £50 | 📞 0800 298 9230</p>
      </div>
    </div>

    <!-- Main Header -->
    <div class="main-header">
      <div class="container">
        <div class="header-content">
          <!-- Logo -->
          <router-link to="/" class="logo">
            <div class="logo-container">
              <span class="logo-text">PINDERS</span>
              <span class="logo-subtitle">WORKWEAR</span>
            </div>
          </router-link>

          <!-- Search Bar -->
          <div class="search-container">
            <form class="search-form" @submit.prevent="performSearch">
              <input 
                type="text" 
                v-model="searchQuery" 
                placeholder="Search for products..." 
                class="search-input"
              >
              <button type="submit" class="search-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M21 21L16.514 16.506L21 21ZM19 10.5C19 15.194 15.194 19 10.5 19C5.806 19 2 15.194 2 10.5C2 5.806 5.806 2 10.5 2C15.194 2 19 5.806 19 10.5Z" 
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </button>
            </form>
          </div>

          <!-- Header Icons -->
          <div class="header-icons">
            <!-- Account -->
            <div class="icon-dropdown">
              <button class="icon-btn">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M20 21V19C20 16.7909 18.2091 15 16 15H8C5.79086 15 4 16.7909 4 19V21" 
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                </svg>
                <span class="icon-text">Account</span>
              </button>
              <div class="dropdown-menu account-menu">
                <template v-if="!isAuthenticated">
                  <h4>Welcome</h4>
                  <router-link to="/login" class="dropdown-item">Sign In</router-link>
                  <router-link to="/signup" class="dropdown-item">Create Account</router-link>
                </template>
                <template v-else>
                  <h4>Hello, {{ userFirstName }}</h4>
                  <router-link to="/account" class="dropdown-item">My Account</router-link>
                  <router-link to="/orders" class="dropdown-item">My Orders</router-link>
                  <router-link to="/wishlist" class="dropdown-item">My Wishlist</router-link>
                  <a href="#" class="dropdown-item" @click.prevent="logout">Sign Out</a>
                </template>
              </div>
            </div>

            <!-- Wishlist -->
            <router-link to="/wishlist" class="icon-btn">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19.5 12.572L12 20L4.5 12.572C3.605 11.677 3 10.391 3 9C3 6.791 4.791 5 7 5C8.309 5 9.5 5.671 10.214 6.714L12 9L13.786 6.714C14.5 5.671 15.691 5 17 5C19.209 5 21 6.791 21 9C21 10.391 20.395 11.677 19.5 12.572Z" 
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <span class="icon-text">Wishlist</span>
            </router-link>

            <!-- Cart -->
  <div class="icon-dropdown cart-dropdown" @mouseenter="showCart = true" @mouseleave="hideCartDelay">
    <router-link to="/cart" class="icon-btn cart-btn">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M3 3H5L5.4 5M7 13H17L21 5H5.4M7 13L5.4 5M7 13L4.7 15.3C4.3 15.7 4.6 16.4 5.2 16.4H17M17 13V16.4M9 19C9 19.6 8.6 20 8 20C7.4 20 7 19.6 7 19C7 18.4 7.4 18 8 18C8.6 18 9 18.4 9 19ZM17 19C17 19.6 16.6 20 16 20C15.4 20 15 19.6 15 19C15 18.4 15.4 18 16 18C16.6 18 17 18.4 17 19Z" 
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      <span class="icon-text">Cart</span>
      <span class="cart-count" v-if="cartTotal > 0">{{ cartTotal }}</span>
    </router-link>
    
    <!-- Cart Dropdown -->
    <div class="dropdown-menu cart-dropdown-menu" :class="{ 'show': showCart }" @mouseenter="showCart = true" @mouseleave="hideCartDelay">
      <div class="cart-dropdown-container">
        <div class="cart-dropdown-content">
          <!-- Cart Header -->
          <div class="cart-header">
            <h4>Your Cart ({{ cartTotal }})</h4>
          </div>

          <!-- Cart Items -->
          <div v-if="cartItems.length > 0" class="cart-content">
            <div class="cart-items-scroll">
              <div v-for="item in cartItems" :key="`${item.id}-${item.variant_id}`" class="cart-item">
                <img :src="getProductImage(item)" :alt="item.name" class="item-image">
                <div class="item-details">
                  <h5 class="item-title">{{ item.name }}</h5>
                  <div class="item-meta">
                    <span class="item-price">£{{ item.price }}</span>
                    <span class="item-quantity">× {{ item.quantity }}</span>
                  </div>
                  <span class="item-total">£{{ (item.price * item.quantity).toFixed(2) }}</span>
                </div>
              </div>
            </div>

            <!-- Cart Footer -->
            <div class="cart-footer">
              <div class="cart-summary">
                <div class="subtotal">
                  <span>Subtotal:</span>
                  <span class="amount">£{{ cartSubtotal }}</span>
                </div>
                <p class="shipping-note">Free shipping on orders over £50</p>
              </div>
              
              <div class="cart-actions">
                <router-link to="/cart" class="btn btn-view-cart" @click="hideCart">
                  View Cart
                </router-link>
                <router-link to="/checkout" class="btn btn-checkout" @click="hideCart">
                  Checkout
                </router-link>
              </div>
            </div>
          </div>

          <!-- Empty Cart -->
          <div v-else class="empty-cart-state">
            <div class="empty-icon">🛒</div>
            <p class="empty-text">Your cart is empty</p>
            <router-link to="/products" class="btn btn-start-shopping" @click="hideCart">
              Start Shopping
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
            
          </div>
        </div>
      </div>
    </div>

    <!-- Main Navigation -->
    <nav class="main-navigation">
      <div class="container">
        <ul class="nav-menu">
          <li class="nav-item">
            <router-link to="/" class="nav-link">Home</router-link>
          </li>
          
          <!-- Workwear Mega Menu -->
          <li class="nav-item mega-menu">
            <a href="#" class="nav-link">Workwear ▾</a>
            <div class="mega-menu-content">
              <div class="mega-menu-column">
                <h4>Men's Workwear</h4>
                <router-link to="/workwear/mens/work-jackets">Work Jackets</router-link>
                <router-link to="/workwear/mens/work-trousers">Work Trousers</router-link>
                <router-link to="/workwear/mens/coveralls">Coveralls</router-link>
                <router-link to="/workwear/mens/hi-vis">Hi-Vis Clothing</router-link>
                <router-link to="/workwear/mens/fleeces">Fleeces & Sweatshirts</router-link>
              </div>
              <div class="mega-menu-column">
                <h4>Women's Workwear</h4>
                <router-link to="/workwear/womens/work-jackets">Work Jackets</router-link>
                <router-link to="/workwear/womens/work-trousers">Work Trousers</router-link>
                <router-link to="/workwear/womens/hi-vis">Hi-Vis Clothing</router-link>
                <router-link to="/workwear/womens/fleeces">Fleeces & Sweatshirts</router-link>
              </div>
              <div class="mega-menu-column">
                <h4>Corporate Wear</h4>
                <router-link to="/workwear/corporate/shirts">Shirts</router-link>
                <router-link to="/workwear/corporate/trousers">Trousers</router-link>
                <router-link to="/workwear/corporate/polos">Polo Shirts</router-link>
                <router-link to="/workwear/corporate/footwear">Corporate Footwear</router-link>
              </div>
              <div class="mega-menu-column">
                <h4>Featured Brands</h4>
                <router-link to="/brands/dickies">Dickies</router-link>
                <router-link to="/brands/snickers">Snickers</router-link>
                <router-link to="/brands/portwest">Portwest</router-link>
                <router-link to="/brands/helly-hansen">Helly Hansen</router-link>
                <router-link to="/brands/regatta">Regatta</router-link>
              </div>
            </div>
          </li>

          <!-- Safetywear Mega Menu -->
          <li class="nav-item mega-menu">
            <a href="#" class="nav-link">Safetywear ▾</a>
            <div class="mega-menu-content">
              <div class="mega-menu-column">
                <h4>Head Protection</h4>
                <router-link to="/safetywear/head/safety-helmets">Safety Helmets</router-link>
                <router-link to="/safetywear/head/bump-caps">Bump Caps</router-link>
                <router-link to="/safetywear/head/accessories">Accessories</router-link>
              </div>
              <div class="mega-menu-column">
                <h4>Eye & Face Protection</h4>
                <router-link to="/safetywear/eye/safety-glasses">Safety Glasses</router-link>
                <router-link to="/safetywear/eye/goggles">Goggles</router-link>
                <router-link to="/safetywear/eye/face-shields">Face Shields</router-link>
              </div>
              <div class="mega-menu-column">
                <h4>Hearing Protection</h4>
                <router-link to="/safetywear/hearing/ear-defenders">Ear Defenders</router-link>
                <router-link to="/safetywear/hearing/ear-plugs">Ear Plugs</router-link>
              </div>
              <div class="mega-menu-column">
                <h4>Respiratory Protection</h4>
                <router-link to="/safetywear/respiratory/masks">Face Masks</router-link>
                <router-link to="/safetywear/respiratory/respirators">Respirators</router-link>
              </div>
            </div>
          </li>

          <!-- Footwear Mega Menu -->
          <li class="nav-item mega-menu">
            <a href="#" class="nav-link">Footwear ▾</a>
            <div class="mega-menu-content">
              <div class="mega-menu-column">
                <h4>Safety Boots</h4>
                <router-link to="/footwear/safety-boots/steel-toe">Steel Toe</router-link>
                <router-link to="/footwear/safety-boots/composite-toe">Composite Toe</router-link>
                <router-link to="/footwear/safety-boots/waterproof">Waterproof</router-link>
                <router-link to="/footwear/safety-boots/anti-slip">Anti-Slip</router-link>
              </div>
              <div class="mega-menu-column">
                <h4>Work Shoes</h4>
                <router-link to="/footwear/work-shoes/safety-shoes">Safety Shoes</router-link>
                <router-link to="/footwear/work-shoes/trainers">Work Trainers</router-link>
                <router-link to="/footwear/work-shoes/casual">Casual Work Shoes</router-link>
              </div>
              <div class="mega-menu-column">
                <h4>Wellies & Waders</h4>
                <router-link to="/footwear/wellies/safety-wellies">Safety Wellies</router-link>
                <router-link to="/footwear/wellies/standard">Standard Wellies</router-link>
                <router-link to="/footwear/wellies/waders">Waders</router-link>
              </div>
              <div class="mega-menu-column">
                <h4>Featured Brands</h4>
                <router-link to="/footwear/brands/dickies">Dickies</router-link>
                <router-link to="/footwear/brands/skechers">Skechers</router-link>
                <router-link to="/footwear/brands/cat">CAT</router-link>
                <router-link to="/footwear/brands/dewalt">DeWalt</router-link>
              </div>
            </div>
          </li>

          <li class="nav-item">
            <router-link to="/ppe" class="nav-link">PPE</router-link>
          </li>
          
          <li class="nav-item">
            <router-link to="/brands" class="nav-link">Brands</router-link>
          </li>
          
          <li class="nav-item">
            <router-link to="/clearance" class="nav-link sale">Clearance</router-link>
          </li>
          
          <li class="nav-item">
            <router-link to="/corporate" class="nav-link">Corporate</router-link>
          </li>
        </ul>
      </div>
    </nav>

    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" @click="toggleMobileMenu">
      <span></span>
      <span></span>
      <span></span>
    </button>

    <!-- Mobile Menu -->
    <div class="mobile-menu" :class="{ active: showMobileMenu }">
      <div class="mobile-menu-header">
        <router-link to="/" class="logo" @click="closeMobileMenu">
          <span class="logo-text">PINDERS</span>
        </router-link>
        <button class="close-menu" @click="closeMobileMenu">×</button>
      </div>
      
      <div class="mobile-search">
        <input type="text" placeholder="Search products..." v-model="mobileSearchQuery">
        <button @click="performMobileSearch">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path d="M21 21L16.514 16.506L21 21ZM19 10.5C19 15.194 15.194 19 10.5 19C5.806 19 2 15.194 2 10.5C2 5.806 5.806 2 10.5 2C15.194 2 19 5.806 19 10.5Z" 
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
      </div>

      <ul class="mobile-nav">
        <li><router-link to="/" @click="closeMobileMenu">Home</router-link></li>
        
        <li class="mobile-dropdown">
          <a href="#" @click.prevent="toggleMobileDropdown('workwear')">Workwear ▸</a>
          <ul class="mobile-submenu" v-if="mobileDropdowns.workwear">
            <li><h5>Men's Workwear</h5></li>
            <li><router-link to="/workwear/mens/work-jackets" @click="closeMobileMenu">Work Jackets</router-link></li>
            <li><router-link to="/workwear/mens/work-trousers" @click="closeMobileMenu">Work Trousers</router-link></li>
            <li><h5>Women's Workwear</h5></li>
            <li><router-link to="/workwear/womens/work-jackets" @click="closeMobileMenu">Work Jackets</router-link></li>
            <li><router-link to="/workwear/womens/work-trousers" @click="closeMobileMenu">Work Trousers</router-link></li>
          </ul>
        </li>

        <li><router-link to="/ppe" @click="closeMobileMenu">PPE</router-link></li>
        <li><router-link to="/brands" @click="closeMobileMenu">Brands</router-link></li>
        <li><router-link to="/clearance" @click="closeMobileMenu" class="sale">Clearance</router-link></li>
        
        <template v-if="!isAuthenticated">
          <li><router-link to="/login" @click="closeMobileMenu">Sign In</router-link></li>
          <li><router-link to="/signup" @click="closeMobileMenu">Create Account</router-link></li>
        </template>
        <template v-else>
          <li><router-link to="/account" @click="closeMobileMenu">My Account</router-link></li>
          <li><a href="#" @click="logout">Sign Out</a></li>
        </template>
      </ul>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/store/auth'
import { useCartStore } from '@/store/cart'

const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()

const searchQuery = ref('')
const mobileSearchQuery = ref('')
const showMobileMenu = ref(false)
const showCart = ref(false) // Add this for cart dropdown control
const hideCartTimeout = ref(null)
const mobileDropdowns = ref({
  workwear: false,
  safetywear: false,
  footwear: false
})

// Computed properties
const isAuthenticated = computed(() => authStore.isAuthenticated)
const userFirstName = computed(() => authStore.user?.name?.split(' ')[0] || 'User')
const cartTotal = computed(() => cartStore.totalItems)
const cartItems = computed(() => cartStore.items.slice(0, 3))
const cartSubtotal = computed(() => cartStore.subtotal.toFixed(2))

// Cart dropdown methods
const hideCart = () => {
  showCart.value = false
}

const hideCartDelay = () => {
  // Small delay to allow moving to dropdown
  hideCartTimeout.value = setTimeout(() => {
    showCart.value = false
  }, 300)
}

const clearHideCartTimeout = () => {
  if (hideCartTimeout.value) {
    clearTimeout(hideCartTimeout.value)
    hideCartTimeout.value = null
  }
}

// Other methods remain the same...
const getProductImage = (item) => {
  return item.image || 'https://images.unsplash.com/photo-1558769132-cb1aedeedd98?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=100&q=80'
}

const performSearch = () => {
  if (searchQuery.value.trim()) {
    router.push(`/products?search=${encodeURIComponent(searchQuery.value)}`)
    searchQuery.value = ''
  }
}

const performMobileSearch = () => {
  if (mobileSearchQuery.value.trim()) {
    router.push(`/products?search=${encodeURIComponent(mobileSearchQuery.value)}`)
    mobileSearchQuery.value = ''
    closeMobileMenu()
  }
}

const toggleMobileMenu = () => {
  showMobileMenu.value = !showMobileMenu.value
}

const closeMobileMenu = () => {
  showMobileMenu.value = false
  Object.keys(mobileDropdowns.value).forEach(key => {
    mobileDropdowns.value[key] = false
  })
}

const toggleMobileDropdown = (dropdown) => {
  mobileDropdowns.value[dropdown] = !mobileDropdowns.value[dropdown]
}

const logout = () => {
  authStore.logout()
  closeMobileMenu()
  router.push('/')
}

// Initialize
onMounted(() => {
  cartStore.hydrate()
})

// Cleanup
onUnmounted(() => {
  if (hideCartTimeout.value) {
    clearTimeout(hideCartTimeout.value)
  }
})
</script>


<style scoped>
/* Announcement Bar */
.announcement-bar {
  background: var(--primary-black);
  color: var(--primary-white);
  padding: 8px 0;
  font-size: 13px;
  text-align: center;
  font-weight: 500;
}

/* Main Header */
.main-header {
  padding: 20px 0;
  border-bottom: 1px solid var(--border-color);
}

.header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

/* Logo */
.logo {
  text-decoration: none;
  color: var(--primary-black);
}

.logo-container {
  text-align: center;
}

.logo-text {
  font-size: 32px;
  font-weight: 800;
  color: var(--primary-red);
  display: block;
  line-height: 1;
}

.logo-subtitle {
  font-size: 14px;
  font-weight: 600;
  color: var(--primary-black);
  text-transform: uppercase;
  letter-spacing: 1px;
}

/* Search */
.search-container {
  flex: 1;
  max-width: 500px;
  margin: 0 40px;
}

.search-form {
  display: flex;
  position: relative;
}

.search-input {
  flex: 1;
  padding: 12px 20px;
  border: 2px solid var(--border-color);
  border-right: none;
  font-size: 14px;
  font-family: 'Poppins', sans-serif;
}

.search-input:focus {
  outline: none;
  border-color: var(--primary-red);
}

.search-btn {
  background: var(--primary-red);
  border: 2px solid var(--primary-red);
  color: white;
  padding: 0 20px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.search-btn:hover {
  background: #c1121f;
  border-color: #c1121f;
}

/* Header Icons */
.header-icons {
  display: flex;
  gap: 20px;
}

.icon-dropdown {
  position: relative;
}

.icon-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  background: none;
  border: none;
  color: var(--primary-black);
  text-decoration: none;
  cursor: pointer;
  padding: 5px 10px;
  transition: color 0.3s ease;
  font-size: 12px;
  font-weight: 500;
}

.icon-btn:hover {
  color: var(--primary-red);
}

.icon-text {
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.cart-count {
  position: absolute;
  top: -5px;
  right: 0;
  background: var(--primary-red);
  color: white;
  border-radius: 50%;
  width: 18px;
  height: 18px;
  font-size: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
}

/* Dropdown Menus */
.dropdown-menu {
  position: absolute;
  top: 100%;
  right: 0;
  background: white;
  border: 1px solid var(--border-color);
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  min-width: 250px;
  z-index: 1000;
  opacity: 0;
  visibility: hidden;
  transform: translateY(10px);
  transition: all 0.3s ease;
}

.icon-dropdown:hover .dropdown-menu {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.account-menu {
  padding: 20px;
}

.account-menu h4 {
  margin-bottom: 15px;
  color: var(--primary-black);
  font-size: 16px;
}

.dropdown-item {
  display: block;
  padding: 8px 0;
  color: var(--primary-black);
  text-decoration: none;
  border-bottom: 1px solid var(--border-color);
  transition: color 0.3s ease;
  font-size: 14px;
}

.dropdown-item:hover {
  color: var(--primary-red);
}

.dropdown-item:last-child {
  border-bottom: none;
}

/* Cart Menu */
.cart-menu {
  padding: 20px;
  min-width: 320px;
}

.cart-items {
  max-height: 200px;
  overflow-y: auto;
  margin-bottom: 15px;
}

.cart-item {
  display: flex;
  gap: 15px;
  padding: 10px 0;
  border-bottom: 1px solid var(--border-color);
}

.item-image {
  width: 60px;
  height: 60px;
  object-fit: cover;
}

.item-details h5 {
  font-size: 14px;
  margin-bottom: 5px;
  font-weight: 600;
}

.item-details p {
  font-size: 12px;
  color: var(--gray-medium);
  margin-bottom: 5px;
}

.item-price {
  font-weight: 600;
  color: var(--primary-red);
}

.cart-footer {
  border-top: 1px solid var(--border-color);
  padding-top: 15px;
}

.cart-total {
  text-align: center;
  margin-bottom: 15px;
  font-size: 16px;
}

.cart-actions {
  display: flex;
  gap: 10px;
}

.cart-actions .btn {
  flex: 1;
  padding: 10px;
  font-size: 12px;
}

.empty-cart {
  text-align: center;
  padding: 30px;
  color: var(--gray-medium);
  font-style: italic;
}

/* Main Navigation */
.main-navigation {
  background: var(--primary-black);
  position: relative;
}

.nav-menu {
  display: flex;
  list-style: none;
  margin: 0;
  padding: 0;
}

.nav-item {
  position: relative;
}

.nav-link {
  display: block;
  padding: 15px 20px;
  color: var(--primary-white);
  text-decoration: none;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 14px;
  letter-spacing: 0.5px;
  transition: background-color 0.3s ease;
}

.nav-link:hover,
.nav-item:hover .nav-link {
  background: var(--primary-red);
  color: white;
}

.nav-link.sale {
  color: var(--primary-red);
  font-weight: 700;
}

/* Mega Menu */
.mega-menu-content {
  position: absolute;
  left: 0;
  top: 100%;
  background: white;
  border: 1px solid var(--border-color);
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  display: flex;
  width: 100%;
  min-width: 800px;
  z-index: 1000;
  opacity: 0;
  visibility: hidden;
  transform: translateY(10px);
  transition: all 0.3s ease;
}

.mega-menu:hover .mega-menu-content {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.mega-menu-column {
  flex: 1;
  padding: 30px;
  border-right: 1px solid var(--border-color);
}

.mega-menu-column:last-child {
  border-right: none;
}

.mega-menu-column h4 {
  color: var(--primary-red);
  margin-bottom: 15px;
  font-size: 16px;
  font-weight: 600;
}

.mega-menu-column a {
  display: block;
  padding: 8px 0;
  color: var(--primary-black);
  text-decoration: none;
  font-size: 14px;
  transition: color 0.3s ease;
  border-bottom: 1px solid var(--border-color);
}

.mega-menu-column a:hover {
  color: var(--primary-red);
}

.mega-menu-column a:last-child {
  border-bottom: none;
}

/* Mobile Menu Toggle */
.mobile-menu-toggle {
  display: none;
  flex-direction: column;
  background: none;
  border: none;
  cursor: pointer;
  padding: 10px;
  gap: 4px;
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
}

.mobile-menu-toggle span {
  width: 25px;
  height: 3px;
  background: var(--primary-black);
  transition: 0.3s;
}

/* Mobile Menu */
.mobile-menu {
  position: fixed;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: white;
  z-index: 1001;
  transition: left 0.3s ease;
  overflow-y: auto;
}

.mobile-menu.active {
  left: 0;
}

.mobile-menu-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid var(--border-color);
  background: var(--primary-black);
}

.mobile-menu-header .logo-text {
  color: var(--primary-white);
  font-size: 24px;
  font-weight: 800;
}

.close-menu {
  background: none;
  border: none;
  color: white;
  font-size: 30px;
  cursor: pointer;
  padding: 0;
  width: 40px;
  height: 40px;
}

.mobile-search {
  display: flex;
  padding: 20px;
  border-bottom: 1px solid var(--border-color);
}

.mobile-search input {
  flex: 1;
  padding: 12px;
  border: 1px solid var(--border-color);
  border-right: none;
  font-family: 'Poppins', sans-serif;
}

.mobile-search button {
  background: var(--primary-red);
  border: 1px solid var(--primary-red);
  color: white;
  padding: 0 15px;
  cursor: pointer;
}

.mobile-nav {
  list-style: none;
  padding: 0;
  margin: 0;
}

.mobile-nav li {
  border-bottom: 1px solid var(--border-color);
}

.mobile-nav a {
  display: block;
  padding: 15px 20px;
  color: var(--primary-black);
  text-decoration: none;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 14px;
}

.mobile-nav a.sale {
  color: var(--primary-red);
}

.mobile-nav a:hover {
  background: var(--gray-light);
}

.mobile-dropdown h5 {
  padding: 15px 20px;
  margin: 0;
  background: var(--gray-light);
  color: var(--primary-black);
  font-size: 14px;
  font-weight: 600;
}

.mobile-submenu {
  list-style: none;
  padding: 0;
  margin: 0;
  background: var(--gray-light);
}

.mobile-submenu a {
  padding-left: 40px;
  font-weight: 500;
  text-transform: none;
}

/* Responsive */
@media (max-width: 1024px) {
  .search-container {
    margin: 0 20px;
    max-width: 400px;
  }
  
  .mega-menu-content {
    min-width: 700px;
  }
}

@media (max-width: 768px) {
  .announcement-bar p {
    font-size: 11px;
  }

  .header-content {
    flex-wrap: wrap;
  }

  .logo {
    order: 1;
  }

  .header-icons {
    order: 2;
  }

  .search-container {
    order: 3;
    margin: 15px 0 0;
    max-width: 100%;
  }

  .main-navigation {
    display: none;
  }

  .mobile-menu-toggle {
    display: flex;
  }

  .icon-text {
    display: none;
  }

  .header-icons {
    gap: 10px;
  }
}

/* //cart special */
.cart-dropdown {
  position: relative;
}

.cart-dropdown-menu {
  position: absolute;
  top: 100%;
  right: 0;
  background: white;
  border: 1px solid var(--border-color);
  box-shadow: 0 20px 40px rgba(0,0,0,0.15);
  min-width: 380px;
  max-width: 400px;
  z-index: 9999;
  opacity: 0;
  visibility: hidden;
  transform: translateY(15px);
  transition: all 0.3s ease;
  border-radius: 12px;
  overflow: hidden;
  pointer-events: none;
}

.cart-dropdown-menu.show {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
  pointer-events: all;
}

.cart-dropdown-container {
  max-height: 480px;
  display: flex;
  flex-direction: column;
}

.cart-dropdown-content {
  display: flex;
  flex-direction: column;
  height: 100%;
}

/* Cart Header */
.cart-header {
  padding: 20px;
  border-bottom: 1px solid var(--border-color);
  background: #fafafa;
}

.cart-header h4 {
  margin: 0;
  color: var(--primary-black);
  font-size: 16px;
  font-weight: 700;
  text-align: center;
}

/* Cart Content */
.cart-content {
  display: flex;
  flex-direction: column;
  flex: 1;
  min-height: 0;
}

/* Cart Items Scroll Area */
.cart-items-scroll {
  flex: 1;
  overflow-y: auto;
  max-height: 240px;
  padding: 0 20px;
}

.cart-item {
  display: flex;
  gap: 12px;
  padding: 16px 0;
  border-bottom: 1px solid #f0f0f0;
}

.cart-item:last-child {
  border-bottom: none;
}

.item-image {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 8px;
  flex-shrink: 0;
  background: #f8f9fa;
  border: 1px solid #eee;
}

.item-details {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.item-title {
  font-size: 14px;
  font-weight: 600;
  line-height: 1.3;
  color: var(--primary-black);
  margin: 0 0 8px 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.item-meta {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 6px;
}

.item-price {
  font-size: 13px;
  color: var(--gray-medium);
  font-weight: 500;
}

.item-quantity {
  font-size: 13px;
  color: var(--gray-medium);
}

.item-total {
  font-weight: 700;
  color: var(--primary-red);
  font-size: 14px;
  align-self: flex-start;
}

/* Cart Footer */
.cart-footer {
  padding: 20px;
  border-top: 1px solid var(--border-color);
  background: #fafafa;
  margin-top: auto;
}

.cart-summary {
  margin-bottom: 16px;
}

.subtotal {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
  font-weight: 600;
  font-size: 15px;
}

.amount {
  color: var(--primary-red);
  font-size: 16px;
}

.shipping-note {
  font-size: 12px;
  color: var(--gray-medium);
  text-align: center;
  margin: 0;
  font-style: italic;
}

.cart-actions {
  display: flex;
  gap: 10px;
}

.cart-actions .btn {
  flex: 1;
  padding: 12px 16px;
  font-size: 13px;
  font-weight: 600;
  text-align: center;
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.3s ease;
  border: 2px solid transparent;
  cursor: pointer;
}

.btn-view-cart {
  background: white;
  color: var(--primary-black);
  border-color: var(--border-color) !important;
}

.btn-view-cart:hover {
  background: var(--gray-light);
  border-color: var(--gray-medium) !important;
}

.btn-checkout {
  background: var(--primary-red);
  color: white;
  border-color: var(--primary-red) !important;
}

.btn-checkout:hover {
  background: #c00;
  border-color: #c00 !important;
}

/* Empty Cart State */
.empty-cart-state {
  padding: 40px 30px;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 200px;
}

.empty-icon {
  font-size: 3.5rem;
  margin-bottom: 16px;
  opacity: 0.3;
}

.empty-text {
  color: var(--gray-medium);
  margin-bottom: 20px;
  font-size: 14px;
  font-style: italic;
}

.btn-start-shopping {
  display: inline-block;
  padding: 12px 24px;
  background: var(--primary-red);
  color: white;
  text-decoration: none;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  transition: background 0.3s ease;
  border: 2px solid var(--primary-red);
}

.btn-start-shopping:hover {
  background: #c00;
  border-color: #c00;
}

/* Cart count badge */
.cart-count {
  position: absolute;
  top: -5px;
  right: 0;
  background: var(--primary-red);
  color: white;
  border-radius: 50%;
  width: 18px;
  height: 18px;
  font-size: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
}

/* Cart button */
.cart-btn {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  background: none;
  border: none;
  color: var(--primary-black);
  text-decoration: none;
  cursor: pointer;
  padding: 5px 10px;
  transition: color 0.3s ease;
  font-size: 12px;
  font-weight: 500;
}

.cart-btn:hover {
  color: var(--primary-red);
}

/* Scrollbar Styling */
.cart-items-scroll::-webkit-scrollbar {
  width: 4px;
}

.cart-items-scroll::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 2px;
}

.cart-items-scroll::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 2px;
}

.cart-items-scroll::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

/* Responsive Design */
@media (max-width: 768px) {
  .cart-dropdown-menu {
    position: fixed;
    top: auto;
    bottom: 0;
    left: 0;
    right: 0;
    min-width: auto;
    max-width: none;
    border-radius: 16px 16px 0 0;
    transform: translateY(100%);
    max-height: 70vh;
  }
  
  .cart-dropdown-menu.show {
    transform: translateY(0);
  }

  .cart-dropdown-container {
    max-height: 70vh;
  }

  .cart-items-scroll {
    max-height: 40vh;
  }

  .cart-actions {
    flex-direction: column;
  }

  .cart-actions .btn {
    flex: none;
  }
}

@media (max-width: 480px) {
  .cart-dropdown-menu {
    min-width: auto;
    width: 95%;
    left: 2.5%;
    right: 2.5%;
  }
}
</style>