<template>
    <div class="add-product-container">
        <div class="header-section-add">
            <h1>Edit Product</h1>
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
            <h2>Basic Information</h2>
            <div class="form-group full-width-field">
              <label>Product Name</label>
              <input type="text" v-model="product.name" placeholder="Enter product name">
            </div>
            <div class="form-group full-width-field">
              <label>Description</label>
              <textarea v-model="product.description" placeholder="Enter product description"></textarea>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Price ($)</label>
                <input type="number" v-model="product.price" placeholder="0.00">
              </div>
              <div class="form-group">
                <label>Category</label>
                <select v-model="product.category">
                  <option value="">Select category</option>
                  <option v-for="category in categories" :value="category" :key="category">{{ category }}</option>
                </select>
              </div>
            </div>
          </div>
  
          <div class="form-section">
            <h2>Inventory</h2>
            <div class="form-row">
              <div class="form-group">
                <label>Stock Quantity</label>
                <input type="number" v-model="product.stock" placeholder="0">
              </div>
              <div class="form-group">
                <label>SKU</label>
                <input type="text" v-model="product.sku" placeholder="Enter SKU">
              </div>
            </div>
            <div class="form-group full-width-field">
              <label>Status</label>
              <div class="status-options">
                <label>
                  <input type="radio" v-model="product.status" value="active">
                  <span class="radio-label active">Active</span>
                </label>
                <label>
                  <input type="radio" v-model="product.status" value="inactive">
                  <span class="radio-label inactive">Inactive</span>
                </label>
              </div>
            </div>
          </div>
  
          <div class="form-section">
            <h2>Product Images</h2>
            <div class="image-uploader">
              <div class="upload-area" @click="triggerFileInput">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7"></path>
                  <polyline points="16 5 22 5 22 11"></polyline>
                  <line x1="16" y1="5" x2="22" y2="11"></line>
                </svg>
                <p>Click to upload images</p>
                <input type="file" ref="fileInput" @change="handleFileUpload" multiple style="display: none;">
              </div>
              <div class="image-preview" v-if="product.images.length > 0">
                <div class="preview-item" v-for="(image, index) in product.images" :key="index">
                  <img :src="image.preview" alt="Product image">
                  <button class="remove-image" @click="removeImage(index)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <line x1="18" y1="6" x2="6" y2="18"></line>
                      <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
  
          <div class="form-actions">
            <button class="cancel-button" @click="goBack">
              Cancel
            </button>
            <button class="save-button" @click="saveProduct">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
            <polyline points="17 21 17 13 7 13 7 21"></polyline>
            <polyline points="7 3 7 8 15 8"></polyline>
          </svg>
              Save Product
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  import { useRouter } from 'vue-router'
  
  const router = useRouter()
  const fileInput = ref(null)
  
  const categories = ['Electronics', 'Clothing', 'Home', 'Office', 'Books']
  
  const product = ref({
    name: '',
    description: '',
    price: '',
    category: '',
    stock: '',
    sku: '',
    status: 'active',
    images: []
  })
  
  const triggerFileInput = () => {
    fileInput.value.click()
  }
  
  const handleFileUpload = (e) => {
    const files = e.target.files
    if (files) {
      Array.from(files).forEach(file => {
        const reader = new FileReader()
        reader.onload = (event) => {
          product.value.images.push({
            file,
            preview: event.target.result
          })
        }
        reader.readAsDataURL(file)
      })
    }
  }
  
  const removeImage = (index) => {
    product.value.images.splice(index, 1)
  }
  
  const saveProduct = () => {
    console.log('Product saved:', product.value)
    router.push('/admin/products')
  }
  
  const goBack = () => {
    router.go(-1)
  }
  </script>
  
  <style scoped>
  @import "@/assets/css/custom.css";
</style>

  