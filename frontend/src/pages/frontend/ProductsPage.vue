<template>
  <FrontendLayout>
    <div class="products-page">
      <!-- Breadcrumb -->
      <nav class="breadcrumb">
        <div class="container">
          <ul>
            <li><router-link to="/">Home</router-link></li>
            <li><span class="separator">/</span></li>
            <li>All Products</li>
          </ul>
        </div>
      </nav>

      <!-- Page Header -->
      <!-- <section class="page-header">
        <div class="container">
          <h1 class="page-title">All Products</h1>
          <p class="page-subtitle">Discover our complete collection</p>
        </div>
      </section> -->

      <!-- Main Content -->
      <div class="container">
        <div class="products-layout">
          <!-- Mobile Filter Toggle -->
          <div class="mobile-filter-toggle">
            <button class="filter-toggle-btn" @click="showMobileFilters = !showMobileFilters">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M4 6H20M4 12H20M4 18H20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              </svg>
              Filters
              <span class="filter-count" v-if="activeFilters.length > 0">
                {{ activeFilters.length }}
              </span>
            </button>
          </div>

          <!-- Sidebar Filters -->
          <aside class="filters-sidebar" :class="{ 'mobile-open': showMobileFilters }">
            <div class="filters-header">
              <h3>Filter Products</h3>
              <div class="filter-actions">
                <button class="clear-filters" @click="resetAllFilters">
                  Clear All
                </button>
                <button class="close-filters" @click="showMobileFilters = false">
                  ×
                </button>
              </div>
            </div>
            
            <!-- Categories -->
            <div class="filter-group">
              <div class="filter-group-header" @click="toggleFilterGroup('categories')">
                <h4 class="filter-title">Categories</h4>
                <span class="toggle-icon">{{ openGroups.categories ? '−' : '+' }}</span>
              </div>
              <div class="filter-group-content" v-show="openGroups.categories">
                <div class="checkbox-list">
                  <label 
                    v-for="category in categories" 
                    :key="category.id" 
                    class="checkbox-item"
                  >
                    <input 
                      type="checkbox" 
                      :value="category.id" 
                      v-model="selectedCategories"
                      @change="updateCategoryFilter"
                    >
                    <span class="checkmark"></span>
                    <span class="label-text">{{ category.name }}</span>
                    <span class="item-count">({{ category.product_count || 0 }})</span>
                  </label>
                </div>
              </div>
            </div>

            <!-- Brands -->
            <div class="filter-group">
              <div class="filter-group-header" @click="toggleFilterGroup('brands')">
                <h4 class="filter-title">Brands</h4>
                <span class="toggle-icon">{{ openGroups.brands ? '−' : '+' }}</span>
              </div>
              <div class="filter-group-content" v-show="openGroups.brands">
                <div class="checkbox-list">
                  <label 
                    v-for="brand in brands" 
                    :key="brand.id" 
                    class="checkbox-item"
                  >
                    <input 
                      type="checkbox" 
                      :value="brand.id" 
                      v-model="selectedBrands"
                      @change="updateBrandFilter"
                    >
                    <span class="checkmark"></span>
                    <span class="label-text">{{ brand.name }}</span>
                    <span class="item-count">({{ brand.product_count || 0 }})</span>
                  </label>
                </div>
              </div>
            </div>

            <!-- Price Range -->
            <div class="filter-group">
              <div class="filter-group-header" @click="toggleFilterGroup('price')">
                <h4 class="filter-title">Price Range</h4>
                <span class="toggle-icon">{{ openGroups.price ? '−' : '+' }}</span>
              </div>
              <div class="filter-group-content" v-show="openGroups.price">
                <div class="price-slider-container">
                  <div class="price-inputs">
                    <div class="price-input">
                      <input 
                        type="number" 
                        v-model="priceRange.min" 
                        placeholder="0"
                        @change="updatePriceFilter"
                        class="price-input-field"
                      >
                    </div>
                    <span class="price-separator">-</span>
                    <div class="price-input">
                      <input 
                        type="number" 
                        v-model="priceRange.max" 
                        placeholder="1000"
                        @change="updatePriceFilter"
                        class="price-input-field"
                      >
                    </div>
                  </div>
                  <div class="price-display">
                    £{{ priceRange.min }} - £{{ priceRange.max }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Size -->
            <div class="filter-group">
              <div class="filter-group-header" @click="toggleFilterGroup('sizes')">
                <h4 class="filter-title">Size</h4>
                <span class="toggle-icon">{{ openGroups.sizes ? '−' : '+' }}</span>
              </div>
              <div class="filter-group-content" v-show="openGroups.sizes">
                <div class="size-grid">
                  <button
                    v-for="size in sizeOptions"
                    :key="size"
                    class="size-btn"
                    :class="{ active: selectedSizes.includes(size) }"
                    @click="toggleSize(size)"
                  >
                    {{ size }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Color -->
            <div class="filter-group">
              <div class="filter-group-header" @click="toggleFilterGroup('colors')">
                <h4 class="filter-title">Color</h4>
                <span class="toggle-icon">{{ openGroups.colors ? '−' : '+' }}</span>
              </div>
              <div class="filter-group-content" v-show="openGroups.colors">
                <div class="color-grid">
                  <button
                    v-for="color in colorOptions"
                    :key="color.value"
                    class="color-btn"
                    :class="{ active: selectedColors.includes(color.value) }"
                    @click="toggleColor(color.value)"
                    :style="{ backgroundColor: color.value }"
                    :title="color.name"
                  >
                    <span class="color-check" v-if="selectedColors.includes(color.value)">✓</span>
                  </button>
                </div>
              </div>
            </div>
          </aside>

          <!-- Products Grid -->
          <main class="products-main">
            <!-- Products Header -->
            <div class="products-header">
              <div class="results-info">
                <p class="results-count">
                  Showing <strong>{{ products.length }}</strong> of <strong>{{ pagination.total }}</strong> products
                </p>
              </div>
              <div class="sort-controls">
                <div class="sort-select">
                  <label for="sort-by">Sort by:</label>
                  <v-select
                    id="sort-by"
                    v-model="sortBy"
                    :options="sortOptions"
                    :clearable="false"
                    :searchable="false"
                    @option:selected="handleSortChange"
                    class="sort-dropdown"
                  ></v-select>
                </div>
                <div class="view-options">
                  <button 
                    class="view-btn" 
                    :class="{ active: viewMode === 'grid' }"
                    @click="viewMode = 'grid'"
                    title="Grid View"
                  >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                      <rect x="3" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                      <rect x="14" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                      <rect x="3" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                      <rect x="14" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                    </svg>
                  </button>
                  <button 
                    class="view-btn" 
                    :class="{ active: viewMode === 'list' }"
                    @click="viewMode = 'list'"
                    title="List View"
                  >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                      <rect x="3" y="6" width="18" height="2" rx="1" stroke="currentColor" stroke-width="2"/>
                      <rect x="3" y="11" width="18" height="2" rx="1" stroke="currentColor" stroke-width="2"/>
                      <rect x="3" y="16" width="18" height="2" rx="1" stroke="currentColor" stroke-width="2"/>
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <!-- Active Filters -->
            <div v-if="activeFilters.length > 0" class="active-filters">
              <div class="active-filters-content">
                <span class="active-filters-label">Active filters:</span>
                <div class="filter-tags">
                  <span 
                    v-for="filter in activeFilters" 
                    :key="filter.key"
                    class="filter-tag"
                  >
                    {{ filter.label }}
                    <button @click="removeFilter(filter.key)" class="remove-filter">
                      ×
                    </button>
                  </span>
                </div>
              </div>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="loading-state">
              <div class="spinner"></div>
              <p>Loading products...</p>
            </div>

            <!-- Products Grid -->
            <div v-else-if="products.length > 0" class="products-container" :class="viewMode">
              <ProductCard 
                v-for="product in products" 
                :key="product.id" 
                :product="product"
                :view-mode="viewMode"
                @quick-view="handleQuickView"
                class="product-item"
              />
            </div>

            <!-- Empty State -->
            <div v-else-if="!loading" class="empty-state">
              <div class="empty-icon">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none">
                  <path d="M20 12V8H4V12M20 12V18H4V12M20 12H4" stroke="currentColor" stroke-width="2"/>
                  <path d="M8 16H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
              </div>
              <h3>No products found</h3>
              <p>Try adjusting your filters or search terms</p>
              <button class="btn btn-primary reset-btn" @click="resetAllFilters">
                Reset All Filters
              </button>
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

      <!-- Mobile Filter Overlay -->
      <div 
        v-if="showMobileFilters" 
        class="mobile-overlay"
        @click="showMobileFilters = false"
      ></div>
    </div>
  </FrontendLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useProductsStore } from '@/store/products'
import FrontendLayout from '@/layouts/FrontendLayout.vue'
import ProductCard from '@/components/frontend/ProductCard.vue'
import Pagination from '@/components/frontend/Pagination.vue'
import QuickViewModal from '@/components/frontend/QuickViewModal.vue'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'

const productsStore = useProductsStore()
const viewMode = ref('grid')
const quickViewProduct = ref(null)
const sortBy = ref('created_at')
const showMobileFilters = ref(false)

// Sort options for vue-select
const sortOptions = [
  { label: 'Newest First', value: 'created_at' },
  { label: 'Price: Low to High', value: 'price' },
  { label: 'Price: High to Low', value: 'price_desc' },
  { label: 'Name: A to Z', value: 'name' },
  { label: 'Most Popular', value: 'popularity' }
]

// Filter states
const priceRange = ref({ min: 0, max: 1000 })
const selectedCategories = ref([])
const selectedBrands = ref([])
const selectedSizes = ref([])
const selectedColors = ref([])

// Collapsible filter groups
const openGroups = ref({
  categories: true,
  brands: true,
  price: true,
  sizes: true,
  colors: true
})

// Filter options
const sizeOptions = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL']
const colorOptions = [
  { name: 'Black', value: '#000000' },
  { name: 'Navy', value: '#001F3F' },
  { name: 'Royal Blue', value: '#0074D9' },
  { name: 'Red', value: '#FF4136' },
  { name: 'Green', value: '#2ECC40' },
  { name: 'Yellow', value: '#FFDC00' },
  { name: 'White', value: '#FFFFFF' },
  { name: 'Grey', value: '#AAAAAA' }
]

// Computed properties
const products = computed(() => productsStore.products)
const pagination = computed(() => productsStore.pagination)
const loading = computed(() => productsStore.loading)
const categories = computed(() => productsStore.categories)
const brands = computed(() => productsStore.brands)

// Active Filters
const activeFilters = computed(() => {
  const filters = []
  
  if (priceRange.value.min > 0 || priceRange.value.max < 1000) {
    filters.push({
      key: 'price',
      label: `Price: £${priceRange.value.min}-£${priceRange.value.max}`
    })
  }
  
  if (selectedCategories.value.length > 0) {
    const selectedNames = categories.value
      .filter(cat => selectedCategories.value.includes(cat.id))
      .map(cat => cat.name)
    if (selectedNames.length > 0) {
      filters.push({
        key: 'categories',
        label: `Category: ${selectedNames.join(', ')}`
      })
    }
  }
  
  if (selectedBrands.value.length > 0) {
    const selectedNames = brands.value
      .filter(brand => selectedBrands.value.includes(brand.id))
      .map(brand => brand.name)
    if (selectedNames.length > 0) {
      filters.push({
        key: 'brands',
        label: `Brand: ${selectedNames.join(', ')}`
      })
    }
  }
  
  if (selectedSizes.value.length > 0) {
    filters.push({
      key: 'sizes',
      label: `Size: ${selectedSizes.value.join(', ')}`
    })
  }
  
  if (selectedColors.value.length > 0) {
    const colorNames = colorOptions
      .filter(color => selectedColors.value.includes(color.value))
      .map(color => color.name)
    filters.push({
      key: 'colors',
      label: `Color: ${colorNames.join(', ')}`
    })
  }
  
  return filters
})

// Methods
const toggleFilterGroup = (group) => {
  openGroups.value[group] = !openGroups.value[group]
}

const updatePriceFilter = () => {
  productsStore.updateFilters({ 
    min_price: priceRange.value.min,
    max_price: priceRange.value.max
  })
  productsStore.fetchProducts(1)
}

const updateCategoryFilter = () => {
  productsStore.updateFilters({ categories: selectedCategories.value })
  productsStore.fetchProducts(1)
}

const updateBrandFilter = () => {
  productsStore.updateFilters({ brands: selectedBrands.value })
  productsStore.fetchProducts(1)
}

const toggleSize = (size) => {
  const index = selectedSizes.value.indexOf(size)
  if (index > -1) {
    selectedSizes.value.splice(index, 1)
  } else {
    selectedSizes.value.push(size)
  }
  productsStore.updateFilters({ sizes: selectedSizes.value })
  productsStore.fetchProducts(1)
}

const toggleColor = (color) => {
  const index = selectedColors.value.indexOf(color)
  if (index > -1) {
    selectedColors.value.splice(index, 1)
  } else {
    selectedColors.value.push(color)
  }
  productsStore.updateFilters({ colors: selectedColors.value })
  productsStore.fetchProducts(1)
}

const handleSortChange = (selectedOption) => {
  productsStore.updateFilters({ sort_by: selectedOption.value })
  productsStore.fetchProducts(1)
}

const handlePageChange = (page) => {
  productsStore.fetchProducts(page)
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const removeFilter = (filterKey) => {
  switch (filterKey) {
    case 'price':
      priceRange.value = { min: 0, max: 1000 }
      break
    case 'categories':
      selectedCategories.value = []
      break
    case 'brands':
      selectedBrands.value = []
      break
    case 'sizes':
      selectedSizes.value = []
      break
    case 'colors':
      selectedColors.value = []
      break
  }
  productsStore.updateFilters({ 
    min_price: priceRange.value.min,
    max_price: priceRange.value.max,
    categories: selectedCategories.value,
    brands: selectedBrands.value,
    sizes: selectedSizes.value,
    colors: selectedColors.value
  })
  productsStore.fetchProducts(1)
}

const resetAllFilters = () => {
  productsStore.resetFilters()
  sortBy.value = 'created_at'
  priceRange.value = { min: 0, max: 1000 }
  selectedCategories.value = []
  selectedBrands.value = []
  selectedSizes.value = []
  selectedColors.value = []
  productsStore.fetchProducts(1)
  showMobileFilters.value = false
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

/* Breadcrumb */
.breadcrumb {
  background: var(--primary-white);
  border-bottom: 1px solid var(--border-color);
  padding: 1rem 0;
}

.breadcrumb ul {
  display: flex;
  align-items: center;
  list-style: none;
  margin: 0;
  padding: 0;
  gap: 0.5rem;
}

.breadcrumb a {
  color: var(--gray-medium);
  text-decoration: none;
  font-size: 0.9rem;
  transition: color 0.3s ease;
}

.breadcrumb a:hover {
  color: var(--primary-red);
}

.breadcrumb .separator {
  color: var(--gray-medium);
  font-size: 0.8rem;
}

.breadcrumb li:last-child {
  color: var(--primary-black);
  font-weight: 600;
}

/* Page Header */
.page-header {
  background: var(--primary-white);
  padding: 2rem 0;
  text-align: center;
}

.page-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: var(--primary-black);
  margin-bottom: 0.5rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.page-subtitle {
  color: var(--gray-medium);
  font-size: 1.1rem;
  margin: 0;
}

/* Main Layout */
.container {
  max-width: 1400px;
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

/* Mobile Filter Toggle */
.mobile-filter-toggle {
  display: none;
  margin-bottom: 1rem;
}

.filter-toggle-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 12px 16px;
  background: var(--primary-white);
  border: 2px solid var(--border-color);
  border-radius: 8px;
  color: var(--primary-black);
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  width: 100%;
  justify-content: center;
}

.filter-toggle-btn:hover {
  border-color: var(--primary-red);
  color: var(--primary-red);
}

.filter-count {
  background: var(--primary-red);
  color: var(--primary-white);
  border-radius: 12px;
  padding: 2px 8px;
  font-size: 0.8rem;
  font-weight: 600;
  min-width: 20px;
}

/* Filters Sidebar */
.filters-sidebar {
  background: var(--primary-white);
  border-radius: 8px;
  border: 1px solid var(--border-color);
  position: sticky;
  top: 100px;
  height: fit-content;
  max-height: calc(100vh - 120px);
  overflow-y: auto;
}

.filters-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid var(--border-color);
}

.filters-header h3 {
  margin: 0;
  color: var(--primary-black);
  font-size: 1.25rem;
  font-weight: 700;
}

.filter-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.clear-filters {
  background: none;
  border: none;
  color: var(--primary-red);
  font-weight: 600;
  cursor: pointer;
  font-size: 0.9rem;
  padding: 4px 8px;
  border-radius: 4px;
  transition: background 0.3s ease;
}

.clear-filters:hover {
  background: rgba(227, 27, 35, 0.1);
}

.close-filters {
  display: none;
  background: none;
  border: none;
  color: var(--gray-medium);
  cursor: pointer;
  font-size: 1.5rem;
  padding: 0;
  width: 30px;
  height: 30px;
  border-radius: 4px;
  transition: all 0.3s ease;
}

.close-filters:hover {
  background: var(--gray-light);
  color: var(--primary-black);
}

/* Filter Groups */
.filter-group {
  border-bottom: 1px solid var(--border-color);
}

.filter-group:last-child {
  border-bottom: none;
}

.filter-group-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 1.5rem;
  cursor: pointer;
  transition: background 0.3s ease;
}

.filter-group-header:hover {
  background: var(--gray-light);
}

.filter-title {
  margin: 0;
  color: var(--primary-black);
  font-size: 1rem;
  font-weight: 600;
}

.toggle-icon {
  color: var(--gray-medium);
  font-weight: 600;
  font-size: 1.2rem;
}

.filter-group-content {
  padding: 0 1.5rem 1.5rem;
}

/* Price Slider */
.price-slider-container {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.price-inputs {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.price-input {
  flex: 1;
}

.price-input-field {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid var(--border-color);
  border-radius: 4px;
  font-size: 0.9rem;
  transition: border-color 0.3s ease;
}

.price-input-field:focus {
  outline: none;
  border-color: var(--primary-red);
}

.price-separator {
  color: var(--gray-medium);
  font-weight: 600;
}

.price-display {
  text-align: center;
  color: var(--primary-red);
  font-weight: 600;
  font-size: 0.9rem;
  padding: 8px;
  background: var(--gray-light);
  border-radius: 4px;
}

/* Checkbox List */
.checkbox-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.checkbox-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  cursor: pointer;
  padding: 0.5rem 0;
}

.checkbox-item input {
  display: none;
}

.checkmark {
  width: 18px;
  height: 18px;
  border: 2px solid var(--border-color);
  border-radius: 3px;
  position: relative;
  transition: all 0.3s ease;
  flex-shrink: 0;
}

.checkbox-item input:checked + .checkmark {
  background: var(--primary-red);
  border-color: var(--primary-red);
}

.checkbox-item input:checked + .checkmark::after {
  content: '✓';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: white;
  font-size: 12px;
  font-weight: bold;
}

.label-text {
  font-size: 0.9rem;
  color: var(--gray-dark);
  font-weight: 500;
  flex: 1;
}

.item-count {
  font-size: 0.8rem;
  color: var(--gray-medium);
}

/* Size Grid */
.size-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.5rem;
}

.size-btn {
  padding: 8px 4px;
  border: 1px solid var(--border-color);
  background: var(--primary-white);
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 0.8rem;
  font-weight: 500;
}

.size-btn:hover {
  border-color: var(--primary-red);
  color: var(--primary-red);
}

.size-btn.active {
  background: var(--primary-red);
  color: var(--primary-white);
  border-color: var(--primary-red);
}

/* Color Grid */
.color-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0.5rem;
}

