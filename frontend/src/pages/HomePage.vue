<script setup>
import { onMounted, ref } from "vue";
import MainLayout from "@/layouts/MainLayout.vue";

const products = ref([]);

onMounted(() => {
    // Fake product list
    products.value = [
        { id: 1, name: "Smartphone X", price: "$699" },
        { id: 2, name: "Laptop Pro", price: "$1299" },
        { id: 3, name: "Wireless Headphones", price: "$199" },
        { id: 4, name: "Smartwatch Z", price: "$299" },
    ];
});
</script>

<template>
    <MainLayout>
        <h1>Home Page</h1>
        <div v-for="product in products" :key="product.id">
            <h2>{{ product.name }}</h2>
            <p>{{ product.price }}</p>
        </div>
    </MainLayout>
</template>
