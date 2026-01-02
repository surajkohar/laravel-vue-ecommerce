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

          <!-- Mobile Menu Toggle - Moved here for better positioning -->
<button class="mobile-menu-toggle ml-2" @click="toggleMobileMenu" :class="{ 'active': showMobileMenu }">
  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="hamburger-icon">
    <path d="M3 12H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
    <path d="M3 6H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
    <path d="M3 18H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
  </svg>
</button>

          <!-- Search Bar - Hidden on mobile -->
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
            <div class="icon-dropdown account-dropdown" 
              ref="accountDropdown"
              @mouseenter="onDesktop && (showProfileDropdown = true)"
              @mouseleave="onDesktop && hideProfileDropdown()">
              <button class="icon-btn" @click="toggleProfileDropdown">
                <div class="icon-wrapper">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <path d="M20 21V19C20 16.7909 18.2091 15 16 15H8C5.79086 15 4 16.7909 4 19V21" stroke="currentColor"
                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" />
                  </svg>
                </div>
                <span class="icon-text">Account</span>
              </button>

              <!-- Profile Dropdown Menu -->
              <div class="dropdown-menu account-menu" :class="{ 'show': showProfileDropdown }"
                v-show="showProfileDropdown">
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
                    <router-link to="/account/orders" class="dropdown-item" @click="hideProfileDropdown">
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
            <div class="icon-dropdown wishlist-dropdown" 
              ref="wishlistDropdown"
              @mouseenter="onDesktop && (showWishlistDropdown = true)"
              @mouseleave="onDesktop && hideWishlistDropdown()">
              <button class="icon-btn wishlist-btn" @click="toggleWishlistDropdown">
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
              </button>

              <!-- Wishlist Dropdown Menu -->
              <div class="dropdown-menu wishlist-menu" :class="{ 'show': showWishlistDropdown }"
                v-show="showWishlistDropdown">
                <div class="wishlist-dropdown-content">
                  <div class="wishlist-header">
                    <h4>Your Wishlist ({{ wishlistStore.totalItems }})</h4>
                    <button v-if="!onDesktop" class="close-dropdown" @click="showWishlistDropdown = false">×</button>
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
            <div class="icon-dropdown cart-dropdown" 
              ref="cartDropdown"
              @mouseenter="onDesktop && (showCart = true)"
              @mouseleave="onDesktop && hideCartDelay()">
              <button class="icon-btn cart-btn" @click="toggleCart">
                <div class="icon-wrapper">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M3 3H5L5.4 5M7 13H17L21 5H5.4M7 13L5.4 5M7 13L4.7 15.3C4.3 15.7 4.6 16.4 5.2 16.4H17M17 13V16.4M9 19C9 19.6 8.6 20 8 20C7.4 20 7 19.6 7 19C7 18.4 7.4 18 8 18C8.6 18 9 18.4 9 19ZM17 19C17 19.6 16.6 20 16 20C15.4 20 15 19.6 15 19C15 18.4 15.4 18 16 18C16.6 18 17 18.4 17 19Z"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  <span class="cart-count" v-if="cartTotal > 0">{{ cartTotal }}</span>
                </div>
                <span class="icon-text">Cart</span>
              </button>

              <!-- Cart Dropdown -->
              <div class="dropdown-menu cart-dropdown-menu" :class="{ 'show': showCart }">
                <div class="cart-dropdown-container">
                  <div class="cart-dropdown-content">
                    <!-- Cart Header -->
                    <div class="cart-header">
                      <h4>Your Cart ({{ cartTotal }})</h4>
                      <button v-if="!onDesktop" class="close-dropdown" @click="showCart = false">×</button>
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
          <li class="nav-item mega-menu" 
              ref="workwearMegaMenu"
              @mouseenter="onDesktop && (activeMegaMenu = 'workwear')"
              @mouseleave="onDesktop && (activeMegaMenu = '')">
            <a href="#" class="nav-link" @click.prevent="toggleMegaMenu('workwear')">Workwear ▾</a>
            <div class="mega-menu-content" v-show="onDesktop ? activeMegaMenu === 'workwear' : showMobileMegaMenu === 'workwear'">
              <div class="mega-menu-column">
                <h4>Men's Workwear</h4>
                <router-link to="/workwear/mens/work-jackets" @click="closeMegaMenu">Work Jackets</router-link>
                <router-link to="/workwear/mens/work-trousers" @click="closeMegaMenu">Work Trousers</router-link>
                <router-link to="/workwear/mens/coveralls" @click="closeMegaMenu">Coveralls</router-link>
                <router-link to="/workwear/mens/hi-vis" @click="closeMegaMenu">Hi-Vis Clothing</router-link>
                <router-link to="/workwear/mens/fleeces" @click="closeMegaMenu">Fleeces & Sweatshirts</router-link>
              </div>
              <div class="mega-menu-column">
                <h4>Women's Workwear</h4>
                <router-link to="/workwear/womens/work-jackets" @click="closeMegaMenu">Work Jackets</router-link>
                <router-link to="/workwear/womens/work-trousers" @click="closeMegaMenu">Work Trousers</router-link>
                <router-link to="/workwear/womens/hi-vis" @click="closeMegaMenu">Hi-Vis Clothing</router-link>
                <router-link to="/workwear/womens/fleeces" @click="closeMegaMenu">Fleeces & Sweatshirts</router-link>
              </div>
              <div class="mega-menu-column">
                <h4>Corporate Wear</h4>
                <router-link to="/workwear/corporate/shirts" @click="closeMegaMenu">Shirts</router-link>
                <router-link to="/workwear/corporate/trousers" @click="closeMegaMenu">Trousers</router-link>
                <router-link to="/workwear/corporate/polos" @click="closeMegaMenu">Polo Shirts</router-link>
                <router-link to="/workwear/corporate/footwear" @click="closeMegaMenu">Corporate Footwear</router-link>
              </div>
              <div class="mega-menu-column">
                <h4>Featured Brands</h4>
                <router-link to="/brands/dickies" @click="closeMegaMenu">Dickies</router-link>
                <router-link to="/brands/snickers" @click="closeMegaMenu">Snickers</router-link>
                <router-link to="/brands/portwest" @click="closeMegaMenu">Portwest</router-link>
                <router-link to="/brands/helly-hansen" @click="closeMegaMenu">Helly Hansen</router-link>
                <router-link to="/brands/regatta" @click="closeMegaMenu">Regatta</router-link>
              </div>
            </div>
          </li>

          <!-- Safetywear Mega Menu -->
          <li class="nav-item mega-menu" 
              ref="safetywearMegaMenu"
              @mouseenter="onDesktop && (activeMegaMenu = 'safetywear')"
              @mouseleave="onDesktop && (activeMegaMenu = '')">
            <a href="#" class="nav-link" @click.prevent="toggleMegaMenu('safetywear')">Safetywear ▾</a>
            <div class="mega-menu-content" v-show="onDesktop ? activeMegaMenu === 'safetywear' : showMobileMegaMenu === 'safetywear'">
              <div class="mega-menu-column">
                <h4>Head Protection</h4>
                <router-link to="/safetywear/head/safety-helmets" @click="closeMegaMenu">Safety Helmets</router-link>
                <router-link to="/safetywear/head/bump-caps" @click="closeMegaMenu">Bump Caps</router-link>
                <router-link to="/safetywear/head/accessories" @click="closeMegaMenu">Accessories</router-link>
              </div>
              <div class="mega-menu-column">
                <h4>Eye & Face Protection</h4>
                <router-link to="/safetywear/eye/safety-glasses" @click="closeMegaMenu">Safety Glasses</router-link>
                <router-link to="/safetywear/eye/goggles" @click="closeMegaMenu">Goggles</router-link>
                <router-link to="/safetywear/eye/face-shields" @click="closeMegaMenu">Face Shields</router-link>
              </div>
              <div class="mega-menu-column">
                <h4>Hearing Protection</h4>
                <router-link to="/safetywear/hearing/ear-defenders" @click="closeMegaMenu">Ear Defenders</router-link>
                <router-link to="/safetywear/hearing/ear-plugs" @click="closeMegaMenu">Ear Plugs</router-link>
              </div>
              <div class="mega-menu-column">
                <h4>Respiratory Protection</h4>
                <router-link to="/safetywear/respiratory/masks" @click="closeMegaMenu">Face Masks</router-link>
                <router-link to="/safetywear/respiratory/respirators" @click="closeMegaMenu">Respirators</router-link>
              </div>
            </div>
          </li>

          <!-- Footwear Mega Menu -->
          <li class="nav-item mega-menu" 
              ref="footwearMegaMenu"
              @mouseenter="onDesktop && (activeMegaMenu = 'footwear')"
              @mouseleave="onDesktop && (activeMegaMenu = '')">
            <a href="#" class="nav-link" @click.prevent="toggleMegaMenu('footwear')">Footwear ▾</a>
            <div class="mega-menu-content" v-show="onDesktop ? activeMegaMenu === 'footwear' : showMobileMegaMenu === 'footwear'">
              <div class="mega-menu-column">
                <h4>Safety Boots</h4>
                <router-link to="/footwear/safety-boots/steel-toe" @click="closeMegaMenu">Steel Toe</router-link>
                <router-link to="/footwear/safety-boots/composite-toe" @click="closeMegaMenu">Composite Toe</router-link>
                <router-link to="/footwear/safety-boots/waterproof" @click="closeMegaMenu">Waterproof</router-link>
                <router-link to="/footwear/safety-boots/anti-slip" @click="closeMegaMenu">Anti-Slip</router-link>
              </div>
              <div class="mega-menu-column">
                <h4>Work Shoes</h4>
                <router-link to="/footwear/work-shoes/safety-shoes" @click="closeMegaMenu">Safety Shoes</router-link>
                <router-link to="/footwear/work-shoes/trainers" @click="closeMegaMenu">Work Trainers</router-link>
                <router-link to="/footwear/work-shoes/casual" @click="closeMegaMenu">Casual Work Shoes</router-link>
              </div>
              <div class="mega-menu-column">
                <h4>Wellies & Waders</h4>
                <router-link to="/footwear/wellies/safety-wellies" @click="closeMegaMenu">Safety Wellies</router-link>
                <router-link to="/footwear/wellies/standard" @click="closeMegaMenu">Standard Wellies</router-link>
                <router-link to="/footwear/wellies/waders" @click="closeMegaMenu">Waders</router-link>
              </div>
              <div class="mega-menu-column">
                <h4>Featured Brands</h4>
                <router-link to="/footwear/brands/dickies" @click="closeMegaMenu">Dickies</router-link>
                <router-link to="/footwear/brands/skechers" @click="closeMegaMenu">Skechers</router-link>
                <router-link to="/footwear/brands/cat" @click="closeMegaMenu">CAT</router-link>
                <router-link to="/footwear/brands/dewalt" @click="closeMegaMenu">DeWalt</router-link>
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
          <a href="#" @click.prevent="toggleMobileDropdown('workwear')">
            Workwear 
            <span class="mobile-dropdown-arrow">{{ mobileDropdowns.workwear ? '▾' : '▸' }}</span>
          </a>
          <ul class="mobile-submenu" v-if="mobileDropdowns.workwear">
            <li><h5>Men's Workwear</h5></li>
            <li><router-link to="/workwear/mens/work-jackets" @click="closeMobileMenu">Work Jackets</router-link></li>
            <li><router-link to="/workwear/mens/work-trousers" @click="closeMobileMenu">Work Trousers</router-link></li>
            <li><router-link to="/workwear/mens/coveralls" @click="closeMobileMenu">Coveralls</router-link></li>
            <li><router-link to="/workwear/mens/hi-vis" @click="closeMobileMenu">Hi-Vis Clothing</router-link></li>
            <li><router-link to="/workwear/mens/fleeces" @click="closeMobileMenu">Fleeces & Sweatshirts</router-link></li>
            <li><h5>Women's Workwear</h5></li>
            <li><router-link to="/workwear/womens/work-jackets" @click="closeMobileMenu">Work Jackets</router-link></li>
            <li><router-link to="/workwear/womens/work-trousers" @click="closeMobileMenu">Work Trousers</router-link></li>
            <li><router-link to="/workwear/womens/hi-vis" @click="closeMobileMenu">Hi-Vis Clothing</router-link></li>
            <li><router-link to="/workwear/womens/fleeces" @click="closeMobileMenu">Fleeces & Sweatshirts</router-link></li>
            <li><h5>Corporate Wear</h5></li>
            <li><router-link to="/workwear/corporate/shirts" @click="closeMobileMenu">Shirts</router-link></li>
            <li><router-link to="/workwear/corporate/trousers" @click="closeMobileMenu">Trousers</router-link></li>
            <li><router-link to="/workwear/corporate/polos" @click="closeMobileMenu">Polo Shirts</router-link></li>
            <li><router-link to="/workwear/corporate/footwear" @click="closeMobileMenu">Corporate Footwear</router-link></li>
            <li><h5>Featured Brands</h5></li>
            <li><router-link to="/brands/dickies" @click="closeMobileMenu">Dickies</router-link></li>
            <li><router-link to="/brands/snickers" @click="closeMobileMenu">Snickers</router-link></li>
            <li><router-link to="/brands/portwest" @click="closeMobileMenu">Portwest</router-link></li>
            <li><router-link to="/brands/helly-hansen" @click="closeMobileMenu">Helly Hansen</router-link></li>
            <li><router-link to="/brands/regatta" @click="closeMobileMenu">Regatta</router-link></li>
          </ul>
        </li>

        <li class="mobile-dropdown">
          <a href="#" @click.prevent="toggleMobileDropdown('safetywear')">
            Safetywear 
            <span class="mobile-dropdown-arrow">{{ mobileDropdowns.safetywear ? '▾' : '▸' }}</span>
          </a>
          <ul class="mobile-submenu" v-if="mobileDropdowns.safetywear">
            <li><h5>Head Protection</h5></li>
            <li><router-link to="/safetywear/head/safety-helmets" @click="closeMobileMenu">Safety Helmets</router-link></li>
            <li><router-link to="/safetywear/head/bump-caps" @click="closeMobileMenu">Bump Caps</router-link></li>
            <li><router-link to="/safetywear/head/accessories" @click="closeMobileMenu">Accessories</router-link></li>
            <li><h5>Eye & Face Protection</h5></li>
            <li><router-link to="/safetywear/eye/safety-glasses" @click="closeMobileMenu">Safety Glasses</router-link></li>
            <li><router-link to="/safetywear/eye/goggles" @click="closeMobileMenu">Goggles</router-link></li>
            <li><router-link to="/safetywear/eye/face-shields" @click="closeMobileMenu">Face Shields</router-link></li>
            <li><h5>Hearing Protection</h5></li>
            <li><router-link to="/safetywear/hearing/ear-defenders" @click="closeMobileMenu">Ear Defenders</router-link></li>
            <li><router-link to="/safetywear/hearing/ear-plugs" @click="closeMobileMenu">Ear Plugs</router-link></li>
            <li><h5>Respiratory Protection</h5></li>
            <li><router-link to="/safetywear/respiratory/masks" @click="closeMobileMenu">Face Masks</router-link></li>
            <li><router-link to="/safetywear/respiratory/respirators" @click="closeMobileMenu">Respirators</router-link></li>
          </ul>
        </li>

        <li class="mobile-dropdown">
          <a href="#" @click.prevent="toggleMobileDropdown('footwear')">
            Footwear 
            <span class="mobile-dropdown-arrow">{{ mobileDropdowns.footwear ? '▾' : '▸' }}</span>
          </a>
          <ul class="mobile-submenu" v-if="mobileDropdowns.footwear">
            <li><h5>Safety Boots</h5></li>
            <li><router-link to="/footwear/safety-boots/steel-toe" @click="closeMobileMenu">Steel Toe</router-link></li>
            <li><router-link to="/footwear/safety-boots/composite-toe" @click="closeMobileMenu">Composite Toe</router-link></li>
            <li><router-link to="/footwear/safety-boots/waterproof" @click="closeMobileMenu">Waterproof</router-link></li>
            <li><router-link to="/footwear/safety-boots/anti-slip" @click="closeMobileMenu">Anti-Slip</router-link></li>
            <li><h5>Work Shoes</h5></li>
            <li><router-link to="/footwear/work-shoes/safety-shoes" @click="closeMobileMenu">Safety Shoes</router-link></li>
            <li><router-link to="/footwear/work-shoes/trainers" @click="closeMobileMenu">Work Trainers</router-link></li>
            <li><router-link to="/footwear/work-shoes/casual" @click="closeMobileMenu">Casual Work Shoes</router-link></li>
            <li><h5>Wellies & Waders</h5></li>
            <li><router-link to="/footwear/wellies/safety-wellies" @click="closeMobileMenu">Safety Wellies</router-link></li>
            <li><router-link to="/footwear/wellies/standard" @click="closeMobileMenu">Standard Wellies</router-link></li>
            <li><router-link to="/footwear/wellies/waders" @click="closeMobileMenu">Waders</router-link></li>
            <li><h5>Featured Brands</h5></li>
            <li><router-link to="/footwear/brands/dickies" @click="closeMobileMenu">Dickies</router-link></li>
            <li><router-link to="/footwear/brands/skechers" @click="closeMobileMenu">Skechers</router-link></li>
            <li><router-link to="/footwear/brands/cat" @click="closeMobileMenu">CAT</router-link></li>
            <li><router-link to="/footwear/brands/dewalt" @click="closeMobileMenu">DeWalt</router-link></li>
          </ul>
        </li>

        <li><router-link to="/ppe" @click="closeMobileMenu">PPE</router-link></li>
        <li><router-link to="/brands" @click="closeMobileMenu">Brands</router-link></li>
        <li><router-link to="/clearance" @click="closeMobileMenu" class="sale">Clearance</router-link></li>
        <li><router-link to="/corporate" @click="closeMobileMenu">Corporate</router-link></li>

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
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/store/auth'
import { useCartStore } from '@/store/cart'
import { useWishlistStore } from '@/store/wishlist'

