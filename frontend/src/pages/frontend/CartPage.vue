<template>
  <FrontendLayout>
    <div class="cart-page">
      <div class="container">
        <!-- Page Header -->
        <div class="page-header">
          <div class="breadcrumb">
            <router-link to="/" class="breadcrumb-link">Home</router-link>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">Shopping Cart</span>
          </div>
          <h1 class="page-title">Shopping Cart</h1>
          <p class="page-subtitle">Review your items and proceed to checkout</p>
        </div>

        <div class="cart-layout">
          <!-- Main Cart Content -->
          <div class="cart-main">
            <!-- Cart Items Section -->
            <div class="cart-section" v-if="cartStore.hasItems">
              <div class="section-header">
                <h2>Your Items ({{ cartStore.totalItems }})</h2>
                <button class="btn btn-outline" @click="clearCart" v-if="cartStore.hasItems">
                  Clear Cart
                </button>
              </div>

              <div class="cart-items">
                <div v-for="item in cartStore.items" :key="`${item.id}-${item.variant_id}-${item.size_id}`"
                  class="cart-item">
                  <div class="item-image">
                    <img :src="item.image" :alt="item.name" />
                    <div class="item-badge" v-if="item.quantity > 1">{{ item.quantity }}</div>
                  </div>

                  <div class="item-details">
                    <h3 class="item-name">{{ item.name }}</h3>
                    <div class="item-variants">
                      <span class="variant" v-if="item.variant_name">Color: {{ item.variant_name }}</span>
                      <span class="variant" v-if="item.size_title">Size: {{ item.size_title }}</span>
                      <!-- Change from item.size to item.size_title -->
                    </div>
                    <div class="item-price-info">
                      <span class="item-unit-price">£{{ item.price }} each</span>
                    </div>
                    <div class="item-stock" :class="{ 'low-stock': item.stock < 10 }">
                      {{ item.stock < 10 ? `Only ${item.stock} left in stock` : 'In stock' }} </div>
                    </div>

                    <div class="item-controls">
                      <div class="quantity-control">
                        <button class="qty-btn" @click="decreaseQuantity(item)" :disabled="item.quantity <= 1">
                          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                          </svg>
                        </button>
                        <span class="quantity">{{ item.quantity }}</span>
                        <button class="qty-btn" @click="increaseQuantity(item)" :disabled="item.quantity >= item.stock">
                          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                          </svg>
                        </button>
                      </div>

                      <div class="price-info">
                        <div class="unit-price">£{{ item.price }} each</div>
                        <div class="total-price">£{{ (item.price * item.quantity).toFixed(2) }}</div>
                      </div>
                    </div>

                    <button class="remove-btn" @click="removeItem(item)" title="Remove item">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Empty Cart State -->
              <div class="empty-state" v-else>
                <div class="empty-state-content">
                  <div class="empty-icon">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none">
                      <path
                        d="M3 3H5L5.4 5M7 13H17L21 5H5.4M7 13L5.4 5M7 13L4.7 15.3C4.3 15.7 4.6 16.4 5.2 16.4H17M17 13V16.4M9 19C9 19.6 8.6 20 8 20C7.4 20 7 19.6 7 19C7 18.4 7.4 18 8 18C8.6 18 9 18.4 9 19ZM17 19C17 19.6 16.6 20 16 20C15.4 20 15 19.6 15 19C15 18.4 15.4 18 16 18C16.6 18 17 18.4 17 19Z"
                        stroke="var(--primary-red)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                  </div>
                  <h3>Your cart is empty</h3>
                  <p>Looks like you haven't added any items to your cart yet.</p>
                  <router-link to="/products" class="btn btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="margin-right: 8px;">
                      <path
                        d="M6 2L3 6V20C3 20.5304 3.21071 21.0391 3.58579 21.4142C3.96086 21.7893 4.46957 22 5 22H19C19.5304 22 20.0391 21.7893 20.4142 21.4142C20.7893 21.0391 21 20.5304 21 20V6L18 2H6Z"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                      <path
                        d="M16 10C16 11.0609 15.5786 12.0783 14.8284 12.8284C14.0783 13.5786 13.0609 14 12 14C10.9391 14 9.92172 13.5786 9.17157 12.8284C8.42143 12.0783 8 11.0609 8 10"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Start Shopping
                  </router-link>
                </div>
              </div>

              <!-- Trust Badges -->
              <div class="trust-badges" v-if="cartStore.hasItems">
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
                    <strong>Easy Returns</strong>
                    <span>30-day money back guarantee</span>
                  </div>
                </div>
                <div class="trust-item">
                  <div class="trust-icon">🔒</div>
                  <div class="trust-text">
                    <strong>Secure Checkout</strong>
                    <span>Your data is always protected</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="cart-sidebar" v-if="cartStore.hasItems">
              <div class="summary-card">
                <h3 class="summary-title">Order Summary</h3>

                <div class="summary-items">
                  <div class="summary-row">
                    <span>Subtotal ({{ cartStore.totalItems }} items)</span>
                    <span>£{{ cartStore.subtotal.toFixed(2) }}</span>
                  </div>
                  <div class="summary-row">
                    <span>Shipping</span>
                    <span class="free-shipping">Free</span>
                  </div>
                  <div class="summary-row">
                    <span>VAT (20%)</span>
                    <span>£{{ (cartStore.subtotal * 0.2).toFixed(2) }}</span>
                  </div>
                </div>

                <div class="summary-divider"></div>

                <div class="summary-total">
                  <span>Total</span>
                  <span class="total-amount">£{{ (cartStore.subtotal * 1.2).toFixed(2) }}</span>
                </div>

                <div class="shipping-progress">
                  <div class="progress-bar">
                    <div class="progress-fill" :style="{ width: shippingProgress + '%' }"></div>
                  </div>
                  <p class="progress-text" v-if="cartStore.subtotal < 50">
                    Add £{{ (50 - cartStore.subtotal).toFixed(2) }} more for free shipping!
                  </p>
                  <p class="progress-text success" v-else>
                    🎉 You qualify for free shipping!
                  </p>
                </div>

                <button class="btn btn-primary checkout-btn" @click="proceedToCheckout">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" style="margin-right: 8px;">
                    <path d="M5 12H19M12 5L19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                      stroke-linejoin="round" />
                  </svg>
                  Proceed to Checkout
                </button>

                <router-link to="/products" class="btn btn-outline continue-shopping">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="margin-right: 8px;">
                    <path d="M19 12H5M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                      stroke-linejoin="round" />
                  </svg>
                  Continue Shopping
                </router-link>

                <div class="payment-methods">
                  <p>We accept:</p>
                  <div class="payment-icons">
                    <span>💳</span>
                    <span>📱</span>
                    <span>🏦</span>
                    <span>🔗</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
  </FrontendLayout>
