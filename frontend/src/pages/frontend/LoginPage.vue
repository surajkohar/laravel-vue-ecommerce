<template>
    <div class="auth-container">
      <h2>Login to MyShop</h2>
      <form @submit.prevent="handleLogin">
        <input type="email" placeholder="Email" v-model="email" required />
        <input type="password" placeholder="Password" v-model="password" required />
        <button type="submit">Login</button>
      </form>
      <p>Don't have an account? <router-link to="/signup">Sign up</router-link></p>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  
  const email = ref('');
  const password = ref('');
  
  const handleLogin = () => {
    console.log("Logging in:", email.value, password.value);
    // You can later replace with real API call
  };
  </script>
  
  <style scoped>
  @import "@/assets/css/auth.css";
  </style>
  