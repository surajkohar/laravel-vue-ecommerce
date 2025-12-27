<template>
  <div class="products-listing">
    <div class="header-section">
      <h1>Products</h1>
      <div class="action-buttons">
        <button class="add-button" @click="router.push('/admin/product/add')">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
          </svg>
          Add
        </button>
        <div class="custom-dropdown">
          <button class="filter-button" @click="toggleDropdown">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
            </svg>
            Filters
          </button>
          <div class="custom-dropdown-menu" v-show="showDropdown">
            <div class="dropdown-content">
              <button class="close-dropdown" @click="toggleDropdown">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
              </button>
              <div class="filter-group">
                <label>Status</label>
                <div class="password-options d-flex"> <!-- Reusing the same class -->
  <label class="radio-option">
    <input type="radio" id="status-all" value="" v-model="filters.status" />
    <span class="radio-custom"></span>
    <span class="radio-label">All</span>
  </label>
  <label class="radio-option">
    <input type="radio" id="status-active" value="active" v-model="filters.status" />
    <span class="radio-custom"></span>
    <span class="radio-label">Active</span>
  </label>
  <label class="radio-option">
    <input type="radio" id="status-inactive" value="inactive" v-model="filters.status" />
    <span class="radio-custom"></span>
    <span class="radio-label">Inactive</span>
  </label>