const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()
const wishlistStore = useWishlistStore()

// Refs
const searchQuery = ref('')
const mobileSearchQuery = ref('')
const showMobileMenu = ref(false)
const showCart = ref(false)
const showProfileDropdown = ref(false)
const showWishlistDropdown = ref(false)
const activeMegaMenu = ref('')
const showMobileMegaMenu = ref('')
const onDesktop = ref(window.innerWidth > 768)

// Timeout refs
const hideCartTimeout = ref(null)
const hideProfileTimeout = ref(null)
const hideWishlistTimeout = ref(null)

// Mobile dropdowns
const mobileDropdowns = ref({
  workwear: false,
  safetywear: false,
  footwear: false
})

// Element refs
const accountDropdown = ref(null)
const wishlistDropdown = ref(null)
const cartDropdown = ref(null)
const workwearMegaMenu = ref(null)
const safetywearMegaMenu = ref(null)
const footwearMegaMenu = ref(null)

// Computed properties
const isAuthenticated = computed(() => authStore.isAuthenticated)
const userFirstName = computed(() => authStore.userFirstName)
const userEmail = computed(() => authStore.userEmail)
const cartTotal = computed(() => cartStore.totalItems)
const cartItems = computed(() => cartStore.items.slice(0, 3))
const cartSubtotal = computed(() => cartStore.subtotal.toFixed(2))

