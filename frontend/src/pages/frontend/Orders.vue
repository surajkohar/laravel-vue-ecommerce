<template>
  <FrontendLayout>
    <div class="order-history-page">
      <div class="container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
          <router-link to="/" class="breadcrumb-link">Home</router-link>
          <span class="breadcrumb-separator">/</span>
          <span class="breadcrumb-current">My Orders</span>
        </div>

        <!-- Page Header -->
        <div class="page-header">
          <div class="header-content">
            <h1>My Orders</h1>
            <p class="page-subtitle">View and manage your order history</p>
          </div>
          <div class="header-stats">
            <div class="stat-card">
              <div class="stat-value">{{ totalOrders }}</div>
              <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-card">
              <div class="stat-value">{{ activeOrders }}</div>
              <div class="stat-label">Active</div>
            </div>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <div class="loading-spinner"></div>
          <p>Loading your orders...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="orders.length === 0" class="empty-state">
          <div class="empty-icon">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none">
              <path d="M9 17H5C3.89543 17 3 16.1046 3 15V5C3 3.89543 3.89543 3 5 3H15C16.1046 3 17 3.89543 17 5V9M9 17L13 13M9 17L11 15M13 13L16 16M13 13L15 11M16 16L21 11M16 16L19 13M21 11L19 9M21 11L23 13" 
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <h3>No orders found</h3>
          <p>You haven't placed any orders yet</p>
          <router-link to="/products" class="btn btn-primary">
            Start Shopping
          </router-link>
        </div>

        <!-- Orders List -->
        <div v-else class="orders-section">
          <div class="orders-grid">
            <div 
              v-for="order in orders" 
              :key="order.id"
              class="order-card"
            >
              <!-- Order Header -->
              <div class="order-card-header">
                <div class="order-info">
                  <h3 class="order-number">
                    <router-link :to="`/account/orders/${order.order_number}`">
                      {{ order.order_number }}
                    </router-link>
                  </h3>
                  <p class="order-date">{{ formatDate(order.created_at) }}</p>
                </div>
                <div class="order-status" :class="order.status">
                  {{ getStatusText(order.status) }}
                </div>
              </div>

              <!-- Order Items -->
              <div class="order-items">
                <div 
                  v-for="item in order.items.slice(0, 2)" 
                  :key="item.id"
                  class="order-item"
                >
                  <div class="item-image">
                    <img :src="item.image_url || '/images/placeholder.jpg'" :alt="item.product_name" />
                  </div>
                  <div class="item-details">
                    <h4 class="item-name">{{ item.product_name }}</h4>
                    <div class="item-meta">
                      <span v-if="item.variant_name" class="item-variant">{{ item.variant_name }}</span>
                      <span v-if="item.size_title" class="item-size">Size: {{ item.size_title }}</span>
                    </div>
                    <p class="item-price">₹{{ item.unit_price }} × {{ item.quantity }}</p>
                  </div>
                </div>
                <div v-if="order.items.length > 2" class="more-items">
                  +{{ order.items.length - 2 }} more items
                </div>
              </div>

              <!-- Order Summary -->
              <div class="order-summary">
                <div class="summary-row">
                  <span>Items Total</span>
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
                <div class="summary-total">
                  <span>Total Amount</span>
                  <span>₹{{ order.total_amount }}</span>
                </div>
              </div>

              <!-- Payment Status -->
              <div class="payment-status">
                <span class="payment-label">Payment:</span>
                <span class="payment-value" :class="order.payment_status">
                  {{ getPaymentStatusText(order.payment_status) }}
                </span>
              </div>

              <!-- Order Actions -->
              <div class="order-actions">
                <router-link 
                  :to="`/account/orders/${order.order_number}`"
                  class="btn btn-outline btn-sm"
                >
                  View Details
                </router-link>
                
                <button 
                  v-if="canReorder(order)"
                  class="btn btn-primary btn-sm"
                  @click="reorder(order)"
                >
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                    <path d="M3 3H5L5.4 5M7 13H17L21 5H5.4M7 13L5.4 5M7 13L4.7 15.3C4.3 15.7 4.6 16.4 5.2 16.4H17M17 13V16.4M9 19C9 19.6 8.6 20 8 20C7.4 20 7 19.6 7 19C7 18.4 7.4 18 8 18C8.6 18 9 18.4 9 19ZM17 19C17 19.6 16.6 20 16 20C15.4 20 15 19.6 15 19C15 18.4 15.4 18 16 18C16.6 18 17 18.4 17 19Z" 
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  Reorder
                </button>
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
import { useRouter } from 'vue-router'
import { useCartStore } from '@/store/cart'
import FrontendLayout from '@/layouts/FrontendLayout.vue'
import api from '@/services/api'

