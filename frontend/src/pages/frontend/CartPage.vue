<!-- src/pages/Unauthorized.vue -->
<template>
    <div class="unauthorized-container">
      <h1>403 - Unauthorized</h1>
      <p>You don't have permission to access this page.</p>
      <router-link to="/admin">Go back to dashboard</router-link>
    </div>
  </template>
  
  <style scoped>
  .unauthorized-container {
    text-align: center;
    padding: 2rem;
    margin-top: 5rem;
  }
  </style>