// Update desktop detection
const updateDesktop = () => {
  onDesktop.value = window.innerWidth > 768
}

// Desktop hover methods
const hideProfileDropdown = () => {
  if (onDesktop.value) {
    hideProfileTimeout.value = setTimeout(() => {
      showProfileDropdown.value = false
    }, 300)
  }
}

const hideWishlistDropdown = () => {
  if (onDesktop.value) {
    hideWishlistTimeout.value = setTimeout(() => {
      showWishlistDropdown.value = false
    }, 300)
  }
}

const hideCartDelay = () => {
  if (onDesktop.value) {
    hideCartTimeout.value = setTimeout(() => {
      showCart.value = false
    }, 300)
  }
}

// Clear timeouts when entering dropdown
const clearProfileTimeout = () => {
  if (hideProfileTimeout.value) {
    clearTimeout(hideProfileTimeout.value)
    hideProfileTimeout.value = null
  }
}

const clearWishlistTimeout = () => {
  if (hideWishlistTimeout.value) {
    clearTimeout(hideWishlistTimeout.value)
    hideWishlistTimeout.value = null
  }
}

const clearCartTimeout = () => {
  if (hideCartTimeout.value) {
    clearTimeout(hideCartTimeout.value)
    hideCartTimeout.value = null
  }
}

