<template>
  <div class="products-listing">
    <!-- Header Section (unchanged) -->
    <div class="header-section">
      <h1>Email Templates</h1>
      <div class="action-buttons">
        <button class="add-button" @click="router.push('/admin/email-template/add')">
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
    
    <!-- Search and Title Section (unchanged) -->
    <div class="d-flex justify-content-between align-items-center mb-2">
      <div>
        <h6>Manage Email Templates here!</h6>
      </div>
      <div class="search-container">
        <input type="text" v-model="filters.search" @input="fetchTemplates" placeholder="Search templates..." class="search-input">
      </div>
    </div>

    <!-- Email Templates Table -->
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
            <th @click="sortBy('title')">
              Title
              <span class="sort-icon">
                <template v-if="filters.sort_field === 'title'">
                  {{ filters.sort_direction === 'asc' ? '↑' : '↓' }}
                </template>
                <template v-else>↑↓</template>
              </span>
            </th>
            <th @click="sortBy('subject')">
              Subject
              <span class="sort-icon">
                <template v-if="filters.sort_field === 'subject'">
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
          <tr v-for="template in page.data" :key="template.id">
            <td>{{ template.id }}</td>
            <td>{{ template.title }}</td>
            <td>{{ template.subject }}</td>
            <td>{{ formatDate(template.created) }}</td>
            <td class="actions-cell">
              <button class="view-button" @click="viewTemplate(template.id)">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
              </button>
              <button class="edit-button" @click="editTemplate(template.id)">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
              </button>
              <button class="delete-button" @click="deleteTemplate(template.id)">
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
        Loading email templates...
      </div>
    </div>

    <!-- Pagination -->
    <Pagination 
      v-if="page && page.last_page > 1"
      :meta="page"
      @page-changed="goToPage"
    />

    <!-- Empty State -->
    <div v-if="!loading && (!page.data || page.data.length === 0)" class="empty-state">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
        <polyline points="22,6 12,13 2,6"></polyline>
      </svg>
      <h6>No email templates found</h6>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { API } from '@/utils/config';
import Pagination from '@/components/Pagination.vue';

const router = useRouter();

// State management
const page = ref({
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

// Fetch templates from API with pagination
const fetchTemplates = async () => {
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

    const response = await fetch(`${API.BACKEND_URL}/email-templates?${queryParams.toString()}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
    });

    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.message || 'Failed to fetch email templates');
    }

    const data = await response.json();
    page.value = data.page; // Adjusted to match your API response structure
  } catch (err) {
    error.value = err.message;
    console.error('Error fetching email templates:', err);
  } finally {
    loading.value = false;
  }
};

// Format date for display
const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

// Sorting functionality
const sortBy = (field) => {
  if (filters.value.sort_field === field) {
    filters.value.sort_direction = filters.value.sort_direction === 'asc' ? 'desc' : 'asc';
  } else {
    filters.value.sort_field = field;
    filters.value.sort_direction = 'asc';
  }
  fetchTemplates();
};

// Pagination methods
const goToPage = (pageNum) => {
  filters.value.page = pageNum;
  fetchTemplates();
};

// Template actions
const viewTemplate = (id) => {
  router.push(`/admin/email-template/${id}`);
};

const editTemplate = (id) => {
  router.push(`/admin/email-template/${id}/edit`);
};

const deleteTemplate = async (id) => {
  if (!confirm('Are you sure you want to delete this template?')) return;
  
  try {
    loading.value = true;
    const token = localStorage.getItem('auth_token');
    const response = await fetch(`${API.BACKEND_URL}/email-templates/${id}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`,
      },
    });
    
    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.message || 'Failed to delete template');
    }
    
    // Refresh the templates list
    await fetchTemplates();
  } catch (err) {
    error.value = err.message;
    console.error('Error deleting template:', err);
  } finally {
    loading.value = false;
  }
};

// Filter methods
const toggleDropdown = () => showDropdown.value = !showDropdown.value;
const applyFilters = () => {
  showDropdown.value = false;
  filters.value.page = 1;
  fetchTemplates();
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
  fetchTemplates();
};

onMounted(() => {
  fetchTemplates();
});
</script>

<style scoped>
@import "@/assets/css/custom.css";

.email-templates {
  padding: 20px;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.filters {
  display: flex;
  gap: 10px;
  align-items: center;
}

.template-table {
  width: 100%;
  border-collapse: collapse;
}

.template-table th, .template-table td {
  padding: 12px 15px;
  text-align: left;
  border-bottom: 1px solid #eee;
}

.template-table th {
  background-color: #f8f9fa;
  font-weight: 600;
}

.badge {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  text-transform: capitalize;
}

.badge.admin {
  background: #e0f2fe;
  color: #0369a1;
}

.badge.client {
  background: #dcfce7;
  color: #166534;
}

.badge.system {
  background: #fef3c7;
  color: #92400e;
}

.actions {
  display: flex;
  gap: 5px;
}

.btn {
  padding: 6px 12px;
  border-radius: 4px;
  font-size: 14px;
  cursor: pointer;
}

.btn-sm {
  padding: 4px 8px;
  font-size: 12px;
}

.btn-edit {
  background: #3b82f6;
  color: white;
  border: none;
}

.btn-test {
  background: #10b981;
  color: white;
  border: none;
}

.btn-delete {
  background: #ef4444;
  color: white;
  border: none;
}

.btn-delete:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>