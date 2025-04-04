<template>
  <nav class="navbar">
    <div class="container nav-content">
      <h1 class="logo">MyShop</h1>

      <ul class="nav-links">
        <li><router-link to="/">Home</router-link></li>
        <li><router-link to="/products">Products</router-link></li>
        <li><router-link to="/cart">Cart</router-link></li>
      </ul>

      <div class="auth-buttons">
        <router-link v-if="!isLoggedIn" to="/login" class="btn">Login</router-link>
        <template v-else>
          <router-link to="/profile" class="btn">Profile</router-link>
          <button class="btn" @click="logout">Logout</button>
        </template>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref } from "vue";

const isLoggedIn = ref(false); // replace with Pinia/Auth later

const login = () => {
  isLoggedIn.value = true;
};

const logout = () => {
  isLoggedIn.value = false;
};
</script>

<style scoped>
@import "@/assets/css/navbar.css";
</style>
