<template>
  <div class="add-product-container">
    <div class="header-section-add">
      <h1>Brands</h1>
      <div class="action-buttons">
        <button class="cancel-button" @click="goBack">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
          <h5>Manage Brands</h5>

          <div class="size-inputs">
            <div v-for="(size, index) in brands" :key="index" class="form-group full-width-field size-item">
              <input type="text" v-model="size.value" placeholder="Enter size (e.g., S, M, L)"
                :class="{ 'error-field': errors[`sizes.${index}`] }">
              <button class="remove-size-btn" @click="confirmDelete(size.id, index)"
                v-if="brands.length > 1 || index > 0">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
              </button>
              <span class="error-message" v-if="errors[`sizes.${index}`]">
                {{ errors[`brands.${index}`][0] }}
              </span>
            </div>
          </div>

          <button class="add-more-button" @click="addNewSize">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add More
          </button>
        </div>

        <div class="form-actions">
          <button class="cancel-button" @click="goBack">
            Cancel
          </button>
          <button class="save-button" @click="saveSizes" :disabled="loading">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
              <polyline points="17 21 17 13 7 13 7 21"></polyline>
              <polyline points="7 3 7 8 15 8"></polyline>
            </svg>
            {{ loading ? 'Saving...' : 'Save' }}
          </button>
        </div>
      </div>
    </div>
    <ConfirmDialog ref="confirmDialog" />

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { API } from '@/utils/config';
import { toast } from 'vue3-toastify'
import ConfirmDialog from '@/components/ConfirmDialog.vue'

const confirmDialog = ref();
const router = useRouter()
const route = useRoute()
const token = localStorage.getItem('auth_token')

const brands = ref([{ value: '', id: null }])
const errors = ref({})
const loading = ref(false)
const editingId = ref(null)

// Fetch existing sizes on mounted
onMounted(async () => {
  fetchBrands();
})

const fetchBrands = async () => {
  try {
    loading.value = true
    const response = await fetch(`${API.BACKEND_URL}/brands`, {
      headers: {
        'Authorization': `Bearer ${token}`,
      }
    })

    if (!response.ok) throw new Error('Failed to fetch sizes')

    const data = await response.json()

    // Show existing sizes if found, else show one empty input
    if (data.brands && data.brands.length > 0) {
        brands.value = data.brands.map(b => ({
          id: b.id,
          value: b.title
        }))
    } else {
      brands.value = [{ value: '', id: null }]
    }
  } catch (error) {
    toast.error(error.message || 'Failed to load brands')
    brands.value = [{ value: '', id: null }] // fallback in case of error
  } finally {
    loading.value = false
  } 
}

const addNewSize = () => {
  brands.value.push({ value: '', id: null })
}

const confirmDelete = async (id, index) => {
  const confirmed = await confirmDialog.value.show({
    title: 'Are you sure you want to delete?',
    message: 'This cannot be undone!'
  });
  if (confirmed && id) {
    await deleteSize(id, index)
  } else if(confirmed) {
    brands.value.splice(index, 1)
  }

}

const deleteSize = async (id, index) => {
  try {
    loading.value = true
    const response = await fetch(`${API.BACKEND_URL}/brands/${id}/delete`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`,
      }
    })

    if (!response.ok) {
      const errorData = await response.json()
      throw new Error(errorData.message || 'Failed to delete size')
    }

    toast.success('Record deleted successfully!')
    brands.value.splice(index, 1)
  } catch (error) {
    toast.error(error.message || 'Failed to delete record')
  } finally {
    loading.value = false
  }
}

const saveSizes = async () => {
  try {
    loading.value = true
    errors.value = {}

    const validSizes = brands.value.filter(size => size.value.trim())

    if (validSizes.length === 0) {
      errors.value = { 'brands.0': ['At least one brand is required'] }
      return
    }

    const response = await fetch(`${API.BACKEND_URL}/brands/save-all`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
      body: JSON.stringify({
        brands: validSizes.map(b => ({
          id: b.id || null,
          name: b.value.trim()
        }))
      })
    })

    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.message || 'Failed to save record')
    }

    toast.success(data.message)
    fetchBrands();
    router.push('/admin/brands')
  } catch (error) {
    toast.error(error.message || 'Failed to save record')
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

.size-inputs {
  margin-bottom: 20px;
}

.size-item {
  position: relative;
  display: flex;
  align-items: center;
  margin-bottom: 15px;
}

.size-item input {
  flex: 1;
  padding-right: 35px;
  /* Space for delete button */
}

.remove-size-btn {
  position: absolute;
  right: 10px;
  background: none;
  border: none;
  color: #ff4444;
  cursor: pointer;
  padding: 5px;
}

.add-more-button {
  display: flex;
  align-items: center;
  gap: 8px;
  background: none;
  border: 1px dashed #ddd;
  padding: 8px 16px;
  cursor: pointer;
  margin-bottom: 20px;
  color: #666;
}

.add-more-button:hover {
  border-color: #666;
}

.error-message {
  color: #ff4444;
  font-size: 12px;
  position: absolute;
  bottom: -18px;
  left: 0;
}
</style>