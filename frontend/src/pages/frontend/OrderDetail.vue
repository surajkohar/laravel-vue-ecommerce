<template>
  <FrontendLayout>
    <div class="order-details-page">
      <div class="container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
          <router-link to="/" class="breadcrumb-link">Home</router-link>
          <span class="breadcrumb-separator">/</span>
          <router-link to="/account/orders" class="breadcrumb-link">My Orders</router-link>
          <span class="breadcrumb-separator">/</span>
          <span class="breadcrumb-current">Order {{ orderNumberParam }}</span>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <div class="loading-spinner"></div>
          <p>Loading order details...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="error-state">
          <div class="error-icon">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none">
              <path d="M10 14L12 12M12 12L14 10M12 12L10 10M12 12L14 14M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" 
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <h3>Error Loading Order</h3>
          <p>{{ error }}</p>
          <router-link to="/account/orders" class="btn btn-primary">
            Back to Orders
          </router-link>
        </div>

        <!-- Order Not Found -->
        <div v-else-if="!order" class="not-found">
          <div class="not-found-icon">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none">
              <path d="M9 17H5C3.89543 17 3 16.1046 3 15V5C3 3.89543 3.89543 3 5 3H15C16.1046 3 17 3.89543 17 5V9M9 17L13 13M9 17L11 15M13 13L16 16M13 13L15 11M16 16L21 11M16 16L19 13M21 11L19 9M21 11L23 13" 
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <h3>Order Not Found</h3>
          <p>The order "{{ orderNumberParam }}" doesn't exist or you don't have permission to view it.</p>
          <router-link to="/account/orders" class="btn btn-primary">
            Back to Orders
          </router-link>
        </div>

        <!-- Order Details -->
        <div v-else class="order-content">
          <!-- Order Header -->
          <div class="order-header">
            <div class="order-title">
              <h1>Order {{ order.order_number }}</h1>
              <p class="order-date">Placed on {{ formatDate(order.created_at) }}</p>
            </div>
            <div class="order-status-info">
              <div class="status-badge" :class="order.status">
                {{ getStatusText(order.status) }}
              </div>
              <div class="payment-status-badge" :class="order.payment_status">
                {{ getPaymentStatusText(order.payment_status) }}
              </div>
            </div>
          </div>

          <div class="order-layout">
            <!-- Main Content -->
            <div class="order-main">
              <!-- Order Items -->
              <div class="order-items-section">
                <div class="section-header">
                  <h2>Order Items</h2>
                  <div class="items-count">{{ order.items.length }} items</div>
                </div>

                <div class="order-items-list">
                  <div 
                    v-for="item in order.items" 
                    :key="item.id"
                    class="order-item"
                  >
                    <div class="item-image">
                      <img :src="item.image_url || '/images/placeholder.jpg'" :alt="item.product_name" />
                    </div>
                    <div class="item-details">
                      <h3 class="item-name">{{ item.product_name }}</h3>
                      <div class="item-meta">
                        <span v-if="item.variant_name" class="item-variant">{{ item.variant_name }}</span>
                        <span v-if="item.size_title" class="item-size">Size: {{ item.size_title }}</span>
                        <span class="item-quantity">Qty: {{ item.quantity }}</span>
                      </div>
                      <div class="item-price-info">
                        <div class="item-unit-price">₹{{ item.unit_price }} each</div>
                        <div class="item-total-price">₹{{ item.total_price }}</div>
                      </div>
                    </div>
                    <div class="item-actions">
                      <button 
                        class="btn btn-outline btn-sm"
                        @click="reorderItem(item)"
                      >
                        Reorder
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Shipping & Billing -->
              <div class="address-section">
                <div class="address-grid">
                  <div class="address-card">
                    <h3>Shipping Address</h3>
                    <div class="address-details">
                      <p v-if="shippingAddress.firstName || shippingAddress.lastName">
                        <strong>{{ shippingAddress.firstName }} {{ shippingAddress.lastName }}</strong>
                      </p>
                      <p>{{ shippingAddress.address1 }}</p>
                      <p v-if="shippingAddress.address2">{{ shippingAddress.address2 }}</p>
                      <p>{{ shippingAddress.city }}, {{ shippingAddress.postcode }}</p>
                      <p>{{ shippingAddress.country }}</p>
                      <p v-if="order.customer_phone">
                        <strong>Phone:</strong> {{ order.customer_phone }}
                      </p>
                      <p v-if="order.customer_email">
                        <strong>Email:</strong> {{ order.customer_email }}
                      </p>
                    </div>
                  </div>

                  <div class="address-card">
                    <h3>Billing Address</h3>
                    <div class="address-details">
                      <p v-if="billingAddress.firstName || billingAddress.lastName">
                        <strong>{{ billingAddress.firstName }} {{ billingAddress.lastName }}</strong>
                      </p>
                      <p>{{ billingAddress.address1 }}</p>
                      <p v-if="billingAddress.address2">{{ billingAddress.address2 }}</p>
                      <p>{{ billingAddress.city }}, {{ billingAddress.postcode }}</p>
                      <p>{{ billingAddress.country }}</p>
                    </div>
                  </div>

                  <div class="shipping-method-card">
                    <h3>Shipping Method</h3>
                    <div class="shipping-details">
                      <p><strong>{{ shippingMethodName }}</strong></p>
                      <p class="shipping-price">₹{{ order.shipping_cost }}</p>
                    </div>
                  </div>

                  <div class="payment-method-card">
                    <h3>Payment Method</h3>
                    <div class="payment-details">
                      <p><strong>{{ getPaymentMethodText(order.payment_method) }}</strong></p>
                      <p v-if="order.payment_status === 'paid'">
                        Paid on {{ formatDate(order.created_at) }}
                      </p>
                      <p v-else-if="order.payment_status === 'pending'">
                        Payment pending
                      </p>
                      <p v-if="order.stripe_payment_intent_id && order.payment_method !== 'cod'">
                        Transaction ID: {{ order.stripe_payment_intent_id }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Order Notes -->
              <div v-if="order.notes" class="notes-section">
                <h3>Order Notes</h3>
                <div class="notes-content">
                  <p>{{ order.notes }}</p>
                </div>
              </div>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="order-sidebar">
              <div class="summary-card">
                <h3>Order Summary</h3>
                
                <div class="summary-items">
                  <div class="summary-row">
                    <span>Subtotal</span>
                    <span>₹{{ order.subtotal }}</span>
                  </div>
                  <div class="summary-row">
                    <span>Shipping</span>
                    <span>₹{{ order.shipping_cost }}</span>
                  </div>
                  <div class="summary-row">
                    <span>Tax</span>
                    <span>₹{{ order.tax_amount }}</span>
                  </div>
                  <div class="summary-divider"></div>
                  <div class="summary-row grand-total">
                    <span>Total Amount</span>
                    <span>₹{{ order.total_amount }}</span>
                  </div>
                </div>
              </div>

              <!-- Order Actions -->
              <div class="actions-card">
                <h4>Order Actions</h4>
                <div class="action-buttons">
                  <button 
                    v-if="canReorderOrder"
                    class="btn btn-primary full-width"
                    @click="reorderAll"
                  >
                    Reorder All Items
                  </button>
                  <button 
                    class="btn btn-outline full-width"
                    @click="printInvoice"
                  >
                    Print Invoice
                  </button>
                  <router-link 
                    to="/contact"
                    class="btn btn-outline full-width"
                  >
                    Contact Support
                  </router-link>
                </div>
              </div>

              <!-- Customer Support -->
              <div class="support-card">
                <h4>Need Help?</h4>
                <p>Our customer service team is here to assist you.</p>
                <div class="support-contacts">
                  <div class="contact-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                      <path d="M22 16.92V19C22 19.5304 21.7893 20.0391 21.4142 20.4142C21.0391 20.7893 20.5304 21 20 21H4C3.46957 21 2.96086 20.7893 2.58579 20.4142C2.21071 20.0391 2 19.5304 2 19V16.92M22 16.92C21.674 16.945 21.347 16.96 21 16.96C17.134 16.96 13.57 15.51 10.889 13.111C8.208 10.712 6.573 7.468 6.134 4M22 16.92C21.329 13.92 19.532 11.354 17 9.75" 
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <div class="contact-info">
                      <strong>Call Us</strong>
                      <span>+91 XXX XXX XXXX</span>
                    </div>
                  </div>
                  <div class="contact-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                      <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" 
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M22 6L12 13L2 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <div class="contact-info">
                      <strong>Email Us</strong>
                      <span>support@pinders.com</span>
                    </div>
                  </div>
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
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useCartStore } from '@/store/cart'
import FrontendLayout from '@/layouts/FrontendLayout.vue'
import api from '@/services/api'

