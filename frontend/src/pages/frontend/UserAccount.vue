<template>
  <FrontendLayout>
    <div class="account-page">
      <div class="container">
        <div class="account-layout">
          <!-- Sidebar -->
          <div class="account-sidebar">
            <div class="user-profile">
              <div class="user-avatar">
                <img :src="user.avatar || '/images/default-avatar.png'" :alt="user.name" />
              </div>
              <div class="user-info">
                <h3>{{ user.name }}</h3>
                <p>{{ user.email }}</p>
              </div>
            </div>
            
            <nav class="account-nav">
              <router-link 
                v-for="item in navItems" 
                :key="item.id"
                :to="item.path" 
                class="nav-item"
                :class="{ active: $route.path === item.path }"
              >
                <component :is="item.icon" class="nav-icon" />
                <span class="nav-label">{{ item.label }}</span>
                <span class="nav-badge" v-if="item.badge">{{ item.badge }}</span>
              </router-link>
            </nav>
          </div>

          <!-- Main Content -->
          <div class="account-main">
            <router-view />
          </div>
        </div>
      </div>
    </div>
  </FrontendLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/store/auth'
import FrontendLayout from '@/layouts/FrontendLayout.vue'

// Icons
const ProfileIcon = {
  template: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none">
    <path d="M12 12C14.2091 12 16 10.2091 16 8C16 5.79086 14.2091 4 12 4C9.79086 4 8 5.79086 8 8C8 10.2091 9.79086 12 12 12Z" stroke="currentColor" stroke-width="2"/>
    <path d="M20 21V19C20 16.7909 18.2091 15 16 15H8C5.79086 15 4 16.7909 4 19V21" stroke="currentColor" stroke-width="2"/>
  </svg>`
}

const OrdersIcon = {
  template: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none">
    <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2"/>
    <path d="M9 22V12H15V22" stroke="currentColor" stroke-width="2"/>
  </svg>`
}

const WishlistIcon = {
  template: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none">
    <path d="M19.5 12.572L12 20L4.5 12.572C3.605 11.677 3 10.391 3 9C3 6.791 4.791 5 7 5C8.309 5 9.5 5.671 10.214 6.714L12 9L13.786 6.714C14.5 5.671 15.691 5 17 5C19.209 5 21 6.791 21 9C21 10.391 20.395 11.677 19.5 12.572Z" stroke="currentColor" stroke-width="2"/>
  </svg>`
}

const AddressIcon = {
  template: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none">
    <path d="M21 10C21 17 12 23 12 23C12 23 3 17 3 10C3 7.61305 3.94821 5.32387 5.63604 3.63604C7.32387 1.94821 9.61305 1 12 1C14.3869 1 16.6761 1.94821 18.364 3.63604C20.0518 5.32387 21 7.61305 21 10Z" stroke="currentColor" stroke-width="2"/>
    <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2"/>
  </svg>`
}

const authStore = useAuthStore()

const user = ref({
  name: 'John Doe',
  email: 'john.doe@example.com',
  avatar: '/images/avatars/user-1.jpg',
  memberSince: '2023'
})

const navItems = ref([
  { id: 'profile', label: 'Profile', path: '/account', icon: ProfileIcon },
  { id: 'orders', label: 'My Orders', path: '/account/orders', icon: OrdersIcon, badge: 3 },
  { id: 'wishlist', label: 'Wishlist', path: '/account/wishlist', icon: WishlistIcon, badge: 12 },
  { id: 'addresses', label: 'Addresses', path: '/account/addresses', icon: AddressIcon }
])
</script>

<style scoped src="@/assets/styles/frontend.css"></style>

<style scoped>
.account-page {
  min-height: 100vh;
  background: linear-gradient(135deg, var(--gray-light) 0%, #f1f5f9 100%);
  padding: 2rem 0 4rem;
}

.account-layout {
  display: grid;
  grid-template-columns: 300px 1fr;
  gap: 2rem;
  align-items: start;
}

/* Sidebar */
.account-sidebar {
  background: var(--primary-white);
  border-radius: 16px;
  box-shadow: var(--shadow-medium);
  overflow: hidden;
  position: sticky;
  top: 120px;
}

.user-profile {
  padding: 2rem;
  text-align: center;
  border-bottom: 1px solid var(--border-color);
  background: linear-gradient(135deg, var(--primary-red) 0%, #ff6b6b 100%);
  color: var(--primary-white);
}

.user-avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  overflow: hidden;
  margin: 0 auto 1rem;
  border: 4px solid rgba(255, 255, 255, 0.3);
}

.user-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.user-info h3 {
  margin: 0 0 0.25rem 0;
  font-size: 1.2rem;
}

.user-info p {
  margin: 0;
  opacity: 0.9;
  font-size: 0.9rem;
}

.account-nav {
  display: flex;
  flex-direction: column;
  padding: 1rem 0;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 2rem;
  color: var(--gray-dark);
  text-decoration: none;
  transition: all 0.3s ease;
  position: relative;
  border-left: 3px solid transparent;
}

.nav-item:hover {
  background: #f8fafc;
  color: var(--primary-red);
}

.nav-item.active {
  background: #fef2f2;
  color: var(--primary-red);
  border-left-color: var(--primary-red);
}

.nav-icon {
  flex-shrink: 0;
}

.nav-label {
  flex: 1;
  font-weight: 500;
}

.nav-badge {
  background: var(--primary-red);
  color: var(--primary-white);
  border-radius: 12px;
  padding: 2px 8px;
  font-size: 0.75rem;
  font-weight: 600;
  min-width: 20px;
  text-align: center;
}

/* Main Content */
.account-main {
  background: var(--primary-white);
  border-radius: 16px;
  box-shadow: var(--shadow-medium);
  overflow: hidden;
  min-height: 600px;
}

/* Responsive */
@media (max-width: 768px) {
  .account-layout {
    grid-template-columns: 1fr;
  }
  
  .account-sidebar {
    position: static;
  }
  
  .account-nav {
    flex-direction: row;
    overflow-x: auto;
    padding: 1rem;
  }
  
  .nav-item {
    flex-direction: column;
    text-align: center;
    padding: 1rem;
    min-width: 80px;
    border-left: none;
    border-bottom: 3px solid transparent;
  }
  
  .nav-item.active {
    border-left: none;
    border-bottom-color: var(--primary-red);
  }
  
  .nav-label {
    font-size: 0.8rem;
  }
  
  .nav-badge {
    position: absolute;
    top: 8px;
    right: 8px;
  }
}
</style>