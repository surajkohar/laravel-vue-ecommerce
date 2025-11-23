<template>
  <FrontendLayout>
    <div class="products-page">
      <!-- Hero Section -->
      <section class="page-hero">
        <div class="container">
          <h1>Our Products</h1>
          <p>Discover our amazing collection of products</p>
        </div>
      </section>

      <!-- Main Content -->
      <div class="container">
        <div class="products-layout">
          <!-- Sidebar Filters -->
          <aside class="filters-sidebar">
            <ProductFilter @filters-changed="handleFiltersChange" />
          </aside>

          <!-- Products Grid -->
          <main class="products-main">
            <!-- Header -->
            <div class="products-header">
              <div class="results-info">
                <h2>All Products</h2>
                <p class="results-count">
                  Showing {{ products.length }} of {{ pagination.total }} products
                </p>
              </div>
              <div class="view-options">
                <button 
                  class="view-btn" 
                  :class="{ active: viewMode === 'grid' }"
                  @click="viewMode = 'grid'"
                >
                  Grid
                </button>
                <button 
                  class="view-btn" 
                  :class="{ active: viewMode === 'list' }"
                  @click="viewMode = 'list'"
                >
                  List
                </button>
              </div>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="loading-state">
              <div class="spinner"></div>
              <p>Loading products...</p>
            </div>

            <!-- Products Grid -->
            <div v-else class="products-container" :class="viewMode">
              <ProductCard 
                v-for="product in products" 
                :key="product.id" 
                :product="product"
                @quick-view="handleQuickView"
                class="product-item"
              />
            </div>

            <!-- Empty State -->
            <div v-if="!loading && products.length === 0" class="empty-state">
              <div class="empty-icon">📦</div>
              <h3>No products found</h3>
              <p>Try adjusting your search or filters</p>
              <button class="reset-btn" @click="resetAllFilters">Reset Filters</button>
            </div>

            <!-- Pagination -->
            <div v-if="pagination.last_page > 1" class="pagination">
              <button 
                :disabled="pagination.current_page === 1" 
                @click="changePage(pagination.current_page - 1)"
                class="pagination-btn"
              >
                Previous
              </button>
              
              <span 
                v-for="page in paginationRange" 
                :key="page"
                :class="['page-number', { active: page === pagination.current_page }]"
                @click="changePage(page)"
              >
                {{ page }}
              </span>
              
              <button 
                :disabled="pagination.current_page === pagination.last_page" 
                @click="changePage(pagination.current_page + 1)"
                class="pagination-btn"
              >
                Next
              </button>
            </div>
          </main>
        </div>
      </div>

      <!-- Quick View Modal -->
      <QuickViewModal 
        v-if="quickViewProduct"
        :product="quickViewProduct"
        @close="quickViewProduct = null"
      />
    </div>
  </FrontendLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useProductsStore } from '@/store/products'
import FrontendLayout from '@/layouts/FrontendLayout.vue'
import ProductFilter from '@/components/frontend/ProductFilter.vue'
import ProductCard from '@/components/frontend/ProductCard.vue'
import QuickViewModal from '@/components/frontend/QuickViewModal.vue'

const productsStore = useProductsStore()
const viewMode = ref('grid')
const quickViewProduct = ref(null)

// Computed properties
const products = computed(() => productsStore.products)
const pagination = computed(() => productsStore.pagination)
const loading = computed(() => productsStore.loading)

const paginationRange = computed(() => {
  const range = []
  const current = pagination.value.current_page
  const last = pagination.value.last_page
  const delta = 2
  
  for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
    range.push(i)
  }
  
  if (current - delta > 2) range.unshift('...')
  if (current + delta < last - 1) range.push('...')
  
  range.unshift(1)
  if (last > 1) range.push(last)
  
  return range
})

// Methods
const handleFiltersChange = () => {
  productsStore.fetchProducts(1)
}

const handlePageChange = (page) => {
  if (page !== '...') {
    productsStore.fetchProducts(page)
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const resetAllFilters = () => {
  productsStore.resetFilters()
  productsStore.fetchProducts(1)
}

const handleQuickView = (product) => {
  quickViewProduct.value = product
}

const changePage = (page) => {
  productsStore.setPage(page)
  productsStore.fetchProducts(page)
}

// Lifecycle
onMounted(() => {
  productsStore.fetchProducts()
  productsStore.fetchCategories()
  productsStore.fetchBrands()
})
</script>

<style scoped>
.products-page {
  min-height: 100vh;
  background: #f8f9fa;
}

.page-hero {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 3rem 0;
  text-align: center;
}

.page-hero h1 {
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
}

.page-hero p {
  font-size: 1.1rem;
  opacity: 0.9;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.products-layout {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 2rem;
  padding: 2rem 0;
}

.products-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.results-info h2 {
  margin: 0 0 0.5rem 0;
  color: #333;
}

.results-count {
  color: #6c757d;
  margin: 0;
}

.view-options {
  display: flex;
  gap: 0.5rem;
}

.view-btn {
  padding: 8px 16px;
  border: 1px solid #ddd;
  background: white;
  color: #666;
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.3s ease;
}

.view-btn.active {
  background: #667eea;
  color: white;
  border-color: #667eea;
}

.products-container.grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
  margin-bottom: 3rem;
}

.products-container.list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-bottom: 3rem;
}

.products-container.list .product-item {
  display: flex;
  max-height: 200px;
}

.products-container.list .product-item .product-image {
  width: 200px;
  height: 200px;
  flex-shrink: 0;
}

.loading-state {
  text-align: center;
  padding: 3rem;
  color: #667eea;
}

.spinner {
  border: 3px solid #f3f3f3;
  border-top: 3px solid #667eea;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.empty-state h3 {
  color: #495057;
  margin-bottom: 0.5rem;
}

.empty-state p {
  color: #6c757d;
  margin-bottom: 1.5rem;
}

.reset-btn {
  padding: 10px 20px;
  background: #667eea;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.3s ease;
}

.reset-btn:hover {
  background: #5a6fd8;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.5rem;
  margin: 3rem 0;
}

.pagination-btn {
  padding: 8px 16px;
  border: 1px solid #dee2e6;
  background: white;
  color: #667eea;
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.3s ease;
}

.pagination-btn:hover:not(:disabled) {
  background: #667eea;
  color: white;
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-number {
  padding: 8px 12px;
  border: 1px solid #dee2e6;
  background: white;
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.3s ease;
  min-width: 40px;
  text-align: center;
}

.page-number:hover {
  background: #f8f9fa;
}

.page-number.active {
  background: #667eea;
  color: white;
  border-color: #667eea;
}

/* Responsive */
@media (max-width: 1024px) {
  .products-layout {
    grid-template-columns: 250px 1fr;
    gap: 1.5rem;
  }
}

@media (max-width: 768px) {
  .products-layout {
    grid-template-columns: 1fr;
  }

  .filters-sidebar {
    order: 2;
  }

  .products-main {
    order: 1;
  }

  .products-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }

  .products-container.grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
  }
  
  .products-container.list .product-item {
    flex-direction: column;
    max-height: none;
  }
  
  .products-container.list .product-item .product-image {
    width: 100%;
    height: 200px;
  }
}

@media (max-width: 480px) {
  .products-container.grid {
    grid-template-columns: 1fr;
  }
  
  .pagination {
    flex-wrap: wrap;
  }
}
</style>