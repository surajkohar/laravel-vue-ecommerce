<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="quick-view-modal">
      <button class="close-btn" @click="$emit('close')">✕</button>
      
      <div class="modal-content" v-if="product">
        <div class="product-images">
          <img :src="product.main_image_url" :alt="product.name" />
        </div>
        
        <div class="product-details">
          <h2>{{ product.name }}</h2>
          <div class="price">${{ product.price }}</div>
          <p class="description">{{ product.description }}</p>
          
          <div class="quick-actions">
            <button class="view-details-btn" @click="viewProductDetails">
              View Full Details
            </button>
            <button class="add-to-cart-btn" @click="addToCart">
              Add to Cart
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useCartStore } from '@/store/cart'

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['close'])
const router = useRouter()
const cartStore = useCartStore()

const viewProductDetails = () => {
  emit('close')
  router.push(`/products/${props.product.id}`)
}

const addToCart = () => {
  cartStore.addToCart(props.product)
  emit('close')
  // Show success toast
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.quick-view-modal {
  background: white;
  border-radius: 16px;
  max-width: 800px;
  width: 100%;
  max-height: 90vh;
  overflow: hidden;
  position: relative;
}

.close-btn {
  position: absolute;
  top: 15px;
  right: 15px;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  cursor: pointer;
  z-index: 10;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
}

.modal-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  height: 100%;
}

.product-images {
  background: #f8f9fa;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.product-images img {
  width: 100%;
  max-height: 400px;
  object-fit: cover;
  border-radius: 8px;
}

.product-details {
  padding: 2rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.product-details h2 {
  margin: 0 0 1rem 0;
  color: #2c3e50;
  font-size: 1.5rem;
}

.price {
  font-size: 1.8rem;
  font-weight: bold;
  color: #e74c3c;
  margin-bottom: 1rem;
}

.description {
  color: #5d6d7e;
  line-height: 1.6;
  margin-bottom: 2rem;
}

.quick-actions {
  display: flex;
  gap: 1rem;
}

.view-details-btn, .add-to-cart-btn {
  flex: 1;
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.view-details-btn {
  background: #f8f9fa;
  color: #2c3e50;
  border: 2px solid #e9ecef;
}

.view-details-btn:hover {
  background: #e9ecef;
}

.add-to-cart-btn {
  background: #e74c3c;
  color: white;
}

.add-to-cart-btn:hover {
  background: #c0392b;
  transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
  .modal-content {
    grid-template-columns: 1fr;
    max-height: 80vh;
    overflow-y: auto;
  }
  
  .product-images {
    padding: 1rem;
  }
  
  .product-details {
    padding: 1.5rem;
  }
  
  .quick-actions {
    flex-direction: column;
  }
}
</style>