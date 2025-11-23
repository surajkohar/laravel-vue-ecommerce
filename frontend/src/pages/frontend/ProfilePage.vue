<template>
  <FrontendLayout>
    <div class="profile-page">
      <div class="container">
        <div class="profile-header">
          <h1>My Profile</h1>
          <p>Manage your account information and preferences</p>
        </div>

        <div class="profile-content">
          <div class="profile-card">
            <h2>Personal Information</h2>
            <div class="user-info">
              <div class="info-item">
                <label>Full Name</label>
                <p>{{ user?.name || 'Not set' }}</p>
              </div>
              <div class="info-item">
                <label>Email Address</label>
                <p>{{ user?.email || 'Not set' }}</p>
              </div>
              <div class="info-item">
                <label>Member Since</label>
                <p>January 2024</p>
              </div>
            </div>
          </div>

          <div class="profile-actions">
            <button class="action-btn">Edit Profile</button>
            <button class="action-btn">Order History</button>
            <button class="action-btn logout-btn" @click="handleLogout">Logout</button>
          </div>
        </div>
      </div>
    </div>
  </FrontendLayout>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/store/auth'
import FrontendLayout from '@/layouts/FrontendLayout.vue'

const router = useRouter()
const authStore = useAuthStore()

const user = authStore.user

const handleLogout = () => {
  authStore.logout()
  router.push('/')
}
</script>

<style scoped>
.profile-page {
  min-height: 100vh;
  background: #f8f9fa;
  padding: 2rem 0;
}

.container {
  max-width: 800px;
  margin: 0 auto;
  padding: 0 20px;
}

.profile-header {
  text-align: center;
  margin-bottom: 3rem;
}

.profile-header h1 {
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.profile-header p {
  color: #6c757d;
  font-size: 1.1rem;
}

.profile-content {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.profile-card {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.profile-card h2 {
  color: #2c3e50;
  margin-bottom: 1.5rem;
  font-size: 1.5rem;
}

.user-info {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.info-item label {
  display: block;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.info-item p {
  color: #5d6d7e;
  margin: 0;
  font-size: 1.1rem;
}

.profile-actions {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.action-btn {
  padding: 12px 24px;
  background: white;
  color: #2c3e50;
  border: 2px solid #e9ecef;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  flex: 1;
  min-width: 150px;
}

.action-btn:hover {
  background: #f8f9fa;
  border-color: #667eea;
}

.logout-btn {
  background: #e74c3c;
  color: white;
  border-color: #e74c3c;
}

.logout-btn:hover {
  background: #c0392b;
  border-color: #c0392b;
}

/* Responsive */
@media (max-width: 768px) {
  .profile-actions {
    flex-direction: column;
  }
  
  .action-btn {
    min-width: auto;
  }
}
</style>