<template>
  <div class="product-filter">
    <div class="filter-header">
      <h3>Filters</h3>
      <button v-if="activeFilterCount > 0" class="clear-filters" @click="resetFilters">
        Clear All
      </button>
    </div>

    <!-- Search -->
    <div class="filter-group">
      <label>Search</label>
      <div class="search-box">
        <input 
          type="text" 
          v-model="localFilters.search" 
          placeholder="Search products..." 
          @input="handleSearchInput"
        >
        <span class="search-icon">🔍</span>
      </div>
    </div>

    <!-- Category -->
    <div class="filter-group">
      <label>Category</label>
      <select v-model="localFilters.category" @change="applyFilters">
        <option value="">All Categories</option>
        <option 
          v-for="category in categories" 
          :key="category.id" 
          :value="category.slug"
        >
          {{ category.name }}
        </option>
      </select>
    </div>

    <!-- Brand -->
    <div class="filter-group">
      <label>Brand</label>
      <select v-model="localFilters.brand" @change="applyFilters">
        <option value="">All Brands</option>
        <option v-for="brand in brands" :key="brand" :value="brand">
          {{ brand }}
        </option>
      </select>
    </div>

    <!-- Gender -->
    <div class="filter-group">
      <label>Gender</label>
      <select v-model="localFilters.gender" @change="applyFilters">
        <option value="">All</option>
        <option value="men">Men</option>
        <option value="women">Women</option>
        <option value="unisex">Unisex</option>
      </select>
    </div>

    <!-- Price Range -->
    <div class="filter-group">
      <label>Price Range</label>
      <div class="price-range">
        <div class="price-inputs">
          <input 
            type="number" 
            v-model="localFilters.min_price" 
            placeholder="Min" 
            @change="applyFilters"
          >
          <span>to</span>
          <input 
            type="number" 
            v-model="localFilters.max_price" 
            placeholder="Max" 
            @change="applyFilters"
          >
        </div>
        <div class="price-slider">
          <input 
            type="range" 
            min="0" 
            max="1000" 
            step="10"
            v-model="priceSlider"
            @input="handlePriceSlider"
            class="slider"
          >
          <div class="slider-labels">
            <span>$0</span>
            <span>$500</span>
            <span>$1000</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Sort -->
    <div class="filter-group">
      <label>Sort By</label>
      <select v-model="localFilters.sort_by" @change="applyFilters">
        <option value="created_at">Newest</option>
        <option value="price">Price: Low to High</option>
        <option value="price_desc">Price: High to Low</option>
        <option value="name">Name A-Z</option>
        <option value="popular">Most Popular</option>
      </select>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useProductsStore } from '@/store/products'

const productsStore = useProductsStore()
const emit = defineEmits(['filters-changed'])

const localFilters = ref({ ...productsStore.filters })
const priceSlider = ref(500)

// Computed properties
const activeFilterCount = computed(() => {
  const filters = localFilters.value
  return Object.values(filters).filter(value => 
    value !== '' && value !== null && value !== undefined
  ).length
})

const categories = computed(() => productsStore.categories)
const brands = computed(() => productsStore.brands)

// Methods
const handleSearchInput = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    applyFilters()
  }, 500)
}

const handlePriceSlider = () => {
  localFilters.value.max_price = priceSlider.value
  applyFilters()
}

let searchTimeout

const applyFilters = () => {
  productsStore.updateFilters(localFilters.value)
  emit('filters-changed')
}

const resetFilters = () => {
  productsStore.resetFilters()
  localFilters.value = { ...productsStore.filters }
  priceSlider.value = 500
  emit('filters-changed')
}

// Lifecycle
onMounted(() => {
  productsStore.fetchCategories()
  productsStore.fetchBrands()
})

// Watch for external filter changes
watch(() => productsStore.filters, (newFilters) => {
  localFilters.value = { ...newFilters }
  if (newFilters.max_price) {
    priceSlider.value = parseInt(newFilters.max_price)
  }
}, { deep: true })
</script>

<style scoped>
.product-filter {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  height: fit-content;
  position: sticky;
  top: 100px;
}

.filter-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #e9ecef;
}

.filter-header h3 {
  margin: 0;
  color: #2c3e50;
  font-size: 1.2rem;
}

.clear-filters {
  background: #e74c3c;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 0.8rem;
  cursor: pointer;
  transition: background 0.3s ease;
}

.clear-filters:hover {
  background: #c0392b;
}

.filter-group {
  margin-bottom: 1.5rem;
}

.filter-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #2c3e50;
  font-size: 0.9rem;
}

.search-box {
  position: relative;
}

.search-box input {
  width: 100%;
  padding: 10px 15px 10px 35px;
  border: 2px solid #e9ecef;
  border-radius: 8px;
  font-size: 0.9rem;
  transition: all 0.3s ease;
  background: #f6f8f9;
}

.search-box input:focus {
  outline: none;
  border-color: #2980b9;
  background: white;
  box-shadow: 0 0 0 3px rgba(41, 128, 185, 0.1);
}

.search-icon {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  color: #7f8c8d;
}

select, input[type="number"] {
  width: 100%;
  padding: 10px 12px;
  border: 2px solid #e9ecef;
  border-radius: 8px;
  font-size: 0.9rem;
  background: #f6f8f9;
  transition: all 0.3s ease;
}

select:focus, input[type="number"]:focus {
  outline: none;
  border-color: #2980b9;
  background: white;
  box-shadow: 0 0 0 3px rgba(41, 128, 185, 0.1);
}

/* Price Range Styles */
.price-range {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.price-inputs {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.price-inputs input {
  flex: 1;
}

.price-inputs span {
  color: #7f8c8d;
  font-size: 0.8rem;
  font-weight: 500;
}

.price-slider {
  margin-top: 0.5rem;
}

.slider {
  width: 100%;
  height: 6px;
  border-radius: 3px;
  background: #e9ecef;
  outline: none;
  -webkit-appearance: none;
}

.slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: #2980b9;
  cursor: pointer;
  border: 2px solid white;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}

.slider::-moz-range-thumb {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: #2980b9;
  cursor: pointer;
  border: 2px solid white;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}

.slider-labels {
  display: flex;
  justify-content: space-between;
  margin-top: 0.5rem;
  font-size: 0.7rem;
  color: #7f8c8d;
}

/* Responsive */
@media (max-width: 768px) {
  .product-filter {
    position: static;
    margin-bottom: 2rem;
  }
}
</style>