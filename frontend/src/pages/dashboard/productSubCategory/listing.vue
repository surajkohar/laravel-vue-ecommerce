<template>
  <div class="products-listing">
    <div class="header-section">
      <h1>Product Sub Categories</h1>
      <div class="action-buttons">
        <button class="add-button" @click="router.push('/admin/product-subCategory/add')">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
          </svg>
          Add
        </button>
        <div class="custom-dropdown">
          <button class="filter-button" @click="toggleDropdown">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
            </svg>
            Filters
          </button>
          <div class="custom-dropdown-menu" v-show="showDropdown">
            <div class="dropdown-content">
              <button class="close-dropdown" @click="toggleDropdown">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
              </button>
              <!-- In your filters dropdown section -->
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
        <h6>Manage product sub categories</h6>
      </div>
      <div class="search-container">
        <input type="text" v-model="filters.search" @input="fetchCategories" placeholder="Search categories..." class="search-input">
        <!-- Bulk actions dropdown trigger -->
        <div class="bulk-actions-container" v-if="selectedIds.length > 0">
          <button class="bulk-actions-button" @click="toggleBulkActions">
              <i class="fas fa-ellipsis-v"></i>
          </button>
          <div class="bulk-actions-dropdown" ref="dropdownContainer"  v-show="showBulkActions">
            <button class="bulk-delete-button" @click="confirmBulkDelete">
                <i class="fas fa-trash text-danger"></i>
              Delete Selected
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Categories Table -->
    <div class="table-container">
      <table>
        <thead>
        <tr>
          <th>
            <input type="checkbox" 
                   :checked="allSelected" 
                   @change="toggleSelectAll">
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
          <th @click="sortBy('title')">
            Title
            <span class="sort-icon">
              <template v-if="filters.sort_field === 'title'">
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
          <th>Actions</th>
        </tr>
      </thead>
        <tbody>
          <tr v-for="category in categories.data" :key="category.id">
             <td>
              <input type="checkbox" 
                     :checked="selectedIds.includes(category.id)" 
                     @change="toggleSelect(category.id)">
            </td>
            <td>{{ category.id }}</td>
            <td>{{ category.title }}</td>
            <td>{{ category.owner_first_name }}</td>
            <td>{{ formatDate(category.created) }}</td>
            <td class="actions-cell">
              <button class="edit-button" @click="editCategory(category.id)">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
              </button>
              <button class="delete-button" @click="confirmDelete(category.id)">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
        Loading categories...
      </div>
    </div>
    <ConfirmDialog ref="confirmDialog" />
    <!-- Pagination -->
    <div class="pagination" v-if="categories.meta && categories.meta.last_page > 1">
      <button @click="prevPage" :disabled="categories.meta.current_page === 1">Previous</button>
      <span v-for="page in visiblePages" :key="page" 
            @click="goToPage(page)" 
            :class="{ active: categories.meta.current_page === page }">
        {{ page }}
      </span>
      <button @click="nextPage" :disabled="categories.meta.current_page === categories.meta.last_page">Next</button>
    </div>

    <!-- Empty State -->
    <div v-if="!loading && (!categories.data || categories.data.length === 0)" class="empty-state">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
        <circle cx="9" cy="7" r="4"></circle>
        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
      </svg>
      <h6>No record found</h6>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted ,onBeforeUnmount} from 'vue';
import { useRouter } from 'vue-router';
import { API } from '@/utils/config';
import { toast } from 'vue3-toastify'
import { onClickOutside } from '@vueuse/core';
import ConfirmDialog from '@/components/ConfirmDialog.vue'
import CustomDatePicker from '@/components/CustomDatePicker.vue';

const router = useRouter();
const confirmDialog = ref();
const showBulkActions = ref(false);
const dropdownContainer = ref(null);
const selectedIds = ref([]);
const token = localStorage.getItem('auth_token');

// State management
const categories = ref({
  data: [],
  meta: {}
});
const loading = ref(false);
const showDropdown = ref(false);

// Filters and pagination
const filters = ref({
  search: '',
  createdFrom: '',
  createdTo: '',
  page: 1,
  per_page: 10,
  sort_field: 'id',
  sort_direction: 'asc'
});