// Toggle methods for mobile
const toggleProfileDropdown = () => {
  if (!onDesktop.value) {
    showProfileDropdown.value = !showProfileDropdown.value
    // Close other dropdowns
    if (showProfileDropdown.value) {
      showWishlistDropdown.value = false
      showCart.value = false
    }
  }
}

const toggleWishlistDropdown = () => {
  if (!onDesktop.value) {
    showWishlistDropdown.value = !showWishlistDropdown.value
    // Close other dropdowns
    if (showWishlistDropdown.value) {
      showProfileDropdown.value = false
      showCart.value = false
    }
  }
}

const toggleCart = () => {
  if (!onDesktop.value) {
    showCart.value = !showCart.value
    // Close other dropdowns
    if (showCart.value) {
      showProfileDropdown.value = false
      showWishlistDropdown.value = false
    }
  }
}

const toggleMegaMenu = (menu) => {
  if (!onDesktop.value) {
    showMobileMegaMenu.value = showMobileMegaMenu.value === menu ? '' : menu
  }
}

const closeMegaMenu = () => {
  if (onDesktop.value) {
    activeMegaMenu.value = ''
  } else {
    showMobileMegaMenu.value = ''
  }
}

const hideCart = () => {
  showCart.value = false
}

const logout = () => {
  authStore.logout()
  wishlistStore.clearWishlist()
  showProfileDropdown.value = false
  closeMobileMenu()
  router.push('/')
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
  // Reset all mobile dropdowns
  Object.keys(mobileDropdowns.value).forEach(key => {
    mobileDropdowns.value[key] = false
  })
  showMobileMegaMenu.value = ''
}

