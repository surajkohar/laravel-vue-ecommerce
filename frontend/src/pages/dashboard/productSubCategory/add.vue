<template>
  <div class="add-product-container">
    <div class="header-section-add">
      <h1>Add new Sub category</h1>
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

            <div class="form-group">
              <label>Categories</label>
              <MultiSelect
                v-model="category_ids"
                :options="categories"
                placeholder="Select categories"
                option-label="title"
                option-value="id"
              />
                <span class="error-message" v-if="errors.category_ids">{{ errors.category_ids[0] }}</span>
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
import { useRouter } from 'vue-router'
import { API } from '@/utils/config';
import { toast } from 'vue3-toastify'
import MultiSelect from "@/components/MultiSelect.vue";

const router = useRouter()
const title = ref('')
const desc = ref('')
const errors = ref({})
const loading = ref(false)
const categories = ref([])
const category_ids = ref([])

const token = localStorage.getItem('auth_token');

onMounted( async()=>{
  await fetchCategory();
})

const fetchCategory =  async() =>{
  try{
    if (!token) {
      router.push('/login');
    }
    const response = await fetch(`${API.BACKEND_URL}/product-fetch-data`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
    });

    const data = await response.json(); 
    if (response.ok) {
      categories.value = data.category; // Use 'data.category' to set categories
    } else {
      toast.error(data.message || 'Failed to fetch categories'); // Handle errors
    }
  }catch(error){
      toast.error(error.message || 'An error occurred');
  }
}

const save = async () =>{
  console.log('category_ids',category_ids.value);
  try {
    const response = await fetch(`${API.BACKEND_URL}/product-subcategories/add`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
      body: JSON.stringify({
        title: title.value,
        description: desc.value,
        category_ids:category_ids.value,
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
      router.push('/admin/product-subCategory')
    }, 500)
    } catch (error) {
    if (!errors.value.title) {
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