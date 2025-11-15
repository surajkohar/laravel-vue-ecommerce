<template>
  <div class="product-view-container">
    <!-- Header Section -->
    <div class="header-section-view">
      <h3>Product Details</h3>
      <div class="action-buttons">
        <button class="back-button" @click="goBack">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
          </svg>
          Back
        </button>
      </div>
    </div>

    <div class="view-container" v-if="!loading">
      <div class="row">
        <!-- Left Column (Details) -->
        <div class="col-md-8">
          <div class="card details-card">
            <div class="card-body">
              <h2 class="product-title">{{ product.name }}</h2>

              <div class="product-meta">
                <span class="badge status-badge" :class="product.status ? 'active' : 'inactive'">
                  {{ product.status ? 'Active' : 'Inactive' }}
                </span>
                <span class="sku">SKU: {{ product.sku }}</span>
              </div>

              <div class="product-price-section">
                <div class="price-row">
                  <span class="price-label">Selling Price:</span>
                  <span class="price-value">£{{ product.price }}</span>
                </div>
                <div class="price-row">
                  <span class="price-label">Purchase Price:</span>
                  <span class="price-value">£{{ product.purchase_price }}</span>
                </div>
                <div class="price-row">
                  <span class="price-label">Stock:</span>
                  <span class="stock-value">{{ product.stock }} units</span>
                </div>
              </div>

              <div class="product-categories">
                <div class="category-row">
                  <span class="label">Category:</span>
                  <span class="value">{{ product.category_title }}</span>
                </div>
                <div class="category-row" v-if="product.subcategories.length">
                  <span class="label">Subcategories:</span>
                  <span class="value">
                    <span v-for="subcat in product.subcategories" :key="subcat.id" class="subcategory-tag">
                      {{ subcat.title }}
                    </span>
                  </span>
                </div>
                <div class="category-row">
                  <span class="label">Brand:</span>
                  <span class="value">{{ product.brand_name || '-' }}</span>
                </div>
                <div class="category-row">
                  <span class="label">Gender:</span>
                  <span class="value">{{ formatGender(product.gender) }}</span>
                </div>
              </div>

              <div class="product-description">
                <h4>Description</h4>
                <div class="description-content" v-html="product.description"></div>
              </div>

              <!-- Variants Section -->
              <div class="variants-section" v-if="product.variants.length">
                <h4>Color Variants</h4>
                <div class="variants-container">
                  <div v-for="variant in product.variants" :key="variant.id" class="variant-item">
                    <div class="variant-header">
                      <div class="color-display" :style="{ backgroundColor: variant.color }"></div>
                      <span class="color-name">{{ variant.color_name }}</span>
                    </div>

                    <div class="variant-sizes">
                      <span class="sizes-label">Available Sizes:</span>
                      <div class="size-tags">
                        <span v-for="size in variant.sizes" :key="size.id" class="size-tag">
                          {{ size.size_title }} - £{{ size.price }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>

        <!-- Right Column (Media) -->
        <div class="col-md-4">
          <div class="card media-card">
            <div class="card-body">
              <!-- Main Image Gallery -->
              <div class="image-gallery" v-if="product.main_image_url">
                <h4>Main Image</h4>
                <div class="main-image-container">
                  <img :src="product.main_image_url" :alt="product.name" class="main-image" />
                </div>
              </div>

              <!-- Size Guide -->
              <div class="size-guide-section" v-if="product.size_guide_url">
                <h4>Size Guide</h4>
                <div class="size-guide-container">
                  <div class="pdf-preview" @click="openPdf(product.size_guide_url)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                      <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    <span>View Size Guide</span>
                  </div>
                </div>
              </div>

              <!-- Variant Images Gallery -->
              <div class="variant-gallery" v-if="product.variants.length">
                <h4>Variant Images</h4>
                <div v-for="variant in product.variants" :key="variant.id" class="variant-gallery-item">
                  <div class="variant-title">
                    <div class="color-indicator" :style="{ backgroundColor: variant.color }"></div>
                    {{ variant.color_name }}
                  </div>

                  <div class="variant-images" v-if="variant.images.length">
                    <div class="image-carousel">
                      <button class="nav-button prev" @click="prevImage(variant.id)"
                        :disabled="currentImageIndex[variant.id] === 0">
                        <i class="fas fa-chevron-left"></i>
                      </button>

                      <div class="image-container">
                        <img :src="variant.images[currentImageIndex[variant.id]]?.url"
                          :alt="`${variant.color_name} image`" />
                      </div>

                      <button class="nav-button next" @click="nextImage(variant.id)"
                        :disabled="currentImageIndex[variant.id] === variant.images.length - 1">
                        <i class="fas fa-chevron-right"></i>
                      </button>
                    </div>

                    <div class="image-counter">
                      {{ currentImageIndex[variant.id] + 1 }} of {{ variant.images.length }}
                    </div>
                  </div>
                  <div v-else class="no-images">
                    No images available for this variant
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="loading-container">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p>Loading product details...</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { API } from '@/utils/config';

const route = useRoute();
const router = useRouter();
const loading = ref(true);
const product = ref({
  id: null,
  name: '',
  description: '',
  status: 0,
  price: 0,
  purchase_price: 0,
  sku: '',
  stock: 0,
  gender: '',
  category_id: null,
  category_title: '',
  brand_name: '',
  main_image_url: null,
  size_guide_url: null,
  subcategories: [],
  variants: []
});
const currentImageIndex = ref({});

const fetchProduct = async () => {
  try {
    loading.value = true;
    const response = await fetch(`${API.BACKEND_URL}/product/${route.params.id}/view`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
      },
    });

    if (!response.ok) {
      throw new Error('Failed to fetch product data');
    }

    const data = await response.json();
    product.value = data.data.product;

    // Safely check if variants exist before accessing
    if (Array.isArray(data.data.variants)) {
      product.value.variants = data.data.variants;

      // Initialize image indexes for each variant
      data.data.variants.forEach((variant) => {
        currentImageIndex.value[variant.id] = 0;
      });
    } else {
      product.value.variants = [];
    }

    // Optional: subcategories
    if (Array.isArray(data.data.subcategories)) {
      product.value.subcategories = data.data.subcategories;
    } else {
      product.value.subcategories = [];
    }

  } catch (error) {
    console.error('Error fetching product:', error);
  } finally {
    loading.value = false;
  }
};


