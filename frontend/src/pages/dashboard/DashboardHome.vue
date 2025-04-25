<template>
    <div class="dashboard-home">
      <h1>Welcome to Admin Dashboard</h1>
  
      <div class="stats-grid">
        <div class="stat-card">
          <h3>Total Users</h3>
          <p>128</p>
        </div>
        <div class="stat-card">
          <h3>Total Orders</h3>
          <p>76</p>
        </div>
        <div class="stat-card">
          <h3>Total Revenue</h3>
          <p>$5,240</p>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  // Fetch actual stats from API in future
  </script>
  
  <style scoped>
  .dashboard-home {
    padding: 20px;
  }
  
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-top: 30px;
  }
  
  .stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  }
  
  .stat-card h3 {
    font-size: 16px;
    color: #666;
    margin-bottom: 10px;
  }
  
  .stat-card p {
    font-size: 28px;
    font-weight: bold;
    color: #1e1e2f;
  }
  </style>
  