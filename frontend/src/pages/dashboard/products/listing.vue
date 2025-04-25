<template>
  <div class="products-listing">
    <div class="header-section">
      <h1>Products</h1>
      <div class="action-buttons">
        <button class="add-button" @click="router.push('/admin/product/add')">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
          </svg>
          Add Product
        </button>
        <!-- filter -->
        <div class="dropdown">
            <button class="filter-button" @click="toggleDropdown">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
              </svg>
              Filters
            </button>
          <div class="dropdown-menu" v-show="showDropdown">
            <div class="dropdown-content">
              <!-- Close button added here -->
              <button class="close-dropdown" @click="toggleDropdown">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
              </button>
              
              <div class="filter-group">
                <label>Category</label>
                <select v-model="filters.category" class="form-select">
                  <option value="">All Categories</option>
                  <option v-for="category in categories" :value="category" :key="category">{{ category }}</option>
                </select>
              </div>
              <div class="filter-group">
                <label>Created From</label>
                <input type="date" v-model="filters.createdFrom" class="form-control">
              </div>
              <div class="filter-group">
                <label>Created To</label>
                <input type="date" v-model="filters.createdTo" class="form-control">
              </div>
              <div class="dropdown-footer">
                <button class="btn btn-sm btn-link" @click="resetFilters">Reset All</button>
                <button class="btn btn-sm btn-primary" @click="applyFilters">Apply</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="d-flex justify-content-between align-items-center">
       <div>
        <h4>This is products listing!</h4>
       </div>
        <div class="search-container">
          <input type="text" v-model="filters.search" placeholder="Search products..." class="search-input">
      </div>
    </div>

    <!-- Products Table -->
    <div class="table-container" @scroll="handleScroll" ref="tableContainer">
      <table>
        <thead>
          <tr>
            <th @click="sortBy('id')">
              ID
              <span class="sort-icon">
                <template v-if="sortField !== 'id'">↑↓</template>
                <template v-if="sortField === 'id'">
                  {{ sortDirection === 'asc' ? '↑' : '↓' }}
                </template>
              </span>
            </th>
            <th @click="sortBy('name')">
              Name
              <span class="sort-icon">
                <template v-if="sortField !== 'name'">↑↓</template>
                <template v-if="sortField === 'name'">
                  {{ sortDirection === 'asc' ? '↑' : '↓' }}
                </template>
              </span>
            </th>
            <th @click="sortBy('price')">
              Price
              <span class="sort-icon">
                <template v-if="sortField !== 'price'">↑↓</template>
                <template v-if="sortField === 'price'">
                  {{ sortDirection === 'asc' ? '↑' : '↓' }}
                </template>
              </span>
            </th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in filteredProducts" :key="product.id">
            <td>{{ product.id }}</td>
            <td>{{ product.name }}</td>
            <td>${{ product.price }}</td>
            <td>
              <span class="status-badge" :class="product.status">{{ product.status }}</span>
            </td>
            <td class="actions-cell">
              <button class="view-button" @click="viewProduct(product.id)">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
              </button>
              <button class="edit-button" @click="editProduct(product.id)">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
              </button>
              <button class="delete-button" @click="deleteProduct(product.id)">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="3 6 5 6 21 6"></polyline>
                  <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="loadingMore" class="loading-more">
        <div class="spinner"></div>
        Loading more products...
      </div>
    </div>

    <!-- Pagination (optional) -->
    <div class="pagination" v-if="usePagination && totalPages > 1">
      <button @click="prevPage" :disabled="currentPage === 1">Previous</button>
      <span v-for="page in visiblePages" :key="page" 
            @click="goToPage(page)" 
            :class="{ active: currentPage === page }">
        {{ page }}
      </span>
      <button @click="nextPage" :disabled="currentPage === totalPages">Next</button>
    </div>

    <!-- Loading and Error States -->
    <div v-if="loading" class="loading">
      <div class="spinner"></div>
      Loading products...
    </div>
    <div v-if="error" class="error">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="12" y1="8" x2="12" y2="12"></line>
        <line x1="12" y1="16" x2="12.01" y2="16"></line>
      </svg>
      {{ error }}
    </div>

    <!-- Empty State -->
    <div v-if="!loading && filteredProducts.length === 0" class="empty-state">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
      </svg>
      <h3>No products found</h3>
      <p>Try adjusting your filters or add a new product</p>
      <button class="add-button" >Add Product</button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