const route = useRoute()
const router = useRouter()
const cartStore = useCartStore()

// State
const loading = ref(true)
const order = ref(null)
const error = ref(null)

// Get order number from route params
const orderNumberParam = computed(() => route.params.orderNumber)

// Computed properties
const shippingAddress = computed(() => {
  if (!order.value || !order.value.shipping_address) return {}
  
  try {
    return typeof order.value.shipping_address === 'string' 
      ? JSON.parse(order.value.shipping_address)
      : order.value.shipping_address
  } catch (e) {
    console.error('Error parsing shipping address:', e)
    return {}
  }
})

const billingAddress = computed(() => {
  if (!order.value || !order.value.billing_address) return {}
  
  try {
    return typeof order.value.billing_address === 'string'
      ? JSON.parse(order.value.billing_address)
      : order.value.billing_address
  } catch (e) {
    console.error('Error parsing billing address:', e)
    return {}
  }
})

const shippingMethodName = computed(() => {
  const method = order.value?.shipping_method
  if (!method) return 'Standard Delivery'
  
  const methodMap = {
    'standard': 'Standard Delivery',
    'express': 'Express Delivery',
    'next-day': 'Next Day Delivery',
    'free': 'Free Delivery'
  }
  
  return methodMap[method] || method
})

const canReorderOrder = computed(() => {
  return order.value?.status === 'delivered' || order.value?.status === 'cancelled'
})