.color-btn {
  width: 32px;
  height: 32px;
  border: 2px solid var(--border-color);
  border-radius: 4px;
  cursor: pointer;
  position: relative;
  transition: all 0.3s ease;
}

.color-btn:hover {
  transform: scale(1.1);
}

.color-btn.active {
  border-color: var(--primary-black);
}

.color-check {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: white;
  font-size: 12px;
  font-weight: bold;
  text-shadow: 0 0 2px rgba(0, 0, 0, 0.5);
}

/* Products Header */
.products-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding: 1.5rem;
  background: var(--primary-white);
  border-radius: 8px;
  border: 1px solid var(--border-color);
}

.results-count {
  margin: 0;
  color: var(--gray-dark);
  font-size: 0.95rem;
}

.results-count strong {
  color: var(--primary-black);
}

.sort-controls {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.sort-select {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.sort-select label {
  font-size: 0.9rem;
  color: var(--gray-dark);
  font-weight: 500;
  white-space: nowrap;
}

/* Vue Select Customization */
:deep(.v-select) {
  min-width: 160px;
}

:deep(.vs__dropdown-toggle) {
  border: 1px solid var(--border-color);
  border-radius: 4px;
  padding: 8px 12px;
  background: var(--primary-white);
}

:deep(.vs__selected) {
  color: var(--primary-black);
  font-size: 0.9rem;
}

:deep(.vs__dropdown-option) {
  font-size: 0.9rem;
  padding: 8px 12px;
}

:deep(.vs__dropdown-option--highlight) {
  background: var(--primary-red);
}

:deep(.vs__clear) {
  display: none;
}

:deep(.vs__open-indicator) {
  fill: var(--gray-medium);
}

.view-options {
  display: flex;
  background: var(--gray-light);
  padding: 4px;
  border-radius: 6px;
  border: 1px solid var(--border-color);
}

.view-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 6px;
  background: transparent;
  color: var(--gray-medium);
  border: none;
  cursor: pointer;
  border-radius: 4px;
  transition: all 0.3s ease;
  width: 32px;
  height: 32px;
}

.view-btn:hover {
  color: var(--primary-black);
}

.view-btn.active {
  background: var(--primary-white);
  color: var(--primary-red);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Active Filters */
.active-filters {
  margin-bottom: 1.5rem;
}

.active-filters-content {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  padding: 1rem;
  background: var(--primary-white);
  border-radius: 8px;
  border: 1px solid var(--border-color);
}

.active-filters-label {
  font-size: 0.9rem;
  color: var(--gray-dark);
  font-weight: 600;
  white-space: nowrap;
  padding-top: 2px;
}

.filter-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.filter-tag {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: var(--primary-red);
  color: var(--primary-white);
  padding: 6px 10px;
  border-radius: 16px;
  font-size: 0.8rem;
  font-weight: 500;
}

.remove-filter {
  background: none;
  border: none;
  color: var(--primary-white);
  cursor: pointer;
  font-size: 1rem;
  line-height: 1;
  padding: 0;
  width: 14px;
  height: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: background 0.3s ease;
}

.remove-filter:hover {
  background: rgba(255, 255, 255, 0.2);
}

/* Products Grid - 3 cards per row */
.products-container.grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
  margin-bottom: 3rem;
}

.products-container.list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-bottom: 3rem;
}