const formatGender = (gender) => {
  const genderMap = {
    'men': 'Men',
    'women': 'Women',
    'unisex': 'Unisex',
    'Male': 'Men',
    'Female': 'Women',
    'Unisex': 'Unisex'
  };
  return genderMap[gender] || gender;
};

const nextImage = (variantId) => {
  if (currentImageIndex.value[variantId] < product.value.variants.find(v => v.id === variantId).images.length - 1) {
    currentImageIndex.value[variantId]++;
  }
};

const prevImage = (variantId) => {
  if (currentImageIndex.value[variantId] > 0) {
    currentImageIndex.value[variantId]--;
  }
};

const openPdf = (url) => {
  window.open(url, '_blank');
};

const goBack = () => {
  router.go(-1);
};

onMounted(() => {
  fetchProduct();
});
</script>

<style scoped>
.product-view-container {
  padding: 20px;
  max-width: 1400px;
  margin: 0 auto;
}

.header-section-view {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.back-button {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  background-color: #f8f9fa;
  border: 1px solid #ddd;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.2s;
}

.back-button:hover {
  background-color: #e9ecef;
}

.view-container {
  /* background-color: #fff; */
  /* border-radius: 8px; */
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.card {
  border: none;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  margin-bottom: 20px;
}

.details-card {
  height: 100%;
}

.media-card {
  height: 100%;
}

.product-title {
  font-size: 24px;
  font-weight: 600;
  margin-bottom: 15px;
  color: #333;
}

.product-meta {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 20px;
}

.status-badge {
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
}

.status-badge.active {
  background-color: #e6f7ee;
  color: #10b759;
}

.status-badge.inactive {
  background-color: #fef0f0;
  color: #f1416c;
}

.sku {
  font-size: 14px;
  color: #6c757d;
}

.product-price-section {
  background-color: #f8f9fa;
  padding: 15px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.price-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 8px;
}

.price-row:last-child {
  margin-bottom: 0;
}

.price-label {
  font-weight: 500;
  color: #495057;
}

.price-value {
  font-weight: 600;
  color: #212529;
}

.stock-value {
  font-weight: 500;
  color: #212529;
}

.product-categories {
  margin-bottom: 25px;
}

.category-row {
  display: flex;
  margin-bottom: 10px;
}

.category-row:last-child {
  margin-bottom: 0;
}

.label {
  font-weight: 500;
  color: #495057;
  min-width: 120px;
}

.value {
  color: #212529;
}

.subcategory-tag {
  display: inline-block;
  background-color: #e9ecef;
  padding: 3px 8px;
  border-radius: 4px;
  margin-right: 5px;
  margin-bottom: 5px;
  font-size: 13px;
}

.product-description {
  margin-top: 30px;
}

.description-content {
  line-height: 1.6;
  color: #495057;
}

.description-content >>> img {
  max-width: 100%;
  height: auto;
}

.variants-section {
  margin-top: 30px;
}

.variants-container {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.variant-item {
  border: 1px solid #e9ecef;
  border-radius: 8px;
  padding: 15px;
}

.variant-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
}

.color-display {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  border: 1px solid #ddd;
}

.color-name {
  font-weight: 500;
}

.variant-sizes {
  display: flex;
  align-items: center;
  gap: 10px;
}

.sizes-label {
  font-size: 14px;
  color: #6c757d;
}

.size-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 5px;
}

.size-tag {
  background-color: #f8f9fa;
  padding: 3px 10px;
  border-radius: 12px;
  font-size: 13px;
  border: 1px solid #e9ecef;
}

/* Media Section Styles */
.image-gallery {
  margin-bottom: 25px;
}

.main-image-container {
  border: 1px solid #e9ecef;
  border-radius: 8px;
  padding: 10px;
  display: flex;
  justify-content: center;
  background-color: #f8f9fa;
}

.main-image {
  max-width: 100%;
  max-height: 300px;
  object-fit: contain;
  border-radius: 4px;
}

.size-guide-section {
  margin-bottom: 25px;
}

.size-guide-container {
  border: 1px solid #e9ecef;
  border-radius: 8px;
  padding: 15px;
  background-color: #f8f9fa;
}

.pdf-preview {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  transition: all 0.2s;
  padding: 10px;
  border-radius: 4px;
}

.pdf-preview:hover {
  background-color: #e9ecef;
}

.pdf-preview span {
  font-size: 14px;
  color: #495057;
}

.variant-gallery {
  margin-top: 25px;
}

.variant-gallery-item {
  margin-bottom: 20px;
}

.variant-gallery-item:last-child {
  margin-bottom: 0;
}

.variant-title {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 500;
  margin-bottom: 10px;
}

.color-indicator {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  border: 1px solid #ddd;
}

.variant-images {
  position: relative;
}

.image-carousel {
  display: flex;
  align-items: center;
  gap: 10px;
}

.image-container {
  flex-grow: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 200px;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  overflow: hidden;
  background-color: #f8f9fa;
}

.image-container img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.nav-button {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: 1px solid #ddd;
  background-color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
}

.nav-button:hover {
  background-color: #f8f9fa;
}

.nav-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.image-counter {
  text-align: center;
  font-size: 13px;
  color: #6c757d;
  margin-top: 8px;
}

.no-images {
  text-align: center;
  padding: 20px;
  background-color: #f8f9fa;
  border-radius: 8px;
  color: #6c757d;
  font-size: 14px;
}

.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 300px;
  gap: 15px;
}

.loading-container p {
  color: #6c757d;
}

.size-tag {
  background-color: #f8f9fa;
  padding: 3px 10px;
  border-radius: 12px;
  font-size: 13px;
  border: 1px solid #e9ecef;
  display: inline-flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
}

.size-tag::after {
  content: attr(data-price);
  font-size: 11px;
  color: #10b759;
  font-weight: 500;
}

@media (max-width: 768px) {
  .row {
    flex-direction: column;
  }
  
  .col-md-8, .col-md-4 {
    width: 100%;
  }
}
</style>