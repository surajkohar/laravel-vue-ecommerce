<template>
  <div class="view-product-container">
    <div class="header-section-view">
      <h1>Product Details</h1>
      <div class="action-buttons">
        <button class="back-button" @click="goBack">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
          </svg>
          Back
        </button>
        <button class="edit-button" @click="editProduct">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
          </svg>
          Edit
        </button>
      </div>
    </div>

    <div class="view-content">
      <div class="view-card">
        <!-- Product Images -->
        <div class="image-gallery">
          <div class="main-image">
            <img :src="product.images[0]?.preview || placeholderImage" alt="Main product image">
          </div>
          <div class="thumbnail-container" v-if="product.images.length > 1">
            <div class="thumbnail" v-for="(image, index) in product.images" :key="index" @click="setMainImage(index)">
              <img :src="image.preview" :alt="'Thumbnail ' + (index + 1)">
            </div>
          </div>
        </div>

        <!-- Product Details -->
        <div class="product-details">
          <div class="detail-section">
            <h2>{{ product.name }}</h2>
            <div class="status-badge" :class="product.status">{{ product.status }}</div>
            <p class="price">${{ product.price }}</p>
            <p class="description">{{ product.description }}</p>
          </div>

          <div class="detail-section">
            <h3>Product Information</h3>
            <div class="detail-row">
              <span class="detail-label">Category:</span>
              <span class="detail-value">{{ product.category }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">SKU:</span>
              <span class="detail-value">{{ product.sku }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Stock:</span>
              <span class="detail-value">{{ product.stock }} available</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Created:</span>
              <span class="detail-value">{{ product.createdAt }}</span>
            </div>
          </div>

          <div class="detail-section">
            <h3>Additional Details</h3>
            <div class="detail-row">
              <span class="detail-label">Weight:</span>
              <span class="detail-value">{{ product.weight || '0.5' }} kg</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Dimensions:</span>
              <span class="detail-value">{{ product.dimensions || '10 x 10 x 5 cm' }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const placeholderImage = 'https://via.placeholder.com/400x300?text=No+Image'

// Dummy product data
const product = ref({
  id: 'PRD-001',
  name: 'Premium Wireless Headphones',
  description: 'High-quality wireless headphones with noise cancellation and 30-hour battery life. Perfect for music lovers and professionals who need focus.',
  price: 199.99,
  category: 'Electronics',
  stock: 42,
  sku: 'AUD-HEADS-WL-001',
  status: 'active',
  createdAt: '2023-05-15',
  images: [
    { preview: 'https://via.placeholder.com/800x600?text=Headphones+Front' },
    { preview: 'https://via.placeholder.com/800x600?text=Headphones+Side' },
    { preview: 'https://via.placeholder.com/800x600?text=Headphones+Back' }
  ],
  weight: '0.45',
  dimensions: '18 x 15 x 7 cm'
})

const currentImageIndex = ref(0)

const setMainImage = (index) => {
  currentImageIndex.value = index
}

const goBack = () => {
  router.go(-1)
}

const editProduct = () => {
  router.push(`/admin/products/edit/${product.value.id}`)
}
</script>

<style scoped>
.view-product-container {
  padding: 5px;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.header-section-view {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
  margin-left: 10px;
  margin-right: 10px;
}

.header-section-view h1 {
  margin: 0;
  font-size: 1.5rem;
  color: #333;
  flex-grow: 1;
}

.action-buttons {
  display: flex;
  gap: 10px;
}

.back-button, .edit-button {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
}

.back-button {
  background-color: #f0f0f0;
  color: #555;
}

.back-button:hover {
  background-color: #e0e0e0;
}

.edit-button {
  background-color: #2196F3;
  color: white;
}

.edit-button:hover {
  background-color: #0b7dda;
}

.view-content {
  flex: 1;
}

.view-card {
  background-color: white;
  border-radius: 8px;
  padding: 25px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  margin: 0 10px;
  display: flex;
  flex-direction: column;
  gap: 30px;
}

.image-gallery {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.main-image {
  width: 100%;
  height: 400px;
  border-radius: 8px;
  overflow: hidden;
  background-color: #f8f9fa;
  display: flex;
  align-items: center;
  justify-content: center;
}

.main-image img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.thumbnail-container {
  display: flex;
  gap: 10px;
  overflow-x: auto;
  padding-bottom: 10px;
}

.thumbnail {
  width: 80px;
  height: 80px;
  border-radius: 4px;
  overflow: hidden;
  cursor: pointer;
  border: 2px solid #eee;
  transition: all 0.2s ease;
}

.thumbnail:hover {
  border-color: #4CAF50;
}

.thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-details {
  display: flex;
  flex-direction: column;
  gap: 30px;
}

.detail-section {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.detail-section h2 {
  margin: 0;
  font-size: 1.5rem;
  color: #333;
}

.detail-section h3 {
  margin: 0;
  font-size: 1.2rem;
  color: #333;
  padding-bottom: 8px;
  border-bottom: 1px solid #eee;
}

.status-badge {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 500;
  width: fit-content;
}

.status-badge.active {
  background-color: #d4edda;
  color: #155724;
}

.status-badge.inactive {
  background-color: #f8d7da;
  color: #721c24;
}

.price {
  font-size: 1.5rem;
  font-weight: 600;
  color: #4CAF50;
  margin: 10px 0;
}

.description {
  color: #555;
  line-height: 1.6;
}

.detail-row {
  display: flex;
  gap: 15px;
  padding: 8px 0;
  border-bottom: 1px solid #f5f5f5;
}

.detail-label {
  font-weight: 500;
  color: #555;
  min-width: 120px;
}

.detail-value {
  color: #333;
}

@media (max-width: 768px) {
  .view-card {
    padding: 20px 15px;
    margin: 0;
  }
  
  .main-image {
    height: 300px;
  }
  
  .detail-row {
    flex-direction: column;
    gap: 5px;
  }
  
  .detail-label {
    min-width: auto;
  }
}
</style>