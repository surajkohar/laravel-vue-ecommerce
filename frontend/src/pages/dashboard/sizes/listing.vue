<template>
  <div class="add-product-container">
    <div class="header-section-add">
      <h1>Manage Sizes</h1>
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
        <h5>Sizes</h5>
        <div class="sizes-list">
          <div class="row" v-for="(size, index) in sizes" :key="index">
            <div class="col-11">
              <input  
                type="text" 
                class="form-control" 
                v-model="sizes[index].size_title" 
                placeholder="Enter size"
                :class="{ 'error-field': errors.sizes && errors.sizes[index] }"
              />
              <span class="error-message" v-if="errors.sizes && errors.sizes[index]">{{ errors.sizes[index][0] }}</span>
            </div>
            <div class="col-1">
              <button  class="remove-button mb-1" @click="removeSize(index)">
                &times; <!-- Cross icon for removal -->
              </button>
            </div>
          </div>
        </div>
        <button  class="add-button" @click="addSize">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 4v16m8-8H4" />
          </svg>
          Add Size
        </button>

        <div class="form-actions">
          <button class="cancel-button" @click="goBack">
            Cancel
          </button>
          <button class="save-button" @click="saveSizes" :disabled="loading">
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
const sizes = ref(['']);
const errors = ref({})
const loading = ref(false)

const token = localStorage.getItem('auth_token');

// Fetch sizes when component mounts
onMounted(async () => {
  if (!token) {
    router.push('/login');
  }
  
  try {
    loading.value = true;
    const response = await fetch(`${API.BACKEND_URL}/sizes`, {
      headers: {
        'Authorization': `Bearer ${token}`,
      },
    });

    if (!response.ok) {
      throw new Error('Failed to fetch sizes');
    }

    const data = await response.json();
    sizes.value = data.sizes && data.sizes.length > 0 ? data.sizes : [''];
  } catch (error) {
    console.error('Error fetching sizes:', error);
  } finally {
    loading.value = false;
  }
});

// Add a new size input
const addSize = () => {
  sizes.value.push('');
}

// Remove a size input
const removeSize = (index) => {
  sizes.value.splice(index, 1);
}


// Save role to backend
const saveSizes = async () => {
  console.log('sizes',sizes.value);

  loading.value = true
  errors.value = {}

  try {
    const response = await fetch(`${API.BACKEND_URL}/sizes/add`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
      body: JSON.stringify({
        sizes: sizes.value, // Send sizes
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
      router.push('/admin/sizes')
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

/* Additional styles */
.sizes-list {
  margin-bottom: 20px;
}

.remove-button {
  background: none;
  border: none;
  color: red;
  cursor: pointer;
  font-size: 20px;
}

.add-button {
  margin-top: 10px;
  display: inline-flex;
  align-items: center;
}
</style>
