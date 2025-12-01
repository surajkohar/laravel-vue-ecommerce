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

        <!-- Filters & Search -->
        <div class="filters-section">
          <div class="filters-grid">
            <!-- Search -->
            <div class="search-box">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="search-icon">
                <path d="M21 21L16.514 16.506M19 10.5C19 15.194 15.194 19 10.5 19C5.806 19 2 15.194 2 10.5C2 5.806 5.806 2 10.5 2C15.194 2 19 5.806 19 10.5Z" 
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <input 
                type="text" 
                v-model="searchQuery"
                placeholder="Search orders..."
                class="search-input"
              >
            </div>

            <!-- Status Filter -->
            <div class="filter-group">
              <label class="filter-label">Status</label>
              <v-select
                v-model="statusFilter"
                :options="statusOptions"
                :clearable="false"
                placeholder="All Statuses"
                class="v-select-custom"
              ></v-select>
            </div>

            <!-- Date Filter -->
            <div class="filter-group">
              <label class="filter-label">Date Range</label>
              <v-select
                v-model="dateFilter"
                :options="dateOptions"
                :clearable="false"
                placeholder="All Time"
                class="v-select-custom"
              ></v-select>
            </div>

            <!-- Sort -->
            <div class="filter-group">
              <label class="filter-label">Sort By</label>
              <v-select
                v-model="sortBy"
                :options="sortOptions"
                :clearable="false"
                class="v-select-custom"
              ></v-select>
            </div>

            <!-- Clear Filters -->
            <button 
              class="btn btn-outline clear-filters"
              @click="clearFilters"
              :disabled="!hasActiveFilters"
            >
              Clear All
            </button>
          </div>
        </div>

        <!-- Orders List -->
        <div class="orders-section">
          <!-- Loading State -->
          <div v-if="loading" class="loading-state">
            <div class="loading-spinner"></div>
            <p>Loading your orders...</p>
          </div>

          <!-- Empty State -->
          <div v-else-if="filteredOrders.length === 0" class="empty-state">
            <div class="empty-icon">
              <svg width="64" height="64" viewBox="0 0 24 24" fill="none">
                <path d="M9 17H5C3.89543 17 3 16.1046 3 15V5C3 3.89543 3.89543 3 5 3H15C16.1046 3 17 3.89543 17 5V9M9 17L13 13M9 17L11 15M13 13L16 16M13 13L15 11M16 16L21 11M16 16L19 13M21 11L19 9M21 11L23 13" 
                      stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <h3>No orders found</h3>
            <p v-if="hasActiveFilters">
              Try adjusting your filters or search terms
            </p>
            <p v-else>
              You haven't placed any orders yet
            </p>
            <router-link to="/products" class="btn btn-primary">
              Start Shopping
            </router-link>
          </div>

          <!-- Orders Grid -->
          <div v-else class="orders-grid">
            <div 
              v-for="order in paginatedOrders" 
              :key="order.id"
              class="order-card"
            >
              <!-- Order Header -->
              <div class="order-card-header">
                <div class="order-info">
                  <h3 class="order-number">
                    <router-link :to="`/account/orders/${order.id}`">
                      #{{ order.number }}
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
                    <img :src="item.image" :alt="item.name" />
                  </div>
                  <div class="item-details">
                    <h4 class="item-name">{{ item.name }}</h4>
                    <p class="item-meta">Qty: {{ item.quantity }} • £{{ item.price }}</p>
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
                  <span>£{{ order.subtotal }}</span>
                </div>
                <div class="summary-row">
                  <span>Shipping</span>
                  <span>£{{ order.shipping_cost }}</span>
                </div>
                <div v-if="order.discount_amount > 0" class="summary-row discount">
                  <span>Discount</span>
                  <span>-£{{ order.discount_amount }}</span>
                </div>
                <div class="summary-total">
                  <span>Total Paid</span>
                  <span>£{{ order.total }}</span>
                </div>
              </div>

              <!-- Order Actions -->
              <div class="order-actions">
                <router-link 
                  :to="`/account/orders/${order.id}`"
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

                <button 
                  v-if="canTrack(order)"
                  class="btn btn-outline btn-sm"
                  @click="trackOrder(order)"
                >
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2V6M12 18V22M6 12H2M22 12H18M19.07 4.93L16.24 7.76M7.76 16.24L4.93 19.07M19.07 19.07L16.24 16.24M7.76 7.76L4.93 4.93" 
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  Track
                </button>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="totalPages > 1" class="pagination">
            <button 
              class="pagination-btn"
              :disabled="currentPage === 1"
              @click="changePage(currentPage - 1)"
            >
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path d="M15 18L9 12L15 6" 
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              Previous
            </button>

            <div class="pagination-pages">
              <button 
                v-for="page in visiblePages" 
                :key="page"
                class="pagination-page"
                :class="{ active: page === currentPage }"
                @click="changePage(page)"
              >
                {{ page }}
              </button>
            </div>

            <button 
              class="pagination-btn"
              :disabled="currentPage === totalPages"
              @click="changePage(currentPage + 1)"
            >
              Next
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path d="M9 18L15 12L9 6" 
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Quick Stats -->
        <div class="stats-section">
          <h2>Order Summary</h2>
          <div class="stats-grid">
            <div class="stat-item">
              <div class="stat-icon delivered">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" 
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <div class="stat-content">
                <div class="stat-number">{{ stats.delivered }}</div>
                <div class="stat-label">Delivered</div>
              </div>
            </div>

            <div class="stat-item">
              <div class="stat-icon processing">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <path d="M12 2V6M12 18V22M6 12H2M22 12H18M19.07 4.93L16.24 7.76M7.76 16.24L4.93 19.07M19.07 19.07L16.24 16.24M7.76 7.76L4.93 4.93" 
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <div class="stat-content">
                <div class="stat-number">{{ stats.processing }}</div>
                <div class="stat-label">Processing</div>
              </div>
            </div>

            <div class="stat-item">
              <div class="stat-icon shipped">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <path d="M9 17H5C3.89543 17 3 16.1046 3 15V5C3 3.89543 3.89543 3 5 3H15C16.1046 3 17 3.89543 17 5V9M9 17L13 13M9 17L11 15M13 13L16 16M13 13L15 11M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" 
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <div class="stat-content">
                <div class="stat-number">{{ stats.shipped }}</div>
                <div class="stat-label">Shipped</div>
              </div>
            </div>

            <div class="stat-item">
              <div class="stat-icon cancelled">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                  <path d="M18 6L6 18M6 6L18 18" 
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <div class="stat-content">
                <div class="stat-number">{{ stats.cancelled }}</div>
                <div class="stat-label">Cancelled</div>
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
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'

