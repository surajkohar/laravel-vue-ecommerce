<template>
    <header class="dashboard-navbar">
      <button class="hamburger" @click="$emit('toggleSidebar')">☰</button>
      <div class="logo">Welcome, Admin</div>
      <button @click="logout">Logout</button>
    </header>
  </template>
  
  <script setup>
  import { useAuthStore } from '@/store/auth'
  import { useRouter } from 'vue-router'
  
  const authStore = useAuthStore()
  const router = useRouter()
  
  const logout = () => {
    authStore.logout()
    router.push('/login')
  }
  </script>
  
  <style scoped>
  .dashboard-navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fff;
    padding: 18px 24px;
    border-bottom: 1px solid #ddd;
  }
  
  .hamburger {
    display: none;
    background: transparent;
    font-size: 24px;
    cursor: pointer;
    border: none;
  }
  
  @media (max-width: 768px) {
    .hamburger {
      display: block;
    }
  }
  </style>
  