const router = useRouter()
const cartStore = useCartStore()

// State
const loading = ref(true)
const orders = ref([])

// Computed properties
const totalOrders = computed(() => orders.value.length)

const activeOrders = computed(() => {
  return orders.value.filter(order => 
    ['pending', 'processing', 'shipped'].includes(order.status)
  ).length
})

// Methods
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-GB', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
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

const canReorder = (order) => {
  return order.status === 'delivered' || order.status === 'cancelled'
}

const reorder = async (order) => {
  try {
    for (const item of order.items) {
      await cartStore.addToCart({
        product_id: item.product_id,
        variant_id: item.variant_id,
        size_id: item.size_id,
        quantity: item.quantity
      })
    }
    router.push('/cart')
  } catch (error) {
    console.error('Failed to reorder:', error)
  }
}

const fetchOrders = async () => {
  try {
    loading.value = true
    const response = await api.get('/frontend/orders')
    
    if (response.data.status) {
      orders.value = response.data.data.data || response.data.data
    }
  } catch (error) {
    console.error('Failed to fetch orders:', error)
  } finally {
    loading.value = false
  }
}

// Lifecycle
onMounted(() => {
  fetchOrders()
})
</script>

<style scoped>
/* Add/Update these styles for your Orders page */

.order-history-page {
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

/* Page Header */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding: 1.5rem;
  background: var(--primary-white);
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.header-content h1 {
  margin: 0 0 0.25rem 0;
  color: var(--primary-black);
  font-size: 1.75rem;
}

.page-subtitle {
  margin: 0;
  color: var(--gray-medium);
  font-size: 1rem;
}

.header-stats {
  display: flex;
  gap: 1rem;
}

.stat-card {
  text-align: center;
  padding: 0.75rem 1rem;
  background: #f8fafc;
  border-radius: 8px;
  border: 1px solid var(--border-color);
  min-width: 80px;
}

.stat-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--primary-red);
  line-height: 1;
}

.stat-label {
  font-size: 0.8rem;
  color: var(--gray-medium);
  margin-top: 0.25rem;
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

/* Empty State */
.empty-state {
  text-align: center;
  padding: 3rem 2rem;
  background: var(--primary-white);
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.empty-icon {
  width: 64px;
  height: 64px;
  margin: 0 auto 1rem;
  color: var(--gray-medium);
}

.empty-state h3 {
  margin: 0 0 0.75rem 0;
  color: var(--primary-black);
  font-size: 1.25rem;
}

.empty-state p {
  margin: 0 0 1.5rem 0;
  color: var(--gray-medium);
  font-size: 0.9rem;
}

/* Orders Grid */
.orders-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
}

.order-card {
  background: var(--primary-white);
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 1.25rem;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  border: 1px solid transparent;
  height: fit-content;
}

.order-card:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  border-color: var(--primary-red);
}

.order-card-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid var(--border-color);
}

.order-number a {
  color: var(--primary-black);
  text-decoration: none;
  font-size: 1rem;
  font-weight: 600;
  transition: color 0.3s ease;
}

.order-number a:hover {
  color: var(--primary-red);
}

.order-date {
  margin: 0.25rem 0 0 0;
  color: var(--gray-medium);
  font-size: 0.8rem;
}

