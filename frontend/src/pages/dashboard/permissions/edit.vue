<template>
  <div class="add-product-container">
    <div class="header-section-add">
      <h1>Edit Permission</h1>
      <div class="action-buttons">
        <button class="cancel-button" @click="goBack">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
          </svg>
          Back
        </button>
      </div>
    </div>

    <div class="form-container">
      <div class="form-card">
        <div class="form-section">
          <h5>Basic Information</h5>
          <div class="form-group full-width-field">
            <label>Role Name</label>
            <input 
              type="text" 
              v-model="role.name" 
              placeholder="Enter role name"
              :class="{ 'error-field': errors.name }"
            >
            <span class="error-message" v-if="errors.name">{{ errors.name[0] }}</span>
          </div>
        </div>

        <div class="form-section">
          <h5>Permissions</h5>
          <div class="permission-options">
            <label v-for="permission in permissions" :key="permission.id">
              <input 
                type="checkbox" 
                v-model="role.permissions" 
                :value="permission.id"
                :id="`permission-${permission.id}`"
              >
              <span class="checkbox-label">{{ permission.name }}</span>
            </label>
          </div>
          <small class="text-danger" v-if="errors && errors.permissions">{{ errors.permissions[0] }}</small>
        </div>

        <div class="form-section">
          <h5>Status</h5>
          <div class="d-flex align-items-center">
            <label class="toggle-switch">
              <input type="checkbox" v-model="role.status" :true-value="1" :false-value="0">
              <span class="slider round"></span>
            </label>
            &nbsp;&nbsp;
            <span class="toggle-label">{{ role.status === 1 ? 'Active' : 'Inactive' }}</span>
          </div>
        </div>

        <div class="form-actions">
          <button class="cancel-button" @click="goBack">
            Cancel
          </button>
          <button class="save-button" @click="updateRole" :disabled="loading">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
              <polyline points="17 21 17 13 7 13 7 21"></polyline>
              <polyline points="7 3 7 8 15 8"></polyline>
            </svg>
            {{ loading ? 'Updating...' : 'Update' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { API } from '@/utils/config';
import { toast } from 'vue3-toastify'

const route = useRoute()
const router = useRouter()

// State
const role = ref({
  id: null,
  name: '',
  permissions: [],
  status: 1
})

const permissions = ref([])
const errors = ref({})
const loading = ref(false)
const token = localStorage.getItem('auth_token');

// Fetch role data and permissions when component mounts
onMounted(async () => {
  if (!token) {
    router.push('/login');
  }
  
  try {
    const roleId = route.params.id;
    
    // Fetch role data with permissions
    const roleResponse = await fetch(`${API.BACKEND_URL}/roles/${roleId}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
      },
    });
    
    if (!roleResponse.ok) throw new Error('Failed to fetch role data');
    const roleData = await roleResponse.json();
    
    if (roleData.status && roleData.role) {
      role.value = {
        id: roleData.role.id,
        name: roleData.role.name.replace(/\b\w/g, char => char.toUpperCase()),
        permissions: roleData.role.permissions || [],
        status: roleData.role.status
      };
    }

    // Fetch all available permissions
    const permissionsResponse = await fetch(`${API.BACKEND_URL}/rolesAndPermissions`, {
      headers: {
        'Authorization': `Bearer ${token}`,
      },
    });
    
    if (!permissionsResponse.ok) throw new Error('Failed to fetch permissions');
    const permissionsData = await permissionsResponse.json();
    
    if (permissionsData.status && permissionsData.permissions) {
      // Store permissions directly without grouping
      permissions.value = permissionsData.permissions;
    }
    
  } catch (error) {
    console.error('Error fetching data:', error);
    toast.error(error.message || 'Failed to load role data');
  } finally {
    loading.value = false;
  }
});


// Update role in backend
const updateRole = async () => {
  loading.value = true
  errors.value = {}

  try {
    const response = await fetch(`${API.BACKEND_URL}/roles/${role.value.id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
      body: JSON.stringify({
        name: role.value.name,
        permissions: role.value.permissions,
        status: role.value.status
      })
    })

    const data = await response.json()

    if (!response.ok) {
      if (response.status === 422) {
        errors.value = data.errors || {}
      }
      throw new Error(data.message || 'Failed to update role')
    }
    toast.success('Role updated successfully!')
    setTimeout(() => {
      router.push('/admin/roles')
    }, 1500)
  } catch (error) {
    if (!errors.value.name) {
      toast.error(error.message || 'An error occurred while updating the role');
    }
  } finally {
    loading.value = false
  }
}

const goBack = () => {
  router.go(-1)
}
</script>

<style scoped>
@import "@/assets/css/custom.css";

/* Reuse the same styles from add role page */
.permission-options {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 10px;
}

@media (min-width: 768px) {
  .permission-options {
    grid-template-columns: repeat(5, 1fr);
  }
}

</style>