const router = useRouter()
const cartStore = useCartStore()

// State
const loading = ref(true)
const searchQuery = ref('')
const statusFilter = ref('')
const dateFilter = ref('')
const sortBy = ref('newest')
const currentPage = ref(1)
const ordersPerPage = 8

// Select Options
const statusOptions = [
  { label: 'All Statuses', value: '' },
  { label: 'Order Placed', value: 'placed' },
  { label: 'Order Confirmed', value: 'confirmed' },
  { label: 'Processing', value: 'processing' },
  { label: 'Shipped', value: 'shipped' },
  { label: 'Out for Delivery', value: 'out_for_delivery' },
  { label: 'Delivered', value: 'delivered' },
  { label: 'Cancelled', value: 'cancelled' }
]

const dateOptions = [
  { label: 'All Time', value: '' },
  { label: 'Last 7 Days', value: '7' },
  { label: 'Last 30 Days', value: '30' },
  { label: 'Last 3 Months', value: '90' },
  { label: 'Last Year', value: '365' }
]

const sortOptions = [
  { label: 'Newest First', value: 'newest' },
  { label: 'Oldest First', value: 'oldest' },
  { label: 'Total: High to Low', value: 'total_high' },
  { label: 'Total: Low to High', value: 'total_low' }
]

// Mock orders data with dummy images
const orders = ref([
  {
    id: 1,
    number: 'PIND-2024-001',
    created_at: '2024-01-15T10:30:00Z',
    status: 'shipped',
    subtotal: 139.98,
    shipping_cost: 4.99,
    discount_amount: 0,
    total: 173.96,
    tracking_number: 'TRK123456789GB',
    tracking_url: 'https://royalmail.com/track/TRK123456789GB',
    items: [
      {
        id: 1,
        name: 'Professional Work Jacket',
        image: 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=150&h=150&fit=crop',
        price: 89.99,
        quantity: 1
      },
      {
        id: 2,
        name: 'Work Trousers',
        image: 'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?w=150&h=150&fit=crop',
        price: 49.99,
        quantity: 1
      }
    ]
  },
  {
    id: 2,
    number: 'PIND-2023-156',
    created_at: '2023-12-20T14:22:00Z',
    status: 'delivered',
    subtotal: 89.99,
    shipping_cost: 0,
    discount_amount: 10.00,
    total: 95.99,
    items: [
      {
        id: 3,
        name: 'Safety Boots',
        image: 'https://images.unsplash.com/photo-1542280756-74b2f55e73ab?w=150&h=150&fit=crop',
        price: 89.99,
        quantity: 1
      }
    ]
  },
  {
    id: 3,
    number: 'PIND-2023-142',
    created_at: '2023-11-05T09:15:00Z',
    status: 'delivered',
    subtotal: 245.97,
    shipping_cost: 4.99,
    discount_amount: 0,
    total: 300.95,
    items: [
      {
        id: 4,
        name: 'Winter Work Coat',
        image: 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=150&h=150&fit=crop',
        price: 189.99,
        quantity: 1
      },
      {
        id: 5,
        name: 'Thermal Gloves',
        image: 'https://images.unsplash.com/photo-1584739556325-4c9e9cf2d75f?w=150&h=150&fit=crop',
        price: 27.99,
        quantity: 2
      }
    ]
  },
  {
    id: 4,
    number: 'PIND-2023-128',
    created_at: '2023-10-12T16:45:00Z',
    status: 'cancelled',
    subtotal: 74.98,
    shipping_cost: 4.99,
    discount_amount: 0,
    total: 95.97,
    items: [
      {
        id: 6,
        name: 'Work Shirt',
        image: 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=150&h=150&fit=crop',
        price: 37.49,
        quantity: 2
      }
    ]
  },
  {
    id: 5,
    number: 'PIND-2023-115',
    created_at: '2023-09-28T11:20:00Z',
    status: 'processing',
    subtotal: 199.98,
    shipping_cost: 0,
    discount_amount: 25.00,
    total: 209.98,
    items: [
      {
        id: 7,
        name: 'High-Vis Vest',
        image: 'https://images.unsplash.com/photo-1589330694652-d53bf3e11bd0?w=150&h=150&fit=crop',
        price: 24.99,
        quantity: 3
      },
      {
        id: 8,
        name: 'Hard Hat',
        image: 'https://images.unsplash.com/photo-1588854337236-6889d631faa1?w=150&h=150&fit=crop',
        price: 49.99,
        quantity: 1
      }
    ]
  },
  {
    id: 6,
    number: 'PIND-2023-099',
    created_at: '2023-08-15T13:10:00Z',
    status: 'delivered',
    subtotal: 159.96,
    shipping_cost: 4.99,
    discount_amount: 0,
    total: 197.95,
    items: [
      {
        id: 9,
        name: 'Work Shorts',
        image: 'https://images.unsplash.com/photo-1582418702059-97ebafb35d09?w=150&h=150&fit=crop',
        price: 34.99,
        quantity: 2
      },
      {
        id: 10,
        name: 'Safety Glasses',
        image: 'https://images.unsplash.com/photo-1581091226033-d5c48150dbaa?w=150&h=150&fit=crop',
        price: 19.99,
        quantity: 1
      }
    ]
  },
  {
    id: 7,
    number: 'PIND-2023-087',
    created_at: '2023-07-22T08:45:00Z',
    status: 'delivered',
    subtotal: 299.97,
    shipping_cost: 0,
    discount_amount: 30.00,
    total: 323.97,
    items: [
      {
        id: 11,
        name: 'Steel Toe Boots',
        image: 'https://images.unsplash.com/photo-1608256246200-53e635b5b65f?w=150&h=150&fit=crop',
        price: 129.99,
        quantity: 1
      },
      {
        id: 12,
        name: 'Work Socks (Pack of 3)',
        image: 'https://images.unsplash.com/photo-1586359743087-8157b8cb8272?w=150&h=150&fit=crop',
        price: 19.99,
        quantity: 3
      }
    ]
  },
  {
    id: 8,
    number: 'PIND-2023-075',
    created_at: '2023-06-10T15:30:00Z',
    status: 'out_for_delivery',
    subtotal: 84.98,
    shipping_cost: 4.99,
    discount_amount: 0,
    total: 107.97,
    items: [
      {
        id: 13,
        name: 'Work Belt',
        image: 'https://images.unsplash.com/photo-1582555172866-f73bb12a2ab3?w=150&h=150&fit=crop',
        price: 29.99,
        quantity: 1
      },
      {
        id: 14,
        name: 'Tool Pouch',
        image: 'https://images.unsplash.com/photo-1582555172866-f73bb12a2ab3?w=150&h=150&fit=crop',
        price: 54.99,
        quantity: 1
      }
    ]
  }
])