// Fetch categories from API with pagination 
const fetchCategories = async () => {
  try {
    loading.value = true;
    const token = localStorage.getItem('auth_token');
    if (!token) {
      router.push('/login');
    }
    const queryParams = new URLSearchParams();
    Object.entries(filters.value).forEach(([key, value]) => {
      if (value) queryParams.append(key, value);
    });

    const response = await fetch(`${API.BACKEND_URL}/product-subcategories?${queryParams.toString()}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
    });

    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.message || 'Failed to fetch categories');
    }

    const data = await response.json();
      const { data: pageData, ...meta } = data.page;
      categories.value.data = pageData;
      categories.value.meta = meta;
      
      } catch (err) {
    console.error('Error fetching categories:', err);
  } finally {
    loading.value = false;
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
  fetchCategories();
};

// Pagination methods
const prevPage = () => {
  if (categories.value.meta.current_page > 1) {
    filters.value.page = categories.value.meta.current_page - 1;
    fetchCategories();
  }
};

const nextPage = () => {
  if (categories.value.meta.current_page < categories.value.meta.last_page) {
    filters.value.page = categories.value.meta.current_page + 1;
    fetchCategories();
  }
};

const goToPage = (page) => {
  if (page !== categories.value.meta.current_page) {
    filters.value.page = page;
    fetchCategories();
  }
};

// Visible pages for pagination
const visiblePages = computed(() => {
  if (!categories.value || !categories.value.meta || categories.value.meta.last_page <= 1) {
    return [];
  }

  const current = categories.value.meta.current_page;
  const last = categories.value.meta.last_page;
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

// Role actions
const editCategory = (id) => {
  router.push(`/admin/product-subCategory/${id}/edit`);
};

const confirmDelete = async (id) => {
  const confirmed = await confirmDialog.value.show({
    title: 'Delete Sub Category',
    message: 'This cannot be undone!'
  });
    if (confirmed) 
    {
    await deleteCategory(id);
  }
};

const deleteCategory = async (id) => {
  try {
    loading.value = true;
    const response = await fetch(`${API.BACKEND_URL}/product-subcategories/${id}/delete`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`,
      }
    });
    
    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.message || 'Failed to delete category');
    }
    toast.success('Sub Category deleted successfully!')
    resetFilters();
    await fetchCategories();
  } catch (err) {
    console.error('Error deleting category:', err);
  } finally {
    loading.value = false;
  }
};

const allSelected = computed(() => {
  if (!categories.value.data || categories.value.data.length === 0) return false;
  return categories.value.data.every(category => selectedIds.value.includes(category.id));
});

// Toggle selection for a single item
const toggleSelect = (id) => {
  const index = selectedIds.value.indexOf(id);
  if (index === -1) {
    selectedIds.value.push(id);
  } else {
    selectedIds.value.splice(index, 1);
  }
};

// Toggle select all items
const toggleSelectAll = (event) => {
  if (event.target.checked) {
    selectedIds.value = categories.value.data.map(category => category.id);
  } else {
    selectedIds.value = [];
  }
};

// Toggle bulk actions dropdown
const toggleBulkActions = () => {
  showBulkActions.value = !showBulkActions.value;
};


// Confirm bulk delete
const confirmBulkDelete = async () => {
  const confirmed = await confirmDialog.value.show({
    title: 'Delete Selected Categories',
    message: `Are you sure you want to delete ${selectedIds.value.length} categories? This cannot be undone!`
  });
  
  if (confirmed) {
    await bulkDeleteCategories('delete');
  }
};

// Bulk delete categories
const bulkDeleteCategories = async (action) => {
  try {
    loading.value = true;
    const response = await fetch(`${API.BACKEND_URL}/product-subcategories/bulk-action/${action}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
      body: JSON.stringify({ ids: selectedIds.value })
    });
    
    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.message || 'Failed to delete categories');
    }
    
    toast.success(`${selectedIds.value.length} sub categories deleted successfully!`);
    selectedIds.value = [];
    showBulkActions.value = false;
    await fetchCategories();
  } catch (err) {
    console.error('Error deleting subcategories:', err);
    toast.error(err.message || 'Failed to delete categories');
  } finally {
    loading.value = false;
  }
};


// Filter methods
const toggleDropdown = () => showDropdown.value = !showDropdown.value;
const applyFilters = () => {
  showDropdown.value = false;
  filters.value.page = 1;
  fetchCategories();
};
const resetFilters = () => {
  filters.value = {
    ...filters.value,
    search: '',
    createdFrom: null,
    createdTo: null,
    page: 1
  };
  showDropdown.value = false;
  fetchCategories();
  selectedIds.value =[];
};

onClickOutside(dropdownContainer, () => {
  if (showBulkActions.value) {
    showBulkActions.value = false;
  }
});

onMounted(() => {
  fetchCategories();
});

</script>

<style scoped>
@import "@/assets/css/custom.css";
</style>
