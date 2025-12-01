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
          <span class="breadcrumb-current">Order #{{ order.number }}</span>
        </div>

        <!-- Order Header -->
        <div class="order-header">
          <div class="order-title">
            <h1>Order #{{ order.number }}</h1>
            <p class="order-date">Placed on {{ formatDate(order.created_at) }}</p>
          </div>
          <div class="order-actions">
            <button class="btn btn-outline" @click="printOrder">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path d="M6 9V3C6 2.44772 6.44772 2 7 2H17C17.5523 2 18 2.44772 18 3V9M6 9H3V21H21V9H18M6 9H18" 
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M8 7H16V5H8V7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M9 14H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M9 18H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              Print Invoice
            </button>
            <button 
              v-if="canReorder" 
              class="btn btn-primary" 
              @click="reorder"
            >
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path d="M3 3H5L5.4 5M7 13H17L21 5H5.4M7 13L5.4 5M7 13L4.7 15.3C4.3 15.7 4.6 16.4 5.2 16.4H17M17 13V16.4M9 19C9 19.6 8.6 20 8 20C7.4 20 7 19.6 7 19C7 18.4 7.4 18 8 18C8.6 18 9 18.4 9 19ZM17 19C17 19.6 16.6 20 16 20C15.4 20 15 19.6 15 19C15 18.4 15.4 18 16 18C16.6 18 17 18.4 17 19Z" 
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              Reorder All
            </button>
          </div>
        </div>

        <div class="order-layout">
          <!-- Main Content -->
          <div class="order-main">
            <!-- Order Status & Tracking -->
            <div class="tracking-section">
              <div class="section-header">
                <h2>Order Status & Tracking</h2>
                <div class="order-status" :class="order.status">
                  {{ getStatusText(order.status) }}
                </div>
              </div>

              <!-- Tracking Progress -->
              <div class="tracking-progress">
                <div class="progress-steps">
                  <div 
                    v-for="step in trackingSteps" 
                    :key="step.status"
                    class="progress-step"
                    :class="{ 
                      completed: isStepCompleted(step.status),
                      current: isStepCurrent(step.status),
                      upcoming: isStepUpcoming(step.status)
                    }"
                  >
                    <div class="step-icon">
                      <component :is="step.icon" />
                    </div>
                    <div class="step-info">
                      <div class="step-title">{{ step.title }}</div>
                      <div class="step-description">{{ step.description }}</div>
                      <div class="step-date" v-if="getStepDate(step.status)">
                        {{ formatStepDate(getStepDate(step.status)) }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Tracking Details -->
              <div class="tracking-details" v-if="order.tracking_number">
                <div class="tracking-info">
                  <div class="tracking-number">
                    <strong>Tracking Number:</strong>
                    <span>{{ order.tracking_number }}</span>
                    <button class="copy-btn" @click="copyTrackingNumber" title="Copy tracking number">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M8 16H6C5.46957 16 4.96086 15.7893 4.58579 15.4142C4.21071 15.0391 4 14.5304 4 14V6C4 5.46957 4.21071 4.96086 4.58579 4.58579C4.96086 4.21071 5.46957 4 6 4H14C14.5304 4 15.0391 4.21071 15.4142 4.58579C15.7893 4.96086 16 5.46957 16 6V8M8 16C8 16.5304 8.21071 17.0391 8.58579 17.4142C8.96086 17.7893 9.46957 18 10 18H18C18.5304 18 19.0391 17.7893 19.4142 17.4142C19.7893 17.0391 20 16.5304 20 16V10C20 9.46957 19.7893 8.96086 19.4142 8.58579C19.0391 8.21071 18.5304 8 18 8H10C9.46957 8 8.96086 8.21071 8.58579 8.58579C8.21071 8.96086 8 9.46957 8 10V16Z" 
                              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                    </button>
                  </div>
                  <div class="carrier">
                    <strong>Carrier:</strong>
                    <span>{{ order.carrier }}</span>
                  </div>
                  <a 
                    :href="order.tracking_url" 
                    target="_blank" 
                    class="tracking-link"
                    v-if="order.tracking_url"
                  >
                    Track on {{ order.carrier }} website
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                      <path d="M18 13V19C18 19.5304 17.7893 20.0391 17.4142 20.4142C17.0391 20.7893 16.5304 21 16 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V8C3 7.46957 3.21071 6.96086 3.58579 6.58579C3.96086 6.21071 4.46957 6 5 6H11" 
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M15 3H21V9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M10 14L21 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </a>
                </div>
              </div>
            </div>

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
                    <img :src="item.image" :alt="item.name" />
                  </div>
                  <div class="item-details">
                    <h3 class="item-name">{{ item.name }}</h3>
                    <div class="item-meta">
                      <span class="item-sku" v-if="item.sku">SKU: {{ item.sku }}</span>
                      <span class="item-variant" v-if="item.variant">{{ item.variant }}</span>
                    </div>
                    <div class="item-price">£{{ item.price }}</div>
                  </div>
                  <div class="item-quantity">
                    <span class="quantity-label">Quantity:</span>
                    <span class="quantity">{{ item.quantity }}</span>
                  </div>
                  <div class="item-total">
                    £{{ (item.price * item.quantity).toFixed(2) }}
                  </div>
                  <div class="item-actions">
                    <button 
                      class="btn btn-outline btn-sm"
                      @click="reorderItem(item)"
                      :disabled="!item.in_stock"
                    >
                      Reorder
                    </button>
                    <router-link 
                      :to="`/product/${item.product_id}`" 
                      class="btn btn-outline btn-sm"
                    >
                      View Product
                    </router-link>
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
                    <p><strong>{{ order.shipping_address.name }}</strong></p>
                    <p>{{ order.shipping_address.address1 }}</p>
                    <p v-if="order.shipping_address.address2">{{ order.shipping_address.address2 }}</p>
                    <p>{{ order.shipping_address.city }}, {{ order.shipping_address.postcode }}</p>
                    <p>{{ order.shipping_address.country }}</p>
                    <p v-if="order.shipping_address.phone">
                      <strong>Phone:</strong> {{ order.shipping_address.phone }}
                    </p>
                  </div>
                </div>

                <div class="address-card">
                  <h3>Billing Address</h3>
                  <div class="address-details">
                    <p><strong>{{ order.billing_address.name }}</strong></p>
                    <p>{{ order.billing_address.address1 }}</p>
                    <p v-if="order.billing_address.address2">{{ order.billing_address.address2 }}</p>
                    <p>{{ order.billing_address.city }}, {{ order.billing_address.postcode }}</p>
                    <p>{{ order.billing_address.country }}</p>
                  </div>
                </div>

                <div class="shipping-method-card">
                  <h3>Shipping Method</h3>
                  <div class="shipping-details">
                    <p><strong>{{ order.shipping_method.name }}</strong></p>
                    <p>{{ order.shipping_method.description }}</p>
                    <p class="shipping-price">£{{ order.shipping_method.price }}</p>
                  </div>
                </div>

                <div class="payment-method-card">
                  <h3>Payment Method</h3>
                  <div class="payment-details">
                    <p><strong>{{ order.payment_method.name }}</strong></p>
                    <p>Ending in {{ order.payment_method.last4 }}</p>
                    <p>Paid on {{ formatDate(order.created_at) }}</p>
                  </div>
                </div>
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
                  <span>£{{ order.subtotal }}</span>
                </div>
                <div class="summary-row">
                  <span>Shipping</span>
                  <span>£{{ order.shipping_cost }}</span>
                </div>
                <div class="summary-row">
                  <span>VAT (20%)</span>
                  <span>£{{ order.tax_amount }}</span>
                </div>
                <div class="summary-row discount" v-if="order.discount_amount > 0">
                  <span>Discount</span>
                  <span>-£{{ order.discount_amount }}</span>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-row grand-total">
                  <span>Total</span>
                  <span>£{{ order.total }}</span>
                </div>
              </div>
            </div>

            <!-- Customer Support -->
            <div class="support-card">
              <h4>Need Help with Your Order?</h4>
              <p>Our customer service team is here to assist you.</p>
              <div class="support-contacts">
                <div class="contact-item">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M22 16.92V19C22 19.5304 21.7893 20.0391 21.4142 20.4142C21.0391 20.7893 20.5304 21 20 21H4C3.46957 21 2.96086 20.7893 2.58579 20.4142C2.21071 20.0391 2 19.5304 2 19V16.92M22 16.92C21.674 16.945 21.347 16.96 21 16.96C17.134 16.96 13.57 15.51 10.889 13.111C8.208 10.712 6.573 7.468 6.134 4M22 16.92C21.329 13.92 19.532 11.354 17 9.75" 
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  <div class="contact-info">
                    <strong>Call Us</strong>
                    <span>0800 298 9230</span>
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
              <button class="btn btn-outline full-width" @click="contactSupport">
                Contact Support
              </button>
            </div>

            <!-- Order Actions -->
            <div class="actions-card" v-if="showOrderActions">
              <h4>Order Actions</h4>
              <div class="action-buttons">
                <button 
                  v-if="canCancel" 
                  class="btn btn-outline full-width btn-cancel"
                  @click="cancelOrder"
                >
                  Cancel Order
                </button>
                <button 
                  v-if="canReturn" 
                  class="btn btn-outline full-width"
                  @click="startReturn"
                >
                  Start Return
                </button>
                <button 
                  v-if="canTrack" 
                  class="btn btn-outline full-width"
                  @click="trackOrder"
                >
                  Track Package
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
import { useRoute, useRouter } from 'vue-router'
import { useCartStore } from '@/store/cart'
import FrontendLayout from '@/layouts/FrontendLayout.vue'

const route = useRoute()
const router = useRouter()
const cartStore = useCartStore()

// Icons for tracking steps
const OrderPlacedIcon = {
  template: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none">
    <path d="M9 11L12 14L22 4M21 12V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H16" 
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
  </svg>`
}

const ProcessingIcon = {
  template: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none">
    <path d="M12 2V6M12 18V22M6 12H2M22 12H18M19.07 4.93L16.24 7.76M7.76 16.24L4.93 19.07M19.07 19.07L16.24 16.24M7.76 7.76L4.93 4.93" 
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
  </svg>`
}

const ShippedIcon = {
  template: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none">
    <path d="M9 17H5C3.89543 17 3 16.1046 3 15V5C3 3.89543 3.89543 3 5 3H15C16.1046 3 17 3.89543 17 5V9M9 17L13 13M9 17L11 15M13 13L16 16M13 13L15 11M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" 
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
  </svg>`
}

const OutForDeliveryIcon = {
  template: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none">
    <path d="M9 17H5C3.89543 17 3 16.1046 3 15V5C3 3.89543 3.89543 3 5 3H15C16.1046 3 17 3.89543 17 5V9M9 17L13 13M9 17L11 15M13 13L16 16M13 13L15 11M16 16L21 11M16 16L19 13M21 11L19 9M21 11L23 13" 
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
  </svg>`
}

const DeliveredIcon = {
  template: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none">
    <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" 
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
  </svg>`
}

// Mock order data
const order = ref({
  id: 1,
  number: 'PIND-2024-001',
  created_at: '2024-01-15T10:30:00Z',
  status: 'shipped',
  subtotal: 139.98,
  shipping_cost: 4.99,
  tax_amount: 28.99,
  discount_amount: 0,
  total: 173.96,
  tracking_number: 'TRK123456789GB',
  carrier: 'Royal Mail',
  tracking_url: 'https://royalmail.com/track/TRK123456789GB',
  items: [
    {
      id: 1,
      product_id: 101,
      name: 'Professional Work Jacket',
      sku: 'PWJ-2024-BLK-M',
      variant: 'Black / M',
      price: 89.99,
      quantity: 1,
      image: '/images/products/jacket.jpg',
      in_stock: true
    },
    {
      id: 2,
      product_id: 102,
      name: 'Work Trousers',
      sku: 'WT-2024-BLK-32',
      variant: 'Black / 32',
      price: 49.99,
      quantity: 1,
      image: '/images/products/trousers.jpg',
      in_stock: true
    }
  ],
  shipping_address: {
    name: 'John Doe',
    address1: '123 Workwear Street',
    address2: 'Unit 4',
    city: 'Manchester',
    postcode: 'M1 1AB',
    country: 'United Kingdom',
    phone: '+44 1234 567890'
  },
  billing_address: {
    name: 'John Doe',
    address1: '123 Workwear Street',
    address2: 'Unit 4',
    city: 'Manchester',
    postcode: 'M1 1AB',
    country: 'United Kingdom'
  },
  shipping_method: {
    name: 'Standard Delivery',
    description: '3-5 business days',
    price: 4.99
  },
  payment_method: {
    name: 'Visa Credit Card',
    last4: '4242'
  },
  timeline: [
    { status: 'placed', date: '2024-01-15T10:30:00Z' },
    { status: 'confirmed', date: '2024-01-15T11:15:00Z' },
    { status: 'processing', date: '2024-01-16T09:00:00Z' },
    { status: 'shipped', date: '2024-01-17T14:30:00Z' }
  ]
})

const trackingSteps = ref([
  {
    status: 'placed',
    title: 'Order Placed',
    description: 'We\'ve received your order',
    icon: OrderPlacedIcon
  },
  {
    status: 'confirmed',
    title: 'Order Confirmed',
    description: 'We\'ve confirmed your order',
    icon: OrderPlacedIcon
  },
  {
    status: 'processing',
    title: 'Processing',
    description: 'We\'re preparing your order',
    icon: ProcessingIcon
  },
  {
    status: 'shipped',
    title: 'Shipped',
    description: 'Your order is on the way',
    icon: ShippedIcon
  },
  {
    status: 'out_for_delivery',
    title: 'Out for Delivery',
    description: 'Your order will arrive today',
    icon: OutForDeliveryIcon
  },
  {
    status: 'delivered',
    title: 'Delivered',
    description: 'Your order has been delivered',
    icon: DeliveredIcon
  }
])

// Computed properties
const canReorder = computed(() => {
  return order.value.status === 'delivered' || order.value.status === 'cancelled'
})

const showOrderActions = computed(() => {
  return ['placed', 'confirmed', 'processing', 'shipped'].includes(order.value.status)
})

const canCancel = computed(() => {
  return ['placed', 'confirmed', 'processing'].includes(order.value.status)
})

const canReturn = computed(() => {
  return order.value.status === 'delivered'
})

const canTrack = computed(() => {
  return ['shipped', 'out_for_delivery'].includes(order.value.status)
})

// Methods
const getStatusText = (status) => {
  const statusMap = {
    placed: 'Order Placed',
    confirmed: 'Order Confirmed',
    processing: 'Processing',
    shipped: 'Shipped',
    out_for_delivery: 'Out for Delivery',
    delivered: 'Delivered',
    cancelled: 'Cancelled'
  }
  return statusMap[status] || status
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-GB', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatStepDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-GB', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const isStepCompleted = (stepStatus) => {
  const statusOrder = ['placed', 'confirmed', 'processing', 'shipped', 'out_for_delivery', 'delivered']
  const currentIndex = statusOrder.indexOf(order.value.status)
  const stepIndex = statusOrder.indexOf(stepStatus)
  return stepIndex <= currentIndex
}

const isStepCurrent = (stepStatus) => {
  return order.value.status === stepStatus
}

const isStepUpcoming = (stepStatus) => {
  const statusOrder = ['placed', 'confirmed', 'processing', 'shipped', 'out_for_delivery', 'delivered']
  const currentIndex = statusOrder.indexOf(order.value.status)
  const stepIndex = statusOrder.indexOf(stepStatus)
  return stepIndex > currentIndex
}

const getStepDate = (stepStatus) => {
  const timelineEvent = order.value.timeline.find(event => event.status === stepStatus)
  return timelineEvent ? timelineEvent.date : null
}

const copyTrackingNumber = async () => {
  try {
    await navigator.clipboard.writeText(order.value.tracking_number)
    alert('Tracking number copied to clipboard!')
  } catch (err) {
    console.error('Failed to copy tracking number:', err)
  }
}

const printOrder = () => {
  window.print()
}

const reorder = () => {
  order.value.items.forEach(item => {
    if (item.in_stock) {
      cartStore.addToCart({
        product_id: item.product_id,
        quantity: item.quantity,
        product: item
      })
    }
  })
  router.push('/cart')
}

const reorderItem = (item) => {
  if (item.in_stock) {
    cartStore.addToCart({
      product_id: item.product_id,
      quantity: item.quantity,
      product: item
    })
    router.push('/cart')
  }
}

const cancelOrder = () => {
  if (confirm('Are you sure you want to cancel this order?')) {
    // Implement cancel order logic
    console.log('Cancel order:', order.value.id)
  }
}

const startReturn = () => {
  // Implement return logic
  console.log('Start return for order:', order.value.id)
}

const trackOrder = () => {
  if (order.value.tracking_url) {
    window.open(order.value.tracking_url, '_blank')
  }
}

const contactSupport = () => {
  // Implement contact support logic
  console.log('Contact support for order:', order.value.id)
}

// Lifecycle
onMounted(() => {
  // In real app, fetch order by ID from route params
  // const orderId = route.params.id
  // fetchOrder(orderId)
})
</script>

<style scoped src="@/assets/styles/frontend.css"></style>

<style scoped>
.order-details-page {
  padding: 2rem 0 4rem;
  background: linear-gradient(135deg, var(--gray-light) 0%, #f1f5f9 100%);
  min-height: 100vh;
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

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 2rem;
  padding: 2rem;
  background: var(--primary-white);
  border-radius: 12px;
  box-shadow: var(--shadow-light);
}

.order-title h1 {
  margin: 0 0 0.5rem 0;
  color: var(--primary-black);
  font-size: 2rem;
}

.order-date {
  margin: 0;
  color: var(--gray-medium);
  font-size: 1.1rem;
}

.order-actions {
  display: flex;
  gap: 1rem;
}

.order-layout {
  display: grid;
  grid-template-columns: 1fr 400px;
  gap: 2rem;
  align-items: start;
}

.order-main {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

/* Tracking Section */
.tracking-section {
  background: var(--primary-white);
  border-radius: 12px;
  box-shadow: var(--shadow-light);
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
  font-size: 1.3rem;
}

.order-status {
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
  text-transform: uppercase;
}

.order-status.placed {
  background: #fef3c7;
  color: #92400e;
}

.order-status.confirmed {
  background: #dbeafe;
  color: #1e40af;
}

.order-status.processing {
  background: #ddd6fe;
  color: #5b21b6;
}

.order-status.shipped {
  background: #d1fae5;
  color: #065f46;
}

.order-status.out_for_delivery {
  background: #f0fdf4;
  color: #166534;
}

.order-status.delivered {
  background: #dcfce7;
  color: #166534;
}

.order-status.cancelled {
  background: #fecaca;
  color: #991b1b;
}

/* Tracking Progress */
.tracking-progress {
  padding: 2rem;
}

.progress-steps {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.progress-step {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  padding: 1rem;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.progress-step.completed {
  background: #f0fdf4;
  border-left: 4px solid #22c55e;
}

.progress-step.current {
  background: #fef3c7;
  border-left: 4px solid #f59e0b;
}

.progress-step.upcoming {
  background: #f8fafc;
  border-left: 4px solid var(--border-color);
  opacity: 0.7;
}

.step-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.progress-step.completed .step-icon {
  background: #22c55e;
  color: white;
}

.progress-step.current .step-icon {
  background: #f59e0b;
  color: white;
}

.progress-step.upcoming .step-icon {
  background: var(--border-color);
  color: var(--gray-medium);
}

.step-info {
  flex: 1;
}

.step-title {
  font-weight: 600;
  color: var(--primary-black);
  margin-bottom: 0.25rem;
}

.step-description {
  color: var(--gray-medium);
  font-size: 0.9rem;
  margin-bottom: 0.25rem;
}

.step-date {
  font-size: 0.8rem;
  color: var(--gray-medium);
  font-style: italic;
}

/* Tracking Details */
.tracking-details {
  padding: 1.5rem 2rem;
  background: #f8fafc;
  border-top: 1px solid var(--border-color);
}

.tracking-info {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.tracking-number,
.carrier {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.95rem;
}

.copy-btn {
  background: none;
  border: none;
  color: var(--primary-red);
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: background-color 0.3s ease;
}

.copy-btn:hover {
  background: #fee;
}

.tracking-link {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--primary-red);
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s ease;
}

.tracking-link:hover {
  color: #c00;
}

/* Order Items */
.order-items-section {
  background: var(--primary-white);
  border-radius: 12px;
  box-shadow: var(--shadow-light);
  overflow: hidden;
}

.items-count {
  color: var(--gray-medium);
  font-size: 0.9rem;
}

.order-items-list {
  padding: 0 2rem 2rem;
}

.order-item {
  display: grid;
  grid-template-columns: 80px 1fr auto auto auto;
  gap: 1rem;
  align-items: center;
  padding: 1.5rem 0;
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
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-details {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.item-name {
  margin: 0;
  font-size: 1rem;
  font-weight: 600;
  color: var(--primary-black);
}

.item-meta {
  display: flex;
  gap: 1rem;
  font-size: 0.85rem;
  color: var(--gray-medium);
}

.item-price {
  font-weight: 600;
  color: var(--primary-red);
  font-size: 1.1rem;
}

.item-quantity {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.25rem;
}

.quantity-label {
  font-size: 0.8rem;
  color: var(--gray-medium);
}

.quantity {
  font-weight: 600;
  color: var(--primary-black);
}

.item-total {
  font-weight: 700;
  color: var(--primary-black);
  font-size: 1.1rem;
}

.item-actions {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.btn-sm {
  padding: 6px 12px;
  font-size: 0.8rem;
}

/* Address Section */
.address-section {
  background: var(--primary-white);
  border-radius: 12px;
  box-shadow: var(--shadow-light);
  overflow: hidden;
}

.address-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
  padding: 2rem;
}

.address-card,
.shipping-method-card,
.payment-method-card {
  padding: 1.5rem;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  background: #fafafa;
}

.address-card h3,
.shipping-method-card h3,
.payment-method-card h3 {
  margin: 0 0 1rem 0;
  color: var(--primary-black);
  font-size: 1.1rem;
}

.address-details p,
.shipping-details p,
.payment-details p {
  margin: 0.5rem 0;
  color: var(--gray-dark);
}

.shipping-price {
  font-weight: 600;
  color: var(--primary-red);
}

/* Sidebar */
.order-sidebar {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  position: sticky;
  top: 120px;
}

.summary-card,
.support-card,
.actions-card {
  background: var(--primary-white);
  border-radius: 12px;
  box-shadow: var(--shadow-light);
  padding: 1.5rem;
}

.summary-card h3,
.support-card h4,
.actions-card h4 {
  margin: 0 0 1.5rem 0;
  color: var(--primary-black);
  text-align: center;
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
}

.summary-row.discount {
  color: #22c55e;
}

.summary-row.grand-total {
  font-size: 1.2rem;
  font-weight: 700;
  color: var(--primary-black);
}

.summary-divider {
  height: 1px;
  background: var(--border-color);
  margin: 0.5rem 0;
}

/* Support Card */
.support-card p {
  margin: 0 0 1.5rem 0;
  color: var(--gray-medium);
  text-align: center;
  font-size: 0.9rem;
}

.support-contacts {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
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
  font-size: 0.9rem;
}

.contact-info span {
  color: var(--gray-medium);
  font-size: 0.85rem;
}

.full-width {
  width: 100%;
}

/* Actions Card */
.action-buttons {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.btn-cancel {
  color: #ef4444;
  border-color: #ef4444;
}

.btn-cancel:hover {
  background: #ef4444;
  color: var(--primary-white);
}

/* Responsive Design */
@media (max-width: 1024px) {
  .order-layout {
    grid-template-columns: 1fr;
    gap: 2rem;
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
  
  .order-actions {
    width: 100%;
    justify-content: space-between;
  }
  
  .address-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .order-item {
    grid-template-columns: 60px 1fr;
    gap: 1rem;
  }
  
  .item-quantity,
  .item-total,
  .item-actions {
    grid-column: 1 / -1;
    justify-self: start;
  }
  
  .item-actions {
    flex-direction: row;
    width: 100%;
    justify-content: space-between;
  }
}

@media (max-width: 480px) {
  .order-header,
  .tracking-section,
  .order-items-section,
  .address-section {
    margin-left: -1rem;
    margin-right: -1rem;
    border-radius: 0;
  }
  
  .section-header {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
  
  .progress-step {
    flex-direction: column;
    text-align: center;
    gap: 0.75rem;
  }
  
  .step-icon {
    align-self: center;
  }
}
</style>