// Computed properties
const totalOrders = computed(() => orders.value.length)

const activeOrders = computed(() => {
  return orders.value.filter(order => 
    ['placed', 'confirmed', 'processing', 'shipped', 'out_for_delivery'].includes(order.status)
  ).length
})

const hasActiveFilters = computed(() => {
  return searchQuery.value || statusFilter.value || dateFilter.value
})

const filteredOrders = computed(() => {
  let filtered = [...orders.value]

  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(order => 
      order.number.toLowerCase().includes(query) ||
      order.items.some(item => item.name.toLowerCase().includes(query))
    )
  }

  // Status filter
  if (statusFilter.value) {
    filtered = filtered.filter(order => order.status === statusFilter.value.value)
  }

  // Date filter
  if (dateFilter.value) {
    const days = parseInt(dateFilter.value.value)
    const cutoffDate = new Date()
    cutoffDate.setDate(cutoffDate.getDate() - days)
    
    filtered = filtered.filter(order => {
      const orderDate = new Date(order.created_at)
      return orderDate >= cutoffDate
    })
  }

  // Sort
  switch (sortBy.value.value) {
    case 'oldest':
      filtered.sort((a, b) => new Date(a.created_at) - new Date(b.created_at))
      break
    case 'total_high':
      filtered.sort((a, b) => b.total - a.total)
      break
    case 'total_low':
      filtered.sort((a, b) => a.total - b.total)
      break
    case 'newest':
    default:
      filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
      break
  }

  return filtered
})

