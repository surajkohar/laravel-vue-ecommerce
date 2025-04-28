<template>
  <div class="add-product-container">
    <div class="header-section-add">
      <h1>Add New Permission</h1>
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
          <div class="permissions-grid">
            <div v-for="(permissions, category) in groupedPermissions" :key="category">
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
            </div>
          </div>
          <small class="text-danger" v-if="errors && errors.permissions">{{ errors.permissions[0] }}</small>
        </div>

          <h5>Status</h5>
          <div class="d-flex align-items-center">
            <label class="toggle-switch">
              <input type="checkbox" v-model="role.status" :true-value="1" :false-value="0">
              <span class="slider round"></span>
            </label>
            &nbsp;&nbsp;
            <span class="toggle-label">{{ role.status === 1 ? 'Active' : 'Inactive' }}</span>
          </div>


        <div class="form-actions">
          <button class="cancel-button" @click="goBack">
            Cancel
          </button>
          <button class="save-button" @click="saveRole" :disabled="loading">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
              <polyline points="17 21 17 13 7 13 7 21"></polyline>
              <polyline points="7 3 7 8 15 8"></polyline>
            </svg>
            {{ loading ? 'Saving...' : 'Save' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { API } from '@/utils/config';
import { toast } from 'vue3-toastify'

const router = useRouter()

// State
const role = ref({
  name: '',
  permissions: [],
  status: 1
})

const permissions = ref([])
const groupedPermissions = ref({})
const errors = ref({})
const loading = ref(false)

const token = localStorage.getItem('auth_token');

// Fetch permissions when component mounts
onMounted(async () => {
  if (!token) {
    router.push('/login');
  }
  try {
    loading.value = true;
    const response = await fetch(`${API.BACKEND_URL}/rolesAndPermissions`, {
      headers: {
        'Authorization': `Bearer ${token}`,
      },
    });

    if (response.status === 302) {
      // Handle the redirect
      const newUrl = response.headers.get('Location');
      const redirectResponse = await fetch(newUrl, {
        headers: {
          'Authorization': `Bearer ${token}`,
        },
      });
      if (redirectResponse.ok) {
        const data = await redirectResponse.json();
        if (data.status && data.permissions) {
          permissions.value = data.permissions;
          groupPermissions();
        }
      } else {
        throw new Error('Failed to fetch permissions after redirect');
      }
    } else if (!response.ok) {
      throw new Error('Failed to fetch permissions');
    } else {
      const data = await response.json();
      if (data.status && data.permissions) {
        permissions.value = data.permissions;
        groupPermissions();
      }
    }
  } catch (error) {
    console.error('Error fetching permissions:', error);
  } finally {
    loading.value = false;
  }
});


// Group permissions by their category
const groupPermissions = () => {
  const grouped = {}
  permissions.value.forEach(permission => {
    const category = permission.category || 'General'
    if (!grouped[category]) {
      grouped[category] = []
    }
    grouped[category].push(permission)
  })
  groupedPermissions.value = grouped
}

// Save role to backend
const saveRole = async () => {
  loading.value = true
  errors.value = {}

  try {
    const response = await fetch(`${API.BACKEND_URL}/roles/create`, {
      method: 'POST',
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
      throw new Error(data.message || 'Failed to create role')
    }

    toast.success('Role created successfully!')
    setTimeout(() => {
      router.push('/admin/roles')
    }, 1500)
    } catch (error) {
    if (!errors.value.name) {
      toast.error(error.message || 'An error occurred while creating the role');
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

/* Additional role-specific styles */

</style>