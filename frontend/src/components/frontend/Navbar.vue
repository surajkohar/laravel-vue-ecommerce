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
              <input type="text" v-model="searchQuery" placeholder="Search for products..." class="search-input">
              <button type="submit" class="search-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M21 21L16.514 16.506L21 21ZM19 10.5C19 15.194 15.194 19 10.5 19C5.806 19 2 15.194 2 10.5C2 5.806 5.806 2 10.5 2C15.194 2 19 5.806 19 10.5Z"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </button>
            </form>
          </div>

          <!-- Header Icons -->
          <div class="header-icons">
            <!-- Account Dropdown -->
            <div class="icon-dropdown account-dropdown" @mouseenter="showProfileDropdown = true"
              @mouseleave="hideProfileDropdown">
              <button class="icon-btn">
                <div class="icon-wrapper">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <path d="M20 21V19C20 16.7909 18.2091 15 16 15H8C5.79086 15 4 16.7909 4 19V21" stroke="currentColor"
                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" />
                  </svg>
                </div>
                <span class="icon-text">Account {{ authStore.user.name }}</span>
              </button>

              <!-- Profile Dropdown Menu -->
              <div class="dropdown-menu account-menu" v-show="showProfileDropdown" @mouseenter="keepProfileDropdownOpen"
                @mouseleave="hideProfileDropdown">
                <template v-if="!isAuthenticated">
                  <div class="dropdown-header">
                    <h4>Welcome</h4>
                    <p>Sign in to your account</p>
                  </div>
                  <div class="dropdown-section">
                    <router-link to="/login" class="dropdown-item" @click="hideProfileDropdown">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path
                          d="M15 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H15"
                          stroke="currentColor" stroke-width="2" />
                        <path d="M10 17L15 12L10 7" stroke="currentColor" stroke-width="2" />
                        <path d="M15 12H3" stroke="currentColor" stroke-width="2" />
                      </svg>
                      Sign In
                    </router-link>
                    <router-link to="/register" class="dropdown-item" @click="hideProfileDropdown">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M16 21V19C16 16.7909 14.2091 15 12 15H5C2.79086 15 1 16.7909 1 19V21"
                          stroke="currentColor" stroke-width="2" />
                        <circle cx="8.5" cy="7" r="4" stroke="currentColor" stroke-width="2" />
                        <path d="M20 8V14M23 11H17" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                      </svg>
                      Create Account
                    </router-link>
                  </div>
                </template>

                <template v-else>
                  <div class="dropdown-header">
                    <div class="user-info">
                      <img :src="authStore.user?.profile_image || '/default-avatar.png'" :alt="authStore.user?.name"
                        class="user-avatar" />
                      <div class="user-details">
                        <h4>Hello, {{ userFirstName }}</h4>
                        <p>{{ userEmail }}</p>
                      </div>
                    </div>
                  </div>

                  <div class="dropdown-section">
                    <router-link to="/account" class="dropdown-item" @click="hideProfileDropdown">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path
                          d="M12 12C14.2091 12 16 10.2091 16 8C16 5.79086 14.2091 4 12 4C9.79086 4 8 5.79086 8 8C8 10.2091 9.79086 12 12 12Z"
                          stroke="currentColor" stroke-width="2" />
                        <path d="M20 21V19C20 16.7909 18.2091 15 16 15H8C5.79086 15 4 16.7909 4 19V21"
                          stroke="currentColor" stroke-width="2" />
                      </svg>
                      My Profile
                    </router-link>
                    <router-link to="/orders" class="dropdown-item" @click="hideProfileDropdown">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path
                          d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z"
                          stroke="currentColor" stroke-width="2" />
                        <path d="M9 22V12H15V22" stroke="currentColor" stroke-width="2" />
                      </svg>
                      My Orders
                    </router-link>
                    <router-link to="/wishlist" class="dropdown-item" @click="hideProfileDropdown">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path
                          d="M19.5 12.572L12 20L4.5 12.572C3.605 11.677 3 10.391 3 9C3 6.791 4.791 5 7 5C8.309 5 9.5 5.671 10.214 6.714L12 9L13.786 6.714C14.5 5.671 15.691 5 17 5C19.209 5 21 6.791 21 9C21 10.391 20.395 11.677 19.5 12.572Z"
                          stroke="currentColor" stroke-width="2" />
                      </svg>
                      My Wishlist
                      <span class="wishlist-count" v-if="wishlistStore.totalItems > 0">
                        {{ wishlistStore.totalItems }}
                      </span>
                    </router-link>
                    <router-link to="/addresses" class="dropdown-item" @click="hideProfileDropdown">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path
                          d="M21 10C21 17 12 23 12 23C12 23 3 17 3 10C3 7.61305 3.94821 5.32387 5.63604 3.63604C7.32387 1.94821 9.61305 1 12 1C14.3869 1 16.6761 1.94821 18.364 3.63604C20.0518 5.32387 21 7.61305 21 10Z"
                          stroke="currentColor" stroke-width="2" />
                        <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2" />
                      </svg>
                      My Addresses
                    </router-link>
                  </div>

                  <div class="dropdown-divider"></div>

                  <div class="dropdown-section">
                    <a href="#" class="dropdown-item logout-item" @click.prevent="logout">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path
                          d="M9 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H9"
                          stroke="currentColor" stroke-width="2" />
                        <path d="M16 17L21 12L16 7" stroke="currentColor" stroke-width="2" />
                        <path d="M21 12H9" stroke="currentColor" stroke-width="2" />
                      </svg>
                      Sign Out
                    </a>
                  </div>
                </template>
              </div>
            </div>

            <!-- Wishlist Dropdown -->
            <div class="icon-dropdown wishlist-dropdown" @mouseenter="showWishlistDropdown = true"
              @mouseleave="hideWishlistDropdown">
              <router-link to="/wishlist" class="icon-btn wishlist-btn">
                <div class="icon-wrapper">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <path
                      d="M19.5 12.572L12 20L4.5 12.572C3.605 11.677 3 10.391 3 9C3 6.791 4.791 5 7 5C8.309 5 9.5 5.671 10.214 6.714L12 9L13.786 6.714C14.5 5.671 15.691 5 17 5C19.209 5 21 6.791 21 9C21 10.391 20.395 11.677 19.5 12.572Z"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  <span class="wishlist-count-nav" v-if="wishlistStore.totalItems > 0">
                    {{ wishlistStore.totalItems }}
                  </span>
                </div>
                <span class="icon-text">Wishlist</span>
              </router-link>

              <!-- Wishlist Dropdown Menu -->
              <div class="dropdown-menu wishlist-menu" :class="{ 'show': true }" @mouseenter="keepWishlistDropdownOpen"
                @mouseleave="hideWishlistDropdown">
                <div class="wishlist-dropdown-content">
                  <div class="wishlist-header">
                    <h4>Your Wishlist ({{ wishlistStore.totalItems }})</h4>
                  </div>

                  <div v-if="wishlistStore.totalItems > 0" class="wishlist-items">
                    <div class="wishlist-items-scroll">
                      <div v-for="item in wishlistStore.items.slice(0, 3)" :key="item.id" class="wishlist-item">
                        <img :src="item.image || '/placeholder-product.jpg'" :alt="item.name"
                          class="wishlist-item-image">
                        <div class="wishlist-item-details">
                          <h5 class="wishlist-item-title">{{ item.name }}</h5>
                          <span class="wishlist-item-price">£{{ item.price }}</span>
                        </div>
                      </div>
                    </div>

                    <div class="wishlist-actions">
                      <router-link to="/wishlist" class="btn btn-view-wishlist" @click="hideWishlistDropdown">
                        View All Wishlist
                      </router-link>
                    </div>
                  </div>

                  <div v-else class="empty-wishlist-state">
                    <div class="empty-wishlist-icon">❤️</div>
                    <p class="empty-wishlist-text">Your wishlist is empty</p>
                    <router-link to="/products" class="btn btn-start-shopping" @click="hideWishlistDropdown">
                      Start Shopping
                    </router-link>
                  </div>
                </div>
              </div>
            </div>

            <!-- Cart -->
            <div class="icon-dropdown cart-dropdown" @mouseenter="showCart = true" @mouseleave="hideCartDelay">
              <router-link to="/cart" class="icon-btn cart-btn">
                <div class="icon-wrapper">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M3 3H5L5.4 5M7 13H17L21 5H5.4M7 13L5.4 5M7 13L4.7 15.3C4.3 15.7 4.6 16.4 5.2 16.4H17M17 13V16.4M9 19C9 19.6 8.6 20 8 20C7.4 20 7 19.6 7 19C7 18.4 7.4 18 8 18C8.6 18 9 18.4 9 19ZM17 19C17 19.6 16.6 20 16 20C15.4 20 15 19.6 15 19C15 18.4 15.4 18 16 18C16.6 18 17 18.4 17 19Z"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  <span class="cart-count" v-if="cartTotal > 0">{{ cartTotal }}</span>
                </div>
                <span class="icon-text">Cart</span>
              </router-link>

              <!-- Cart Dropdown -->
              <div class="dropdown-menu cart-dropdown-menu" :class="{ 'show': showCart }" @mouseenter="keepCartOpen"
                @mouseleave="hideCartDelay">
                <div class="cart-dropdown-container">
                  <div class="cart-dropdown-content">
                    <!-- Cart Header -->
                    <div class="cart-header">
                      <h4>Your Cart ({{ cartTotal }})</h4>
                    </div>

                    <!-- Cart Items -->
                    <div v-if="cartItems.length > 0" class="cart-content">
                      <div class="cart-items-scroll">
                        <div v-for="item in cartItems" :key="`${item.id}-${item.variant_id}-${item.size_id}`"
                          class="cart-item">
                          <img :src="item.image" :alt="item.name" class="item-image">
                          <div class="item-details">
                            <h5 class="item-title">{{ item.name }}</h5>

                            <!-- Display size below product name -->
                            <div class="item-size" v-if="item.size_title">
                              Size: {{ item.size_title }}
                            </div>

                            <!-- Price, quantity and total on same line -->
                            <div class="item-meta">
                              <span class="item-price">£{{ item.price }}</span>
                              <span class="item-quantity">× {{ item.quantity }}</span>
                              <span class="item-total">£{{ (item.price * item.quantity).toFixed(2) }}</span>
                            </div>
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
            <path
              d="M21 21L16.514 16.506L21 21ZM19 10.5C19 15.194 15.194 19 10.5 19C5.806 19 2 15.194 2 10.5C2 5.806 5.806 2 10.5 2C15.194 2 19 5.806 19 10.5Z"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>
      </div>

      <ul class="mobile-nav">
        <li><router-link to="/" @click="closeMobileMenu">Home</router-link></li>

        <li class="mobile-dropdown">
          <a href="#" @click.prevent="toggleMobileDropdown('workwear')">Workwear ▸</a>
          <ul class="mobile-submenu" v-if="mobileDropdowns.workwear">
            <li>
              <h5>Men's Workwear</h5>
            </li>
            <li><router-link to="/workwear/mens/work-jackets" @click="closeMobileMenu">Work Jackets</router-link></li>
            <li><router-link to="/workwear/mens/work-trousers" @click="closeMobileMenu">Work Trousers</router-link></li>
            <li>
              <h5>Women's Workwear</h5>
            </li>
            <li><router-link to="/workwear/womens/work-jackets" @click="closeMobileMenu">Work Jackets</router-link></li>
            <li><router-link to="/workwear/womens/work-trousers" @click="closeMobileMenu">Work Trousers</router-link>
            </li>
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
import { useWishlistStore } from '@/store/wishlist'

