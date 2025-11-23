<template>
  <FrontendLayout>
    <div class="products-page">
      <!-- Hero Section -->
      <section class="page-hero">
        <div class="container">
          <h1>Our Collection</h1>
          <p>Discover our amazing fashion collection</p>
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
              <div class="view-controls">
                <div class="view-options">
                  <button 
                    class="view-btn" 
                    :class="{ active: viewMode === 'grid' }"
                    @click="viewMode = 'grid'"
                  >
                    <span>Grid</span>
                  </button>
                  <button 
                    class="view-btn" 
                    :class="{ active: viewMode === 'list' }"
                    @click="viewMode = 'list'"
                  >
                    <span>List</span>
                  </button>
                </div>
                <div class="sort-dropdown">
                  <select v-model="sortBy" @change="handleSortChange">
                    <option value="created_at">Newest</option>
                    <option value="price">Price: Low to High</option>
                    <option value="price_desc">Price: High to Low</option>
                    <option value="name">Name A-Z</option>
                  </select>
                </div>
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
            <Pagination 
              v-if="pagination.last_page > 1"
              :current-page="pagination.current_page"
              :total-pages="pagination.last_page"
              @page-change="handlePageChange"
            />
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
import { ref, computed, onMounted } from 'vue'
import { useProductsStore } from '@/store/products'
import FrontendLayout from '@/layouts/FrontendLayout.vue'
import ProductFilter from '@/components/frontend/ProductFilter.vue'
import ProductCard from '@/components/frontend/ProductCard.vue'
import Pagination from '@/components/frontend/Pagination.vue'
import QuickViewModal from '@/components/frontend/QuickViewModal.vue'

const productsStore = useProductsStore()
const viewMode = ref('grid')
const quickViewProduct = ref(null)
const sortBy = ref('created_at')

// Computed properties
const products = computed(() => productsStore.products)
const pagination = computed(() => productsStore.pagination)
const loading = computed(() => productsStore.loading)

// Methods
const handleFiltersChange = () => {
  productsStore.fetchProducts(1)
}

const handleSortChange = () => {
  productsStore.updateFilters({ sort_by: sortBy.value })
  productsStore.fetchProducts(1)
}

const handlePageChange = (page) => {
  productsStore.fetchProducts(page)
  // Scroll to top
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const resetAllFilters = () => {
  productsStore.resetFilters()
  sortBy.value = 'created_at'
  productsStore.fetchProducts(1)
}

const handleQuickView = (product) => {
  quickViewProduct.value = product
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
  background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
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
  align-items: start;
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
  flex-wrap: wrap;
  gap: 1rem;
}

.results-info h2 {
  margin: 0 0 0.5rem 0;
  color: #2c3e50;
  font-size: 1.5rem;
}

.results-count {
  color: #6c757d;
  margin: 0;
  font-size: 0.9rem;
}

.view-controls {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.view-options {
  display: flex;
  background: #f8f9fa;
  padding: 4px;
  border-radius: 8px;
}

.view-btn {
  padding: 8px 16px;
  background: transparent;
  color: #6c757d;
  border: none;
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.3s ease;
  font-weight: 500;
}

.view-btn.active {
  background: white;
  color: #3498db;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.sort-dropdown select {
  padding: 8px 12px;
  border: 1px solid #e9ecef;
  border-radius: 6px;
  background: white;
  color: #2c3e50;
  font-size: 0.9rem;
  cursor: pointer;
  transition: border-color 0.3s ease;
}

.sort-dropdown select:focus {
  outline: none;
  border-color: #3498db;
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
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s ease;
}

.products-container.list .product-item:hover {
  transform: translateY(-2px);
}

.products-container.list .product-item .product-image {
  width: 200px;
  height: 200px;
  flex-shrink: 0;
}

.loading-state {
  text-align: center;
  padding: 3rem;
  color: #3498db;
  grid-column: 1 / -1;
}

.spinner {
  border: 3px solid #f3f3f3;
  border-top: 3px solid #3498db;
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
  grid-column: 1 / -1;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
  opacity: 0.5;
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
  background: #3498db;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.3s ease;
  font-weight: 500;
}

.reset-btn:hover {
  background: #2980b9;
}

/* Fixed Filter Sidebar */
.filters-sidebar {
  position: sticky;
  top: 100px;
  height: fit-content;
  max-height: calc(100vh - 120px);
  overflow-y: auto;
}

/* Products Main Scroll */
.products-main {
  min-height: 600px;
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
    position: static;
    max-height: none;
  }

  .products-header {
    flex-direction: column;
    align-items: stretch;
    gap: 1rem;
  }

  .view-controls {
    justify-content: space-between;
  }

  .products-container.grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
  }
  
  .products-container.list .product-item {
    flex-direction: column;
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
  
  .view-controls {
    flex-direction: column;
    gap: 1rem;
  }
  
  .view-options {
    align-self: center;
  }
}
</style>