// Sample product data with more fields
const products = ref([
  { id: 1, name: 'Premium Headphones', price: 199.99, category: 'Electronics', status: 'active', createdAt: '2023-01-15' },
  { id: 2, name: 'Wireless Mouse', price: 29.99, category: 'Electronics', status: 'active', createdAt: '2023-02-20' },
  { id: 3, name: 'Ergonomic Keyboard', price: 89.99, category: 'Electronics', status: 'out-of-stock', createdAt: '2023-03-10' },
  { id: 4, name: 'Desk Lamp', price: 39.99, category: 'Home', status: 'active', createdAt: '2023-04-05' },
  { id: 10, name: 'Bluetooth Speaker', price: 59.99, category: 'Electronics', status: 'discontinued', createdAt: '2023-07-22' },
  { id: 9, name: 'Stapler', price: 5.99, category: 'Office', status: 'active', createdAt: '2023-08-30' },
  { id: 5, name: 'Notebook Set', price: 12.99, category: 'Office', status: 'active', createdAt: '2023-05-12' },
  { id: 6, name: 'Coffee Mug', price: 9.99, category: 'Home', status: 'active', createdAt: '2023-06-18' },
  { id: 7, name: 'Bluetooth Speaker', price: 59.99, category: 'Electronics', status: 'discontinued', createdAt: '2023-07-22' },
  { id: 8, name: 'Stapler', price: 5.99, category: 'Office', status: 'active', createdAt: '2023-08-30' },
]);

const loading = ref(false);
const loadingMore = ref(false);
const error = ref('');
const showFilterDropdown = ref(false);
const showDropdown = ref(false);
const currentPage = ref(1);
const itemsPerPage = ref(5);
const sortField = ref('id');
const sortDirection = ref('asc');
const usePagination = ref(true); // Toggle between pagination and infinite scroll
const tableContainer = ref(null);
import { useRouter } from 'vue-router';
const router = useRouter();

const filters = ref({
  search: '',
  category: '',
  createdFrom: '',
  createdTo: ''
});

const categories = computed(() => {
  return [...new Set(products.value.map(product => product.category))];
});

// Sorting functionality
const sortBy = (field) => {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortField.value = field;
    sortDirection.value = 'asc';
  }
  currentPage.value = 1; // Reset to first page when sorting changes
};

// Filtered and sorted products
const filteredProducts = computed(() => {
  let result = [...products.value];
  
  // Apply search filter
  if (filters.value.search) {
    const searchTerm = filters.value.search.toLowerCase();
    result = result.filter(product => 
      product.name.toLowerCase().includes(searchTerm) ||
      product.category.toLowerCase().includes(searchTerm)
    );
  }
  
  // Apply category filter
  if (filters.value.category) {
    result = result.filter(product => product.category === filters.value.category);
  }
  
  // Apply date range filter
  if (filters.value.createdFrom) {
    result = result.filter(product => product.createdAt >= filters.value.createdFrom);
  }
  
  if (filters.value.createdTo) {
    result = result.filter(product => product.createdAt <= filters.value.createdTo);
  }
  
  // Apply sorting
  result.sort((a, b) => {
    let modifier = sortDirection.value === 'asc' ? 1 : -1;
    if (a[sortField.value] < b[sortField.value]) return -1 * modifier;
    if (a[sortField.value] > b[sortField.value]) return 1 * modifier;
    return 0;
  });
  
  return result;
});

// Pagination logic
const totalPages = computed(() => {
  return Math.ceil(filteredProducts.value.length / itemsPerPage.value);
});

const visiblePages = computed(() => {
  const pages = [];
  const maxVisible = 5;
  let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2));
  let end = Math.min(totalPages.value, start + maxVisible - 1);
  
  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1);
  }
  
  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  
  return pages;
});

// Displayed products (either paginated or all for infinite scroll)
const displayedProducts = computed(() => {
  if (usePagination.value) {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredProducts.value.slice(start, end);
  } else {
    return filteredProducts.value;
  }
});

// Infinite scroll handler
const handleScroll = () => {
  if (usePagination.value || loadingMore.value) return;
  
  const container = tableContainer.value;
  if (container.scrollTop + container.clientHeight >= container.scrollHeight - 100) {
    loadMoreProducts();
  }
};

const loadMoreProducts = () => {
  if (filteredProducts.value.length > displayedProducts.value.length) {
    loadingMore.value = true;
    // Simulate API call delay
    setTimeout(() => {
      loadingMore.value = false;
    }, 1000);
  }
};

// Navigation methods
const prevPage = () => {
  if (currentPage.value > 1) currentPage.value--;
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) currentPage.value++;
};

const goToPage = (page) => {
  currentPage.value = page;
};

const viewProduct = (id) => {
  router.push('/admin/product/view');
};
const editProduct = (id) => {
  router.push('/admin/product/edit');
};
const deleteProduct = (productId) => {
  const confirmDelete = confirm('Are you sure you want to delete this product?');
  if (confirmDelete) {
    products.value = products.value.filter(product => product.id !== productId);
    console.log(`Product with ID: ${productId} deleted`);
  }
};

// Filter methods
const toggleFilterDropdown = () => {
  showFilterDropdown.value = !showFilterDropdown.value;
};


const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value;
};

const applyFilters = () => {
  showDropdown.value = false;
  currentPage.value = 1;
};

const resetFilters = () => {
  filters.value = {
    search: '',
    category: '',
    createdFrom: '',
    createdTo: ''
  };
  showDropdown.value = false;
};

onMounted(() => {
  // Simulate loading data
  loading.value = true;
  setTimeout(() => {
    loading.value = false;
  }, 1000);
});
</script>
  
<style scoped>
  @import "@/assets/css/custom.css";
</style>

  