<script setup>
import FrontendLayout from "@/layouts/FrontendLayout.vue"; // ✅ ADD THIS
const products = [
  { id: 1, name: "Product A", price: "$100" },
  { id: 2, name: "Product B", price: "$200" },
  { id: 3, name: "Product C", price: "$300" },
];
</script>

<template>
  <FrontendLayout>
    <div class="home-page">
      <h2>Featured Products</h2>
      <div class="product-list">
        <div v-for="product in products" :key="product.id" class="product-card">
          <h3>{{ product.name }}</h3>
          <p>{{ product.price }}</p>
        </div>
      </div>
    </div>
  </FrontendLayout>
</template>

<style scoped src="@/assets/css/home.css"></style>