// Methods
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-GB', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getStatusText = (status) => {
  const statusMap = {
    pending: 'Pending',
    processing: 'Processing',
    shipped: 'Shipped',
    delivered: 'Delivered',
    cancelled: 'Cancelled',
    refunded: 'Refunded'
  }
  return statusMap[status] || status
}

const getPaymentStatusText = (paymentStatus) => {
  const statusMap = {
    pending: 'Pending',
    paid: 'Paid',
    failed: 'Failed',
    refunded: 'Refunded'
  }
  return statusMap[paymentStatus] || paymentStatus
}

const getPaymentMethodText = (paymentMethod) => {
  const methodMap = {
    'cod': 'Cash on Delivery',
    'card': 'Credit/Debit Card',
    'paypal': 'PayPal',
    'google-pay': 'Google Pay',
    'apple-pay': 'Apple Pay'
  }
  return methodMap[paymentMethod] || paymentMethod
}

const reorderItem = async (item) => {
  try {
    await cartStore.addToCart({
      product_id: item.product_id,
      variant_id: item.variant_id,
      size_id: item.size_id,
      quantity: item.quantity
    })
    router.push('/cart')
  } catch (error) {
    console.error('Failed to reorder item:', error)
  }
}

const reorderAll = async () => {
  try {
    for (const item of order.value.items) {
      await cartStore.addToCart({
        product_id: item.product_id,
        variant_id: item.variant_id,
        size_id: item.size_id,
        quantity: item.quantity
      })
    }
    router.push('/cart')
  } catch (error) {
    console.error('Failed to reorder all items:', error)
  }
}