.order-status {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 0.7rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.order-status.pending {
  background: #fef3c7;
  color: #92400e;
}

.order-status.processing {
  background: #dbeafe;
  color: #1e40af;
}

.order-status.shipped {
  background: #d1fae5;
  color: #065f46;
}

.order-status.delivered {
  background: #dcfce7;
  color: #166534;
}

.order-status.cancelled {
  background: #fecaca;
  color: #991b1b;
}

.order-status.refunded {
  background: #f3e8ff;
  color: #6b21a8;
}

/* Order Items */
.order-items {
  margin-bottom: 1rem;
}

.order-item {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  padding: 0.5rem 0;
  border-bottom: 1px solid #f1f5f9;
}

.order-item:last-child {
  border-bottom: none;
}

.item-image {
  width: 50px;
  height: 50px;
  border-radius: 6px;
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
  margin: 0 0 0.25rem 0;
  font-size: 0.85rem;
  font-weight: 500;
  color: var(--primary-black);
  line-height: 1.3;
}

.item-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 0.25rem;
}

.item-variant, .item-size {
  font-size: 0.75rem;
  color: var(--gray-medium);
  background: #f1f5f9;
  padding: 2px 6px;
  border-radius: 4px;
}

.item-price {
  margin: 0;
  font-size: 0.8rem;
  color: var(--primary-red);
  font-weight: 500;
}

.more-items {
  text-align: center;
  padding: 0.5rem;
  color: var(--gray-medium);
  font-size: 0.8rem;
  font-style: italic;
  background: #f8fafc;
  border-radius: 4px;
  margin-top: 0.5rem;
}

/* Order Summary */
.order-summary {
  margin-bottom: 1rem;
  padding: 0.75rem;
  background: #f8fafc;
  border-radius: 6px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.25rem;
  font-size: 0.8rem;
  color: var(--gray-dark);
}

.summary-total {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 0.5rem;
  padding-top: 0.5rem;
  border-top: 1px solid var(--border-color);
  font-weight: 700;
  color: var(--primary-black);
  font-size: 0.9rem;
}

/* Payment Status */
.payment-status {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
  padding: 0.5rem;
  background: #f8fafc;
  border-radius: 6px;
  font-size: 0.8rem;
}

.payment-label {
  color: var(--gray-medium);
  font-weight: 500;
}

.payment-value {
  font-weight: 600;
  padding: 2px 6px;
  border-radius: 4px;
}

.payment-value.pending {
  background: #fef3c7;
  color: #92400e;
}

.payment-value.paid {
  background: #dcfce7;
  color: #166534;
}

.payment-value.failed {
  background: #fecaca;
  color: #991b1b;
}

.payment-value.refunded {
  background: #f3e8ff;
  color: #6b21a8;
}

/* Order Actions */
/* Make View Details button always visible */
.order-card .order-actions {
  display: flex !important;
  opacity: 1 !important;
  visibility: visible !important;
}

.order-card .btn.btn-outline.btn-sm {
  display: inline-flex !important;
  align-items: center;
  gap: 4px;
  padding: 6px 12px;
  font-size: 0.75rem;
  border: 1px solid var(--primary-red);
  color: var(--primary-red);
  background: transparent;
  border-radius: 4px;
  text-decoration: none;
  transition: all 0.3s ease;
}

.order-card .btn.btn-outline.btn-sm:hover {
  background: var(--primary-red);
  color: white;
}

.btn-sm {
  padding: 6px 10px;
  font-size: 0.75rem;
  display: flex;
  align-items: center;
  gap: 4px;
}

/* Responsive Design */
@media (max-width: 768px) {
  .page-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .header-stats {
    width: 100%;
    justify-content: space-around;
  }
  
  .orders-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .order-card-header {
    flex-direction: column;
    gap: 0.5rem;
    align-items: flex-start;
  }
  
  .order-actions {
    flex-direction: column;
  }
  
  .order-actions .btn {
    width: 100%;
    justify-content: center;
  }
}
</style>