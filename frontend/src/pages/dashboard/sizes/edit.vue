<template>
  <div class="add-product-container">
    <div class="header-section-add">
      <h1>Edit category</h1>
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
            <label>Title</label>
            <input 
              type="text" 
              v-model="title" 
              placeholder="Enter role title"
              :class="{ 'error-field': errors.title }"
            >
            <span class="error-message" v-if="errors.title">{{ errors.title[0] }}</span>
          </div>

          <div class="form-group full-width-field">
            <label>Description</label>
            <input 
              type="text" 
              v-model="desc" 
              placeholder="Enter role description"
              :class="{ 'error-field': errors.desc }"
            >
            <span class="error-message" v-if="errors.desc">{{ errors.desc[0] }}</span>
          </div>
        </div>

        
        <div class="form-actions">
          <button class="cancel-button" @click="goBack">
            Cancel
          </button>
          <button class="save-button" @click="save" :disabled="loading">
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
import { useRouter,useRoute } from 'vue-router'
import { API } from '@/utils/config';
import { toast } from 'vue3-toastify'

const router = useRouter()
const route = useRoute(); 
const categoryId = ref('')
const title = ref('')
const desc = ref('')
const errors = ref({})
const loading = ref(false)

const token = localStorage.getItem('auth_token');

const save = async () =>{
  loading.value = true;
  try {
    const response = await fetch(`${API.BACKEND_URL}/product-category/${categoryId.value}/update`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
      body: JSON.stringify({
        title: title.value,
        description: desc.value
      })
      
    })

    const data = await response.json()

    if (!response.ok) {
      if (response.status === 422) {
        errors.value = data.errors || {}
      }
      throw new Error(data.message || 'Failed to save')
    }

    toast.success('Category created successfully!')
    setTimeout(() => {
      router.push('/admin/product-category')
    }, 500)
    } catch (error) {
    if (!errors.value.title) {
      toast.error(error.message || 'An error occurred while creating the role');
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  categoryId.value = route.params.id;
  fetchCategories();
});

const fetchCategories = async() => {
    const response = await fetch(`${API.BACKEND_URL}/product-category/${categoryId.value}/edit`, {
    method:'GET',
    headers:{
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`,
    },
  })

    const data = await response.json()
    if (response.ok) {
       title.value = data.category.title
       desc.value = data.category.description
    }
    throw new Error(data.message || 'Failed to save')
}

const goBack = () => {
  router.go(-1)
}
</script>

<style scoped>
@import "@/assets/css/custom.css";

/* Additional role-specific styles */

</style>