const printInvoice = () => {
  window.print()
}

const fetchOrder = async () => {
  try {
    loading.value = true
    error.value = null
    
    const response = await api.get(`/frontend/orders/${orderNumberParam.value}`)
    
    if (response.data.status) {
      order.value = response.data.data
    } else {
      order.value = null
      error.value = response.data.message || 'Order not found'
    }
  } catch (err) {
    console.error('Failed to fetch order:', err)
    order.value = null
    error.value = err.message || 'Failed to load order details'
  } finally {
    loading.value = false
  }
}

// Lifecycle
onMounted(() => {
  fetchOrder()
})

// Watch for route changes
import { watch } from 'vue'
watch(orderNumberParam, () => {
  fetchOrder()
})
</script>

<style scoped>
.order-details-page {
  padding: 1.5rem 0 3rem;
  background: #f8fafc;
  min-height: 100vh;
}

.breadcrumb {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 1.5rem;
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

/* Loading State */
.loading-state {
  text-align: center;
  padding: 3rem 2rem;
  background: var(--primary-white);
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.loading-spinner {
  width: 32px;
  height: 32px;
  border: 3px solid var(--border-color);
  border-top: 3px solid var(--primary-red);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Not Found */
.not-found {
  text-align: center;
  padding: 3rem 2rem;
  background: var(--primary-white);
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.not-found-icon {
  width: 64px;
  height: 64px;
  margin: 0 auto 1rem;
  color: var(--gray-medium);
}

.not-found h3 {
  margin: 0 0 0.75rem 0;
  color: var(--primary-black);
  font-size: 1.25rem;
}

.not-found p {
  margin: 0 0 1.5rem 0;
  color: var(--gray-medium);
  font-size: 0.9rem;
}

/* Order Header */
.order-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1.5rem;
  padding: 1.5rem;
  background: var(--primary-white);
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.order-title h1 {
  margin: 0 0 0.5rem 0;
  color: var(--primary-black);
  font-size: 1.5rem;
}

.order-date {
  margin: 0;
  color: var(--gray-medium);
  font-size: 0.9rem;
}

.order-status-info {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  align-items: flex-end;
}

.status-badge, .payment-status-badge {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.status-badge.pending {
  background: #fef3c7;
  color: #92400e;
}

.status-badge.processing {
  background: #dbeafe;
  color: #1e40af;
}

.status-badge.shipped {
  background: #d1fae5;
  color: #065f46;
}

.status-badge.delivered {
  background: #dcfce7;
  color: #166534;
}

.status-badge.cancelled {
  background: #fecaca;
  color: #991b1b;
}

.status-badge.refunded {
  background: #f3e8ff;
  color: #6b21a8;
}

.payment-status-badge.pending {
  background: #fef3c7;
  color: #92400e;
}

.payment-status-badge.paid {
  background: #dcfce7;
  color: #166534;
}

.payment-status-badge.failed {
  background: #fecaca;
  color: #991b1b;
}

.payment-status-badge.refunded {
  background: #f3e8ff;
  color: #6b21a8;
}

/* Order Layout */
.order-layout {
  display: grid;
  grid-template-columns: 1fr 350px;
  gap: 1.5rem;
  align-items: start;
}

.order-main {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

/* Order Items Section */
.order-items-section {
  background: var(--primary-white);
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid var(--border-color);
  background: #fafafa;
}

.section-header h2 {
  margin: 0;
  color: var(--primary-black);
  font-size: 1.1rem;
}

.items-count {
  color: var(--gray-medium);
  font-size: 0.8rem;
}

.order-items-list {
  padding: 1.5rem;
}

.order-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 0;
  border-bottom: 1px solid var(--border-color);
}

.order-item:last-child {
  border-bottom: none;
}

.item-image {
  width: 80px;
  height: 80px;
  border-radius: 8px;
  overflow: hidden;
  flex-shrink: 0;
  background: #f1f5f9;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-details {
  flex: 1;
  min-width: 0;
}

.item-name {
  margin: 0 0 0.5rem 0;
  font-size: 1rem;
  font-weight: 600;
  color: var(--primary-black);
}

.item-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  margin-bottom: 0.5rem;
}

.item-variant, .item-size, .item-quantity {
  font-size: 0.8rem;
  color: var(--gray-medium);
  background: #f1f5f9;
  padding: 4px 8px;
  border-radius: 4px;
}

.item-price-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.item-unit-price {
  font-size: 0.85rem;
  color: var(--gray-medium);
}

.item-total-price {
  font-weight: 600;
  color: var(--primary-red);
  font-size: 1rem;
}

.item-actions {
  flex-shrink: 0;
}

.btn-sm {
  padding: 6px 12px;
  font-size: 0.8rem;
}

/* Address Section */
.address-section {
  background: var(--primary-white);
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.address-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  padding: 1.5rem;
}

.address-card,
.shipping-method-card,
.payment-method-card {
  padding: 1.25rem;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  background: #fafafa;
}

.address-card h3,
.shipping-method-card h3,
.payment-method-card h3 {
  margin: 0 0 1rem 0;
  color: var(--primary-black);
  font-size: 1rem;
}

.address-details p,
.shipping-details p,
.payment-details p {
  margin: 0.5rem 0;
  color: var(--gray-dark);
  font-size: 0.9rem;
}

.shipping-price {
  font-weight: 600;
  color: var(--primary-red);
}

/* Notes Section */
.notes-section {
  background: var(--primary-white);
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
}

.notes-section h3 {
  margin: 0 0 1rem 0;
  color: var(--primary-black);
  font-size: 1rem;
}

.notes-content {
  padding: 1rem;
  background: #f8fafc;
  border-radius: 6px;
  border: 1px solid var(--border-color);
}

.notes-content p {
  margin: 0;
  color: var(--gray-dark);
  font-size: 0.9rem;
  line-height: 1.5;
}

/* Sidebar */
.order-sidebar {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  position: sticky;
  top: 100px;
}

.summary-card,
.actions-card,
.support-card {
  background: var(--primary-white);
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
}

.summary-card h3,
.actions-card h4,
.support-card h4 {
  margin: 0 0 1.25rem 0;
  color: var(--primary-black);
  text-align: center;
  font-size: 1rem;
}

.summary-items {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: var(--gray-dark);
  font-size: 0.9rem;
}

.summary-row.grand-total {
  font-size: 1rem;
  font-weight: 700;
  color: var(--primary-black);
  padding-top: 0.5rem;
  border-top: 1px solid var(--border-color);
  margin-top: 0.5rem;
}

.summary-divider {
  height: 1px;
  background: var(--border-color);
  margin: 0.5rem 0;
}

/* Actions Card */
.action-buttons {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.full-width {
  width: 100%;
}

/* Support Card */
.support-card p {
  margin: 0 0 1.25rem 0;
  color: var(--gray-medium);
  text-align: center;
  font-size: 0.85rem;
}

.support-contacts {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem;
  background: #f8fafc;
  border-radius: 8px;
}

.contact-info {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.contact-info strong {
  color: var(--primary-black);
  font-size: 0.85rem;
}

.contact-info span {
  color: var(--gray-medium);
  font-size: 0.8rem;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .order-layout {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
  
  .order-sidebar {
    position: static;
  }
}

@media (max-width: 768px) {
  .order-header {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
  
  .order-status-info {
    flex-direction: row;
    width: 100%;
    justify-content: space-between;
  }
  
  .address-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .order-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .item-actions {
    align-self: stretch;
  }
  
  .item-actions .btn {
    width: 100%;
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .order-header,
  .order-items-section,
  .address-section,
  .notes-section {
    margin-left: -1rem;
    margin-right: -1rem;
    border-radius: 0;
  }
  
  .section-header {
    flex-direction: column;
    gap: 0.5rem;
    align-items: flex-start;
  }
}
</style>