/* Loading State */
.loading-state {
  text-align: center;
  padding: 4rem;
  color: var(--primary-red);
  grid-column: 1 / -1;
  background: var(--primary-white);
  border-radius: 8px;
  border: 1px solid var(--border-color);
}

.spinner {
  border: 3px solid var(--border-color);
  border-top: 3px solid var(--primary-red);
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

.loading-state p {
  font-size: 1rem;
  font-weight: 600;
  color: var(--primary-black);
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 3rem 2rem;
  background: var(--primary-white);
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  border: 1px solid var(--border-color);
  grid-column: 1 / -1;
}

.empty-icon {
  color: var(--gray-medium);
  margin-bottom: 1rem;
  opacity: 0.5;
}

.empty-state h3 {
  color: var(--primary-black);
  margin-bottom: 0.5rem;
  font-size: 1.25rem;
  font-weight: 600;
}

.empty-state p {
  color: var(--gray-medium);
  margin-bottom: 1.5rem;
  font-size: 0.95rem;
}

.reset-btn {
  padding: 10px 20px;
  font-size: 0.9rem;
}

/* Mobile Overlay */
.mobile-overlay {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 998;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .products-container.grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
  }
}

@media (max-width: 768px) {
  .products-layout {
    grid-template-columns: 1fr;
  }

  .mobile-filter-toggle {
    display: block;
  }

  .filters-sidebar {
    position: fixed;
    top: 0;
    left: -100%;
    width: 320px;
    height: 100vh;
    z-index: 999;
    border-radius: 0;
    border: none;
    border-right: 1px solid var(--border-color);
    transition: left 0.3s ease;
    max-height: none;
  }

  .filters-sidebar.mobile-open {
    left: 0;
  }

  .close-filters {
    display: block;
  }

  .mobile-overlay {
    display: block;
  }

  .products-header {
    flex-direction: column;
    align-items: stretch;
    gap: 1rem;
  }

  .sort-controls {
    justify-content: space-between;
  }

  .products-container.grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .active-filters-content {
    flex-direction: column;
    align-items: stretch;
    gap: 0.75rem;
  }

  .page-title {
    font-size: 2rem;
  }
}

@media (max-width: 640px) {
  .products-container.grid {
    grid-template-columns: 1fr;
  }

  .sort-controls {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }

  .sort-select {
    justify-content: space-between;
  }

  .view-options {
    align-self: center;
  }

  .price-inputs {
    grid-template-columns: 1fr;
  }

  .size-grid {
    grid-template-columns: repeat(4, 1fr);
  }

  .color-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

@media (max-width: 480px) {
  .container {
    padding: 0 15px;
  }

  .products-layout {
    padding: 1rem 0;
  }

  .page-header {
    padding: 1.5rem 0;
  }

  .page-title {
    font-size: 1.75rem;
  }

  .filters-sidebar {
    width: 100%;
  }
}

/* Scrollbar Styling */
.filters-sidebar::-webkit-scrollbar {
  width: 6px;
}

.filters-sidebar::-webkit-scrollbar-track {
  background: var(--gray-light);
  border-radius: 3px;
}

.filters-sidebar::-webkit-scrollbar-thumb {
  background: var(--gray-medium);
  border-radius: 3px;
}

.filters-sidebar::-webkit-scrollbar-thumb:hover {
  background: var(--primary-black);
}
</style>