</template>

<script setup>
import { useCartStore } from '@/store/cart'
import { useRouter } from 'vue-router'
import FrontendLayout from '@/layouts/FrontendLayout.vue'
import { computed } from 'vue'

const cartStore = useCartStore()
const router = useRouter()

// Computed
const shippingProgress = computed(() => {
  const progress = (cartStore.subtotal / 50) * 100
  return Math.min(progress, 100)
})

// Methods
// Update methods in Cart.vue script
const increaseQuantity = (item) => {
  cartStore.updateQuantity(item.id, item.variant_id, item.size_id, item.quantity + 1)
}

const decreaseQuantity = (item) => {
  if (item.quantity > 1) {
    cartStore.updateQuantity(item.id, item.variant_id, item.size_id, item.quantity - 1)
  }
}

const removeItem = (item) => {
  if (confirm(`Are you sure you want to remove "${item.name}" from your cart?`)) {
    cartStore.removeFromCart(item.id, item.variant_id, item.size_id)
  }
}

const clearCart = () => {
  if (confirm('Are you sure you want to clear your entire cart?')) {
    cartStore.clearCart()
  }
}

const proceedToCheckout = () => {
  // In a real app, this would navigate to checkout
 router.push('/checkout')
  // router.push('/checkout')
}
</script>

