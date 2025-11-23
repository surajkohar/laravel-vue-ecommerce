<template>
  <FrontendLayout>
    <div class="unauthorized-page">
      <div class="container">
        <div class="error-content">
          <div class="error-icon">🚫</div>
          <h1>Access Denied</h1>
          <p>You don't have permission to access this page.</p>
          <div class="action-buttons">
            <router-link to="/" class="home-btn">Go Home</router-link>
            <router-link to="/login" class="login-btn">Sign In</router-link>
          </div>
        </div>
      </div>
    </div>
  </FrontendLayout>
</template>

<script setup>
import FrontendLayout from '@/layouts/FrontendLayout.vue'
</script>

<style scoped>
.unauthorized-page {
  min-height: 100vh;
  background: #f8f9fa;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 0;
}

.container {
  max-width: 500px;
  margin: 0 auto;
  padding: 0 20px;
}

.error-content {
  text-align: center;
  background: white;
  padding: 3rem;
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.error-icon {
  font-size: 4rem;
  margin-bottom: 1.5rem;
}

.error-content h1 {
  color: #2c3e50;
  margin-bottom: 1rem;
}

.error-content p {
  color: #6c757d;
  font-size: 1.1rem;
  margin-bottom: 2rem;
}

.action-buttons {
  display: flex;
  gap: 1rem;
  justify-content: center;
}

.home-btn, .login-btn {
  padding: 12px 24px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
}

.home-btn {
  background: #e74c3c;
  color: white;
}

.home-btn:hover {
  background: #c0392b;
}

.login-btn {
  background: #f8f9fa;
  color: #2c3e50;
  border: 2px solid #e9ecef;
}

.login-btn:hover {
  background: #e9ecef;
}

/* Responsive */
@media (max-width: 480px) {
  .action-buttons {
    flex-direction: column;
  }
  
  .home-btn, .login-btn {
    text-align: center;
  }
}
</style>