</div>

              </div>
              <div class="filter-group">
                <label>Category</label>
                <MultiSelect v-model="filters.category" :options="categories" placeholder="Select category"
                  option-label="title" option-value="id" />
              </div>
              <div class="filter-group">
                <label>Brand</label>
                <Select v-model="filters.brand" placeholder="Select Brand" :options="brands" option-label="title"
                  option-value="id" />
              </div>
              <div class="filter-group">
                <label>Created From</label>
                <CustomDatePicker v-model="filters.createdFrom" placeholder="Select start date" />
              </div>
              <div class="filter-group">
                <label>Created To</label>
                <CustomDatePicker v-model="filters.createdTo" placeholder="Select end date" />
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

    <div class="d-flex justify-content-between align-items-center mb-2">
      <div>
        <h6>Manage products</h6>
      </div>
      <div class="search-container">
        <input type="text" v-model="filters.search" @input="fetchProducts" placeholder="Search products..."
          class="search-input">
        <!-- Bulk actions dropdown trigger -->
        <div class="bulk-actions-container" v-if="selectedIds.length > 0">
          <button class="bulk-actions-button" @click="toggleBulkActions">
            <i class="fas fa-ellipsis-v"></i>
          </button>
          <div class="bulk-actions-dropdown" ref="dropdownContainer" v-show="showBulkActions">
            <button class="bulk-delete-button" @click="confirmBulkDelete">
              <i class="fas fa-trash text-danger"></i>
              Delete Selected
            </button>
            <button class="bulk-status-button" @click="confirmBulkStatusChange('active')">
              <i class="fas fa-check-circle text-success"></i>
              Activate Selected
            </button>
            <button class="bulk-status-button" @click="confirmBulkStatusChange('inactive')">
              <i class="fas fa-times-circle text-warning"></i>
              Deactivate Selected
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Products Table -->
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>
              <input type="checkbox" :checked="allSelected" @change="toggleSelectAll">
            </th>
            <th @click="sortBy('id')">
              ID
              <span class="sort-icon">
                <template v-if="filters.sort_field === 'id'">
                  {{ filters.sort_direction === 'asc' ? '↑' : '↓' }}
                </template>
                <template v-else>↑↓</template>
              </span>
            </th>
            <th @click="sortBy('name')">
              Name
              <span class="sort-icon">
                <template v-if="filters.sort_field === 'name'">
                  {{ filters.sort_direction === 'asc' ? '↑' : '↓' }}
                </template>
                <template v-else>↑↓</template>
              </span>
            </th>
            <th @click="sortBy('price')">
              Price
              <span class="sort-icon">
                <template v-if="filters.sort_field === 'price'">
                  {{ filters.sort_direction === 'asc' ? '↑' : '↓' }}
                </template>
                <template v-else>↑↓</template>
              </span>
            </th>
            <th @click="sortBy('brand_title')">
              Brand
              <span class="sort-icon">
                <template v-if="filters.sort_field === 'brand_title'">
                  {{ filters.sort_direction === 'asc' ? '↑' : '↓' }}
                </template>
                <template v-else>↑↓</template>
              </span>
            </th>
            <th @click="sortBy('category_title')">
              Category
              <span class="sort-icon">
                <template v-if="filters.sort_field === 'category_title'">
                  {{ filters.sort_direction === 'asc' ? '↑' : '↓' }}
                </template>
                <template v-else>↑↓</template>
              </span>
            </th>
            <th @click="sortBy('owner_first_name')">
              Created By
              <span class="sort-icon">
                <template v-if="filters.sort_field === 'owner_first_name'">
                  {{ filters.sort_direction === 'asc' ? '↑' : '↓' }}
                </template>
                <template v-else>↑↓</template>
              </span>
            </th>
            <th @click="sortBy('created')">
              Created At
              <span class="sort-icon">
                <template v-if="filters.sort_field === 'created'">
                  {{ filters.sort_direction === 'asc' ? '↑' : '↓' }}
                </template>
                <template v-else>↑↓</template>
              </span>
            </th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in products.data" :key="product.id">
            <td>
              <input type="checkbox" :checked="selectedIds.includes(product.id)" @change="toggleSelect(product.id)">
            </td>
            <td>{{ product.id }}</td>
            <td>{{ product.name }}</td>
            <td>{{ currencySymbol }} {{ product.price }}</td>
            <td>{{ product.brand_title || '-' }}</td>
            <td>{{ product.category_title || '-' }}</td>
            <td>{{ product.owner_first_name }}</td>
            <td>{{ formatDate(product.created) }}</td>
            <td>
              <span class="status-badge" :class="product.status ? 'active' : 'inactive'">
                {{ product.status ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td class="actions-cell">
              <button class="view-button" @click="viewProduct(product.id)">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
              </button>
              <button class="edit-button" @click="editProduct(product.id)">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
              </button>
              <button class="delete-button" @click="confirmDelete(product.id)">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="3 6 5 6 21 6"></polyline>
                  <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        Loading products...
      </div>
    </div>

    <!-- Pagination -->
    <div class="pagination" v-if="products.meta && products.meta.last_page > 1">
      <button @click="prevPage" :disabled="products.meta.current_page === 1">Previous</button>
      <span v-for="page in visiblePages" :key="page" @click="goToPage(page)"
        :class="{ active: products.meta.current_page === page }">
        {{ page }}
      </span>
      <button @click="nextPage" :disabled="products.meta.current_page === products.meta.last_page">Next</button>
    </div>

    <!-- Empty State -->
    <div v-if="!loading && (!products.data || products.data.length === 0)" class="empty-state">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
      </svg>
      <h6>No products found</h6>
      <p>Try adjusting your filters or add a new product</p>
    </div>

    <ConfirmDialog ref="confirmDialog" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { useRouter } from 'vue-router';
import { API } from '@/utils/config';
import { toast } from 'vue3-toastify';
import { onClickOutside } from '@vueuse/core';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import CustomDatePicker from '@/components/CustomDatePicker.vue';
import Select from "@/components/Select.vue";
import MultiSelect from "@/components/MultiSelect.vue";

const router = useRouter();
const confirmDialog = ref();
const showBulkActions = ref(false);
const dropdownContainer = ref(null);
const selectedIds = ref([]);
const token = localStorage.getItem('auth_token');
const currencySymbol = ref('');
// State management
const products = ref({
  data: [],
  meta: {}
});
const categories = ref([]);
const brands = ref([]);
const loading = ref(false);
const showDropdown = ref(false);

// Filters and pagination
const filters = ref({
  search: '',
  status: '',
  category: '',
  brand: '',
  createdFrom: '',
  createdTo: '',
  page: 1,
  per_page: 10,
  sort_field: 'id',
  sort_direction: 'desc'
});

// Fetch products from API with pagination
const fetchProducts = async () => {
  try {
    loading.value = true;
    const queryParams = new URLSearchParams();
    Object.entries(filters.value).forEach(([key, value]) => {
      if (value) queryParams.append(key, value);
    });

    const response = await fetch(`${API.BACKEND_URL}/products?${queryParams.toString()}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
    });

    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.message || 'Failed to fetch products');
    }

    const data = await response.json();
    const { data: pageData, ...meta } = data.page;
    products.value.data = pageData;
    products.value.meta = meta;

  } catch (err) {
    console.error('Error fetching products:', err);
    toast.error(err.message || 'Failed to fetch products');
  } finally {
    loading.value = false;
  }
};

// Fetch categories for filter dropdown
const fetchData = async () => {
  try {
    const response = await fetch(`${API.BACKEND_URL}/product-fetch-data`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
    });

    if (!response.ok) {
      throw new Error('Failed to fetch categories');
    }

    const data = await response.json();
    categories.value = data.category || [];
    brands.value = data.brands || [];
    currencySymbol.value = data.currencySymbol || '₹';

  } catch (err) {
    console.error('Error fetching categories:', err);
  }
};


// Format date for display
const formatDate = (dateString) => {
  if (!dateString) return '';
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

// Sorting functionality
const sortBy = (field) => {
  if (filters.value.sort_field === field) {
    filters.value.sort_direction = filters.value.sort_direction === 'asc' ? 'desc' : 'asc';
  } else {
    filters.value.sort_field = field;
    filters.value.sort_direction = 'asc';
  }
  fetchProducts();
};

// Pagination methods
const prevPage = () => {
  if (products.value.meta.current_page > 1) {
    filters.value.page = products.value.meta.current_page - 1;
    fetchProducts();
  }
};

const nextPage = () => {
  if (products.value.meta.current_page < products.value.meta.last_page) {
    filters.value.page = products.value.meta.current_page + 1;
    fetchProducts();
  }
};

const goToPage = (page) => {
  if (page !== products.value.meta.current_page) {
    filters.value.page = page;
    fetchProducts();
  }
};

// Visible pages for pagination
const visiblePages = computed(() => {
  if (!products.value || !products.value.meta || products.value.meta.last_page <= 1) {
    return [];
  }

  const current = products.value.meta.current_page;
  const last = products.value.meta.last_page;
  const delta = 2;
  const range = [];

  for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
    range.push(i);
  }

  if (current - delta > 2) range.unshift('...');
  if (current + delta < last - 1) range.push('...');

  range.unshift(1);
  if (last !== 1) range.push(last);

  return range;
});

// Product actions
const viewProduct = (id) => {
  router.push(`/admin/product/${id}/view`);
};

const editProduct = (id) => {
  router.push(`/admin/product/${id}/edit`);
};

const confirmDelete = async (id) => {
  const confirmed = await confirmDialog.value.show({
    title: 'Delete Product',
    message: 'This cannot be undone!'
  });
  if (confirmed) {
    await deleteProduct(id);
  }
};

const deleteProduct = async (id) => {
  try {
    loading.value = true;
    const response = await fetch(`${API.BACKEND_URL}/product/${id}/delete`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`,
      }
    });

    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.message || 'Failed to delete product');
    }
    toast.success('Product deleted successfully!');
    resetFilters();
    await fetchProducts();
  } catch (err) {
    console.error('Error deleting product:', err);
    toast.error(err.message || 'Failed to delete product');
  } finally {
    loading.value = false;
  }
};

