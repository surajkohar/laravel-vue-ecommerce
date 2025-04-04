<template>
    <div class="auth-container">
      <h2>Create Your Account</h2>
      <form @submit.prevent="handleSignup">
        <input type="text" placeholder="Full Name" v-model="name" required />
        <input type="email" placeholder="Email" v-model="email" required />
        <input type="password" placeholder="Password" v-model="password" required />
        <button type="submit">Sign Up</button>
      </form>
      <p>Already have an account? <router-link to="/login">Login</router-link></p>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  
  const name = ref('');
  const email = ref('');
  const password = ref('');
  
  const handleSignup = () => {
    console.log("Signing up:", name.value, email.value);
  };
  </script>
  
  <style scoped>
  @import "@/assets/css/auth.css";
  </style>
  