const toggleMobileDropdown = (dropdown) => {
  // Close other dropdowns
  Object.keys(mobileDropdowns.value).forEach(key => {
    mobileDropdowns.value[key] = key === dropdown ? !mobileDropdowns.value[key] : false
  })
}

// Close dropdowns when clicking outside
const handleClickOutside = (event) => {
  if (onDesktop.value) return

  const isClickInsideDropdown = 
    (accountDropdown.value && accountDropdown.value.contains(event.target)) ||
    (wishlistDropdown.value && wishlistDropdown.value.contains(event.target)) ||
    (cartDropdown.value && cartDropdown.value.contains(event.target)) ||
    (workwearMegaMenu.value && workwearMegaMenu.value.contains(event.target)) ||
    (safetywearMegaMenu.value && safetywearMegaMenu.value.contains(event.target)) ||
    (footwearMegaMenu.value && footwearMegaMenu.value.contains(event.target))

  if (!isClickInsideDropdown) {
    showProfileDropdown.value = false
    showWishlistDropdown.value = false
    showCart.value = false
    showMobileMegaMenu.value = ''
  }
}

// Initialize
onMounted(() => {
  authStore.hydrate()
  cartStore.hydrate()
  wishlistStore.loadWishlist()
  window.addEventListener('resize', updateDesktop)
  document.addEventListener('click', handleClickOutside)
})

// Cleanup
onUnmounted(() => {
  if (hideCartTimeout.value) clearTimeout(hideCartTimeout.value)
  if (hideProfileTimeout.value) clearTimeout(hideProfileTimeout.value)
  if (hideWishlistTimeout.value) clearTimeout(hideWishlistTimeout.value)
  window.removeEventListener('resize', updateDesktop)
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped src="@/assets/styles/frontend.css"></style>