<style scoped src="@/assets/styles/frontend.css"></style>

<style scoped>
.cart-page {
  min-height: 100vh;
  background: linear-gradient(135deg, var(--gray-light) 0%, #f1f5f9 100%);
  padding: 2rem 0 4rem;
}

.page-header {
  text-align: center;
  margin-bottom: 3rem;
}

.breadcrumb {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  margin-bottom: 1rem;
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

.page-title {
  font-size: 2.5rem;
  font-weight: 800;
  color: var(--primary-black);
  margin-bottom: 0.5rem;
  background: linear-gradient(135deg, var(--primary-black) 0%, var(--primary-red) 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.page-subtitle {
  color: var(--gray-medium);
  font-size: 1.1rem;
  max-width: 500px;
  margin: 0 auto;
}

.cart-layout {
  display: grid;
  grid-template-columns: 1fr 400px;
  gap: 2rem;
  align-items: start;
}

.cart-main {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.cart-section {
  background: var(--primary-white);
  border-radius: 16px;
  box-shadow: var(--shadow-medium);
  overflow: hidden;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 2rem;
  border-bottom: 1px solid var(--border-color);
  background: #fafafa;
}

.section-header h2 {
  margin: 0;
  color: var(--primary-black);
  font-size: 1.4rem;
  font-weight: 700;
}

.cart-items {
  padding: 1rem 0;
}

.cart-item {
  display: grid;
  grid-template-columns: 100px 1fr auto 60px;
  gap: 1.5rem;
  align-items: center;
  padding: 1.5rem 2rem;
  border-bottom: 1px solid #f5f5f5;
  transition: background-color 0.3s ease;
  position: relative;
}

.cart-item:hover {
  background: #fafafa;
}

.cart-item:last-child {
  border-bottom: none;
}

.item-image {
  position: relative;
  width: 100px;
  height: 100px;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: var(--shadow-light);
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background: var(--primary-red);
  color: var(--primary-white);
  border-radius: 50%;
  width: 24px;
  height: 24px;
  font-size: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
}

.item-details {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.item-name {
  margin: 0;
  color: var(--primary-black);
  font-size: 1.1rem;
  font-weight: 600;
  line-height: 1.3;
}

.item-variants {
  display: flex;
  gap: 12px;
}

.variant {
  background: var(--gray-light);
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  color: var(--gray-medium);
}

.item-stock {
  font-size: 13px;
  color: #22c55e;
  font-weight: 500;
}

.item-stock.low-stock {
  color: #ef4444;
}

.item-controls {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
}

.quantity-control {
  display: flex;
  align-items: center;
  gap: 8px;
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
  color: var(--primary-black);
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

.price-info {
  text-align: center;
}

.unit-price {
  font-size: 13px;
  color: var(--gray-medium);
  margin-bottom: 4px;
}

.total-price {
  font-weight: 700;
  color: var(--primary-red);
  font-size: 1.1rem;
}

.remove-btn {
  background: none;
  border: none;
  color: var(--gray-medium);
  cursor: pointer;
  padding: 8px;
  border-radius: 6px;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.remove-btn:hover {
  background: #fee;
  color: var(--primary-red);
}

.empty-state {
  background: var(--primary-white);
  border-radius: 16px;
  box-shadow: var(--shadow-medium);
  padding: 4rem 2rem;
  text-align: center;
}

.empty-state-content {
  max-width: 400px;
  margin: 0 auto;
}

.empty-icon {
  margin-bottom: 1.5rem;
  opacity: 0.7;
}

.empty-state h3 {
  color: var(--primary-black);
  margin-bottom: 1rem;
  font-size: 1.5rem;
}

.empty-state p {
  color: var(--gray-medium);
  margin-bottom: 2rem;
  line-height: 1.5;
}

.trust-badges {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-top: 1rem;
}

.trust-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.5rem;
  background: var(--primary-white);
  border-radius: 12px;
  box-shadow: var(--shadow-light);
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
  font-size: 14px;
}

.trust-text span {
  color: var(--gray-medium);
  font-size: 13px;
}

.cart-sidebar {
  position: sticky;
  top: 120px;
}

.summary-card {
  background: var(--primary-white);
  border-radius: 16px;
  box-shadow: var(--shadow-medium);
  padding: 2rem;
  position: sticky;
  top: 120px;
}

.summary-title {
  margin: 0 0 1.5rem 0;
  color: var(--primary-black);
  font-size: 1.3rem;
  font-weight: 700;
  text-align: center;
}

.summary-items {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 1rem;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: var(--gray-dark);
  font-size: 14px;
}

.free-shipping {
  color: #22c55e;
  font-weight: 600;
}

.summary-divider {
  height: 1px;
  background: var(--border-color);
  margin: 1.5rem 0;
}

.summary-total {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 1.2rem;
  font-weight: 700;
  color: var(--primary-black);
  margin-bottom: 1.5rem;
}

.total-amount {
  color: var(--primary-red);
  font-size: 1.4rem;
}

.shipping-progress {
  margin-bottom: 1.5rem;
}

.progress-bar {
  width: 100%;
  height: 6px;
  background: var(--gray-light);
  border-radius: 3px;
  overflow: hidden;
  margin-bottom: 8px;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, var(--primary-red), #ff6b6b);
  border-radius: 3px;
  transition: width 0.5s ease;
}

.progress-text {
  font-size: 13px;
  color: var(--gray-medium);
  text-align: center;
  margin: 0;
}

.progress-text.success {
  color: #22c55e;
  font-weight: 600;
}

.checkout-btn {
  width: 100%;
  padding: 16px;
  font-size: 1.1rem;
  font-weight: 700;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.continue-shopping {
  width: 100%;
  padding: 12px;
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.payment-methods {
  text-align: center;
  padding-top: 1.5rem;
  border-top: 1px solid var(--border-color);
}

.payment-methods p {
  font-size: 13px;
  color: var(--gray-medium);
  margin-bottom: 8px;
}

.payment-icons {
  display: flex;
  justify-content: center;
  gap: 12px;
}

.payment-icons span {
  font-size: 1.2rem;
  opacity: 0.7;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .cart-layout {
    grid-template-columns: 1fr;
    gap: 2rem;
  }

  .cart-sidebar {
    position: static;
  }

  .summary-card {
    position: static;
  }
}

@media (max-width: 768px) {
  .page-title {
    font-size: 2rem;
  }

  .cart-item {
    grid-template-columns: 80px 1fr;
    gap: 1rem;
    padding: 1.5rem;
  }

  .item-controls,
  .remove-btn {
    grid-column: 1 / -1;
    justify-self: start;
    margin-top: 1rem;
  }

  .item-controls {
    flex-direction: row;
    justify-content: space-between;
    width: 100%;
  }

  .trust-badges {
    grid-template-columns: 1fr;
  }

  .section-header {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
}

@media (max-width: 480px) {
  .container {
    padding: 0 15px;
  }

  .page-title {
    font-size: 1.8rem;
  }

  .cart-item {
    padding: 1rem;
  }

  .item-image {
    width: 70px;
    height: 70px;
  }

  .summary-card {
    padding: 1.5rem;
  }

  .checkout-btn,
  .continue-shopping {
    padding: 14px 20px;
    font-size: 1rem;
  }
}
</style>