const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()
const wishlistStore = useWishlistStore()

const searchQuery = ref('')
const mobileSearchQuery = ref('')
const showMobileMenu = ref(false)
const showCart = ref(false)
const showProfileDropdown = ref(false)
const showWishlistDropdown = ref(false)
const hideCartTimeout = ref(null)
const hideProfileTimeout = ref(null)
const hideWishlistTimeout = ref(null)
const mobileDropdowns = ref({
  workwear: false,
  safetywear: false,
  footwear: false
})

// Computed properties
const isAuthenticated = computed(() => authStore.isAuthenticated)
const userFirstName = computed(() => authStore.userFirstName)
const userEmail = computed(() => authStore.userEmail)
const cartTotal = computed(() => cartStore.totalItems)
const cartItems = computed(() => cartStore.items.slice(0, 3))
const cartSubtotal = computed(() => cartStore.subtotal.toFixed(2))

// Profile Dropdown Methods
const keepProfileDropdownOpen = () => {
  if (hideProfileTimeout.value) {
    clearTimeout(hideProfileTimeout.value)
    hideProfileTimeout.value = null
  }
  showProfileDropdown.value = true
}

const hideProfileDropdown = () => {
  hideProfileTimeout.value = setTimeout(() => {
    showProfileDropdown.value = false
  }, 300)
}