// Selection methods
const allSelected = computed(() => {
  if (!products.value.data || products.value.data.length === 0) return false;
  return products.value.data.every(product => selectedIds.value.includes(product.id));
});

const toggleSelect = (id) => {
  const index = selectedIds.value.indexOf(id);
  if (index === -1) {
    selectedIds.value.push(id);
  } else {
    selectedIds.value.splice(index, 1);
  }
};

const toggleSelectAll = (event) => {
  if (event.target.checked) {
    selectedIds.value = products.value.data.map(product => product.id);
  } else {
    selectedIds.value = [];
  }
};

// Bulk actions
const toggleBulkActions = () => {
  showBulkActions.value = !showBulkActions.value;
};

const confirmBulkDelete = async () => {
  const confirmed = await confirmDialog.value.show({
    title: 'Delete Selected Products',
    message: `Are you sure you want to delete ${selectedIds.value.length} products? This cannot be undone!`
  });

  if (confirmed) {
    await bulkDeleteProducts();
  }
};

const confirmBulkStatusChange = async (status) => {
  const action = status === 'active' ? 'activate' : 'deactivate';
  const confirmed = await confirmDialog.value.show({
    title: `${action.charAt(0).toUpperCase() + action.slice(1)} Selected Products`,
    message: `Are you sure you want to ${action} ${selectedIds.value.length} products?`
  });

  if (confirmed) {
    await bulkUpdateProductStatus(status === 'active');
  }
};

const bulkDeleteProducts = async () => {
  try {
    loading.value = true;
    const response = await fetch(`${API.BACKEND_URL}/products/bulk-delete`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
      body: JSON.stringify({ ids: selectedIds.value })
    });

    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.message || 'Failed to delete products');
    }

    toast.success(`${selectedIds.value.length} products deleted successfully!`);
    selectedIds.value = [];
    showBulkActions.value = false;
    await fetchProducts();
  } catch (err) {
    console.error('Error deleting products:', err);
    toast.error(err.message || 'Failed to delete products');
  } finally {
    loading.value = false;
  }
};

const bulkUpdateProductStatus = async (status) => {
  try {
    loading.value = true;
    const response = await fetch(`${API.BACKEND_URL}/products/bulk-status`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
      body: JSON.stringify({
        ids: selectedIds.value,
        status: status
      })
    });

    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.message || 'Failed to update products status');
    }

    const action = status ? 'activated' : 'deactivated';
    toast.success(`${selectedIds.value.length} products ${action} successfully!`);
    selectedIds.value = [];
    showBulkActions.value = false;
    await fetchProducts();
  } catch (err) {
    console.error('Error updating products status:', err);
    toast.error(err.message || 'Failed to update products status');
  } finally {
    loading.value = false;
  }
};

// Filter methods
const toggleDropdown = () => showDropdown.value = !showDropdown.value;
const applyFilters = () => {
  showDropdown.value = false;
  filters.value.page = 1;
  fetchProducts();
};
const resetFilters = () => {
  filters.value = {
    ...filters.value,
    search: '',
    status: '',
    category: '',
    brand: '',
    createdFrom: '',
    createdTo: '',
    page: 1
  };
  showDropdown.value = false;
  fetchProducts();
  selectedIds.value = [];
};

onClickOutside(dropdownContainer, () => {
  if (showBulkActions.value) {
    showBulkActions.value = false;
  }
});

onMounted(() => {
  fetchProducts();
  fetchData();
});
</script>

<style scoped>
@import "@/assets/css/custom.css";

.status-badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
}

.status-badge.active {
  background-color: #e6f7ee;
  color: #10b759;
}

.status-badge.inactive {
  background-color: #fef0f0;
  color: #f1416c;
}
</style>