const totalPages = computed(() => {
  return Math.ceil(filteredOrders.value.length / ordersPerPage)
})

const paginatedOrders = computed(() => {
  const start = (currentPage.value - 1) * ordersPerPage
  const end = start + ordersPerPage
  return filteredOrders.value.slice(start, end)
})

const visiblePages = computed(() => {
  const pages = []
  const maxVisible = 5
  
  let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2))
  let end = Math.min(totalPages.value, start + maxVisible - 1)
  
  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1)
  }
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

const stats = computed(() => {
  return {
    delivered: orders.value.filter(order => order.status === 'delivered').length,
    processing: orders.value.filter(order => 
      ['placed', 'confirmed', 'processing'].includes(order.status)
    ).length,
    shipped: orders.value.filter(order => 
      ['shipped', 'out_for_delivery'].includes(order.status)
    ).length,
    cancelled: orders.value.filter(order => order.status === 'cancelled').length
  }
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
    placed: 'Placed',
    confirmed: 'Confirmed',
    processing: 'Processing',
    shipped: 'Shipped',
    out_for_delivery: 'Out for Delivery',
    delivered: 'Delivered',
    cancelled: 'Cancelled'
  }
  return statusMap[status] || status
}

const canReorder = (order) => {
  return order.status === 'delivered' || order.status === 'cancelled'
}

const canTrack = (order) => {
  return order.tracking_number && ['shipped', 'out_for_delivery'].includes(order.status)
}

const reorder = (order) => {
  order.items.forEach(item => {
    cartStore.addToCart({
      product_id: item.id,
      quantity: item.quantity,
      product: item
    })
  })
  router.push('/cart')
}

const trackOrder = (order) => {
  if (order.tracking_url) {
    window.open(order.tracking_url, '_blank')
  }
}

const clearFilters = () => {
  searchQuery.value = ''
  statusFilter.value = ''
  dateFilter.value = ''
  sortBy.value = sortOptions[0]
  currentPage.value = 1
}

const changePage = (page) => {
  currentPage.value = page
}

// Lifecycle
onMounted(() => {
  // Set default values for selects
  sortBy.value = sortOptions[0]
  
  // Simulate API call
  setTimeout(() => {
    loading.value = false
  }, 800)
})
</script>

<style scoped>
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

/* Filters Section */
.filters-section {
  background: var(--primary-white);
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 1.25rem 1.5rem;
  margin-bottom: 1.5rem;
}

.filters-grid {
  display: grid;
  grid-template-columns: 1.5fr 1fr 1fr 1fr auto;
  gap: 1rem;
  align-items: end;
}

.search-box {
  position: relative;
}