// Wishlist Dropdown Methods
const keepWishlistDropdownOpen = () => {
  if (hideWishlistTimeout.value) {
    clearTimeout(hideWishlistTimeout.value)
    hideWishlistTimeout.value = null
  }
  showWishlistDropdown.value = true
}

const hideWishlistDropdown = () => {
  hideWishlistTimeout.value = setTimeout(() => {
    showWishlistDropdown.value = false
  }, 300)
}

// Cart Dropdown Methods
const keepCartOpen = () => {
  if (hideCartTimeout.value) {
    clearTimeout(hideCartTimeout.value)
    hideCartTimeout.value = null
  }
  showCart.value = true
}

const hideCart = () => {
  showCart.value = false
}

const hideCartDelay = () => {
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

const logout = () => {
  authStore.logout()
  wishlistStore.clearWishlist()
  hideProfileDropdown()
  router.push('/')
}

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

// Initialize
onMounted(() => {
  authStore.hydrate()
  cartStore.hydrate()
  wishlistStore.loadWishlist()
})

// Cleanup
onUnmounted(() => {
  if (hideCartTimeout.value) clearTimeout(hideCartTimeout.value)
  if (hideProfileTimeout.value) clearTimeout(hideProfileTimeout.value)
  if (hideWishlistTimeout.value) clearTimeout(hideWishlistTimeout.value)
})
</script>

<style scoped src="@/assets/styles/frontend.css"></style>