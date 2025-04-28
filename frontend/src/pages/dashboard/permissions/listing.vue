<template>
  <div class="products-listing">
    <div class="header-section">
      <h1>Permissions</h1>
      <div class="action-buttons">
        <button class="add-button" @click="router.push('/admin/permission/add')">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
          </svg>
          Add
        </button>
        <div class="custom-dropdown">
          <button class="filter-button" @click="toggleDropdown" >
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
    
    <div class="d-flex justify-content-between align-items-center mb-2">
      <div>
        <h6>Manage system roles and permissions</h6>
      </div>
      <div class="search-container">
        <input type="text" v-model="filters.search" @input="fetchPermissions" placeholder="Search roles..." class="search-input">
      </div>
    </div>

    <!-- Roles Table -->
    <div class="table-container">
      <table>
        <thead>
        <tr>
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
            Role Name
            <span class="sort-icon">
              <template v-if="filters.sort_field === 'name'">
                {{ filters.sort_direction === 'asc' ? '↑' : '↓' }}
              </template>
              <template v-else>↑↓</template>
            </span>
          </th>
          <th>Guard Name</th>
          <th @click="sortBy('created_at')">
            Created At
            <span class="sort-icon">
              <template v-if="filters.sort_field === 'created_at'">
                {{ filters.sort_direction === 'asc' ? '↑' : '↓' }}
              </template>
              <template v-else>↑↓</template>
            </span>
          </th>
          <th>Actions</th>
        </tr>
      </thead>
        <tbody>
          <tr v-for="role in roles.data" :key="role.id">
            <td>{{ role.id }}</td>
            <td>{{ role.name }}</td>
            <td>{{ role.guard_name }}</td>
            <td>{{ formatDate(role.created_at) }}</td>
            <td class="actions-cell">
              <button class="edit-button" @click="editPermission(role.id)">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
              </button>
              <button class="delete-button" @click="deleteRole(role.id)">
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
        Loading roles...
      </div>
    </div>

    <!-- Pagination -->
    <div class="pagination" v-if="roles.meta && roles.meta.last_page > 1">
      <button @click="prevPage" :disabled="roles.meta.current_page === 1">Previous</button>
      <span v-for="page in visiblePages" :key="page" 
            @click="goToPage(page)" 
            :class="{ active: roles.meta.current_page === page }">
        {{ page }}
      </span>
      <button @click="nextPage" :disabled="roles.meta.current_page === roles.meta.last_page">Next</button>
    </div>

    <!-- Empty State -->
    <div v-if="!loading && (!roles.data || roles.data.length === 0)" class="empty-state">
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
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { API } from '@/utils/config';
import { useAuthStore } from '@/store/auth';


const router = useRouter();

// State management
const roles = ref({
  data: [],
  meta: {}
});
const loading = ref(false);
const error = ref('');
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

// Fetch roles from API with pagination
const fetchPermissions = async () => {
  try {
    loading.value = true;
    const token = localStorage.getItem('auth_token');
    if(!token){
      router.push('/login');
    }
    const queryParams = new URLSearchParams();
    Object.entries(filters.value).forEach(([key, value]) => {
      if (value) queryParams.append(key, value);
    });

    const response = await fetch(`${API.BACKEND_URL}/permissions?${queryParams.toString()}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`, // ✅ Attach token here
      },
    });

    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.message || 'Failed to fetch roles');
    }

    const data = await response.json();
    roles.value = data;
  } catch (err) {
    error.value = err.message;
    console.error('Error fetching roles:', err);
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
  fetchPermissions();
};

// Pagination methods
const prevPage = () => {
  if (roles.value.meta.current_page > 1) {
    filters.value.page = roles.value.meta.current_page - 1;
    fetchPermissions();
  }
};

const nextPage = () => {
  if (roles.value.meta.current_page < roles.value.meta.last_page) {
    filters.value.page = roles.value.meta.current_page + 1;
    fetchPermissions();
  }
};

const goToPage = (page) => {
  if (page !== roles.value.meta.current_page) {
    filters.value.page = page;
    fetchPermissions();
  }
};

// Visible pages for pagination
const visiblePages = computed(() => {
  if (!roles.value.meta) return [];
  
  const current = roles.value.meta.current_page;
  const last = roles.value.meta.last_page;
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
const editPermission = (id) => {
  router.push(`/admin/permission/${id}/edit`);
};

const deleteRole = async (id) => {
  if (!confirm('Are you sure you want to delete this role?')) return;
  
  try {
    loading.value = true;
    const response = await fetch(`/api/roles/${id}`, {
      method: 'DELETE'
    });
    
    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.message || 'Failed to delete role');
    }
    
    // Refresh the roles list
    await fetchPermissions();
  } catch (err) {
    error.value = err.message;
    console.error('Error deleting role:', err);
  } finally {
    loading.value = false;
  }
};

// Filter methods
const toggleDropdown = () => showDropdown.value = !showDropdown.value;
const applyFilters = () => {
  showDropdown.value = false;
  filters.value.page = 1;
  fetchPermissions();
};
const resetFilters = () => {
  filters.value = {
    ...filters.value,
    search: '',
    createdFrom: '',
    createdTo: '',
    page: 1
  };
  showDropdown.value = false;
  fetchPermissions();
};

onMounted(() => {
  fetchPermissions();
});
</script>

<style scoped>
@import "@/assets/css/custom.css";
</style>