.search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--gray-medium);
}

.search-input {
  width: 100%;
  padding: 10px 12px 10px 40px;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  font-size: 0.9rem;
  transition: border-color 0.3s ease;
}

.search-input:focus {
  outline: none;
  border-color: var(--primary-red);
  box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.filter-label {
  font-size: 0.8rem;
  font-weight: 600;
  color: var(--gray-dark);
}

.clear-filters {
  white-space: nowrap;
  font-size: 0.85rem;
  padding: 10px 16px;
}

.clear-filters:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Custom v-select styles */
:deep(.v-select-custom) {
  font-size: 0.9rem;
}

:deep(.v-select-custom .vs__dropdown-toggle) {
  border: 1px solid var(--border-color);
  border-radius: 8px;
  padding: 8px 12px;
  min-height: 42px;
}

:deep(.v-select-custom .vs__selected) {
  margin: 0;
  padding: 0;
}

:deep(.v-select-custom .vs__search) {
  margin: 0;
  padding: 0;
}

:deep(.v-select-custom .vs__actions) {
  padding: 0;
}

:deep(.v-select-custom .vs__clear) {
  padding: 4px;
}

:deep(.v-select-custom .vs__open-indicator) {
  transform: scale(0.8);
}

/* Orders Section */
.orders-section {
  margin-bottom: 2rem;
}

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

/* Orders Grid - Compact Cards */
.orders-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 1.25rem;
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

/* Order Items */
.order-items {
  margin-bottom: 1rem;
}

.order-item {
  display: flex;
  align-items: center;
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
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.item-meta {
  margin: 0;
  color: var(--gray-medium);
  font-size: 0.75rem;
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

.summary-row.discount {
  color: #22c55e;
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

/* Order Actions */
.order-actions {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.btn-sm {
  padding: 6px 10px;
  font-size: 0.75rem;
  display: flex;
  align-items: center;
  gap: 4px;
}

/* Pagination */
.pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 1.5rem;
  padding: 1rem 1.5rem;
  background: var(--primary-white);
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.pagination-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 6px 12px;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  background: var(--primary-white);
  color: var(--gray-dark);
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.8rem;
}

.pagination-btn:hover:not(:disabled) {
  border-color: var(--primary-red);
  color: var(--primary-red);
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.pagination-pages {
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.pagination-page {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  background: var(--primary-white);
  color: var(--gray-dark);
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.8rem;
}

.pagination-page:hover {
  border-color: var(--primary-red);
  color: var(--primary-red);
}

.pagination-page.active {
  background: var(--primary-red);
  border-color: var(--primary-red);
  color: var(--primary-white);
}

/* Stats Section */
.stats-section {
  background: var(--primary-white);
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
}

.stats-section h2 {
  margin: 0 0 1rem 0;
  color: var(--primary-black);
  text-align: center;
  font-size: 1.25rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 1rem;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  transition: transform 0.2s ease;
}

.stat-item:hover {
  transform: translateY(-1px);
}

.stat-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.stat-icon.delivered {
  background: #dcfce7;
  color: #166534;
}

.stat-icon.processing {
  background: #ddd6fe;
  color: #5b21b6;
}

.stat-icon.shipped {
  background: #d1fae5;
  color: #065f46;
}

.stat-icon.cancelled {
  background: #fecaca;
  color: #991b1b;
}

.stat-content {
  flex: 1;
}

.stat-number {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--primary-black);
  line-height: 1;
}

.stat-label {
  font-size: 0.8rem;
  color: var(--gray-medium);
  margin-top: 0.25rem;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .orders-grid {
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  }
}

@media (max-width: 1024px) {
  .filters-grid {
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
  }
  
  .page-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .header-stats {
    width: 100%;
    justify-content: space-around;
  }
}

@media (max-width: 768px) {
  .order-history-page {
    padding: 1rem 0 2rem;
  }
  
  .filters-grid {
    grid-template-columns: 1fr;
  }
  
  .orders-grid {
    grid-template-columns: 1fr;
  }
  
  .order-card-header {
    flex-direction: column;
    gap: 0.5rem;
    align-items: flex-start;
  }
  
  .pagination {
    flex-direction: column;
    gap: 1rem;
  }
  
  .stats-grid {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 480px) {
  .page-header,
  .filters-section,
  .order-card,
  .stats-section {
    margin-left: -0.5rem;
    margin-right: -0.5rem;
    border-radius: 0;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
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