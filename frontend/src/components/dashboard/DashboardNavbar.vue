<template>
  <header class="dashboard-navbar">
    <!-- Left Section: Hamburger & Logo -->
    <div class="navbar-left">
      <button class="hamburger" @click="$emit('toggleSidebar')">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="3" y1="12" x2="21" y2="12"></line>
          <line x1="3" y1="6" x2="21" y2="6"></line>
          <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
      </button>
      <div class="logo">
        <span>Admin Dashboard</span>
      </div>
    </div>

    <!-- Right Section: User Menu & Notifications -->
    <div class="navbar-right">
      <!-- Notifications -->
      <div class="notifications" @click="toggleNotifications">
        <div class="notification-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
          </svg>
          <span v-if="unreadCount > 0" class="notification-badge">{{ unreadCount }}</span>
        </div>
        
        <div v-if="showNotifications" class="notifications-dropdown">
          <div class="notifications-header">
            <h3>Notifications</h3>
            <span class="mark-read" @click="markAllAsRead">Mark all as read</span>
          </div>
          <div class="notifications-list">
            <div v-for="notification in notifications" :key="notification.id" 
                 class="notification-item" :class="{ unread: !notification.read }">
              <div class="notification-content">
                <p class="notification-text">{{ notification.message }}</p>
                <span class="notification-time">{{ notification.time }}</span>
              </div>
            </div>
            <div v-if="notifications.length === 0" class="empty-notifications">
              No notifications
            </div>
          </div>
        </div>
      </div>

      <!-- User Profile Dropdown -->
      <div class="user-menu" @click="toggleUserMenu">
        <div class="user-profile">
          <img :src="userProfile.avatar || '/default-avatar.png'" alt="Profile" class="profile-image">
          <div class="user-info">
            <span class="user-name">{{ userProfile.name }}</span>
            <span class="user-role">{{ userProfile.role }}</span>
          </div>
          <svg class="dropdown-arrow" :class="{ rotated: showUserMenu }" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </div>

        <div v-if="showUserMenu" class="user-dropdown">
          <div class="dropdown-item" @click="openProfileSettings">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="3"></circle>
              <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
            </svg>
            <span>Profile Settings</span>
          </div>
          
          <div class="dropdown-divider"></div>
          
          <div class="dropdown-item logout" @click="logout">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
              <polyline points="16 17 21 12 16 7"></polyline>
              <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
            <span>Logout</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Profile Settings Modal -->
    <ProfileSettingsModal 
      v-if="showProfileModal" 
      :user="userProfile"
      @close="showProfileModal = false" 
      @update="handleProfileUpdate"
    />
  </header>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/store/auth'
import { useRouter } from 'vue-router'
import ProfileSettingsModal from '@/components/ProfileSettingsModal.vue'

const authStore = useAuthStore()
const router = useRouter()

const showUserMenu = ref(false)
const showNotifications = ref(false)
const showProfileModal = ref(false)
const notifications = ref([
  { id: 1, message: 'New user registered', time: '2 min ago', read: false },
  { id: 2, message: 'Order #1234 has been placed', time: '5 min ago', read: true },
  { id: 3, message: 'System update completed', time: '1 hour ago', read: true }
])

const userProfile = computed(() => ({
  name: authStore.user?.name || 'Admin User',
  email: authStore.user?.email || 'admin@example.com',
  phone: authStore.user?.phone || '+1 (555) 123-4567',
  role: authStore.user?.role || 'Administrator',
  avatar: authStore.user?.profile_image || '/default-avatar.png'
}))

const unreadCount = computed(() => 
  notifications.value.filter(n => !n.read).length
)

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
  showNotifications.value = false
}

const toggleNotifications = () => {
  showNotifications.value = !showNotifications.value
  showUserMenu.value = false
}

const markAllAsRead = () => {
  notifications.value = notifications.value.map(n => ({ ...n, read: true }))
}

const openProfileSettings = () => {
  showProfileModal.value = true
  showUserMenu.value = false
}

const logout = () => {
  authStore.logout()
  router.push('/login')
}

const handleProfileUpdate = (updatedData) => {
  console.log('Profile updated:', updatedData)
  // Update user data in store/local storage
  showProfileModal.value = false
}

// Close dropdowns when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest('.user-menu')) {
    showUserMenu.value = false
  }
  if (!event.target.closest('.notifications')) {
    showNotifications.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.dashboard-navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: white;
  padding: 0 24px;
  height: 70px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  position: sticky;
  top: 0;
  z-index: 1000;
  border-bottom: 1px solid #e2e8f0;
}

.navbar-left {
  display: flex;
  align-items: center;
  gap: 16px;
}

.hamburger {
  display: none;
  background: transparent;
  border: none;
  color: #4a5568;
  cursor: pointer;
  padding: 8px;
  border-radius: 4px;
  transition: background-color 0.2s;
}

.hamburger:hover {
  background: #f7fafc;
}

.logo span {
  color: #2d3748;
  font-size: 18px;
  font-weight: 600;
}

.navbar-right {
  display: flex;
  align-items: center;
  gap: 20px;
}

.notifications {
  position: relative;
  cursor: pointer;
}

.notification-icon {
  position: relative;
  padding: 8px;
  border-radius: 8px;
  transition: background-color 0.2s;
  color: #4a5568;
}

.notification-icon:hover {
  background: #f7fafc;
}

.notification-badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background: #e53e3e;
  color: white;
  font-size: 11px;
  font-weight: 600;
  padding: 2px 6px;
  border-radius: 10px;
  min-width: 18px;
  text-align: center;
}

.notifications-dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  width: 320px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.15);
  margin-top: 10px;
  z-index: 1001;
  border: 1px solid #e2e8f0;
}

.notifications-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  border-bottom: 1px solid #e2e8f0;
}

.notifications-header h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: #2d3748;
}

.mark-read {
  color: #667eea;
  font-size: 12px;
  cursor: pointer;
  font-weight: 500;
}

.mark-read:hover {
  text-decoration: underline;
}

.notifications-list {
  max-height: 300px;
  overflow-y: auto;
}

.notification-item {
  padding: 12px 16px;
  border-bottom: 1px solid #f1f3f4;
  cursor: pointer;
  transition: background-color 0.2s;
}

.notification-item:hover {
  background: #f8fafc;
}

.notification-item.unread {
  background: #f0f9ff;
}

.notification-text {
  margin: 0 0 4px 0;
  font-size: 14px;
  color: #2d3748;
}

.notification-time {
  font-size: 12px;
  color: #718096;
}

.empty-notifications {
  padding: 20px;
  text-align: center;
  color: #a0aec0;
  font-size: 14px;
}

.user-menu {
  position: relative;
  cursor: pointer;
}

.user-profile {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 8px 12px;
  border-radius: 8px;
  transition: background-color 0.2s;
}

.user-profile:hover {
  background: #f7fafc;
}

.profile-image {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #e2e8f0;
}

.user-info {
  display: flex;
  flex-direction: column;
}

.user-name {
  color: #2d3748;
  font-weight: 600;
  font-size: 14px;
}

.user-role {
  color: #718096;
  font-size: 12px;
}

.dropdown-arrow {
  color: #718096;
  transition: transform 0.2s;
}

.dropdown-arrow.rotated {
  transform: rotate(180deg);
}

.user-dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  width: 200px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.15);
  margin-top: 10px;
  z-index: 1001;
  overflow: hidden;
  border: 1px solid #e2e8f0;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  cursor: pointer;
  transition: background-color 0.2s;
  color: #2d3748;
  font-size: 14px;
}

.dropdown-item:hover {
  background: #f7fafc;
}

.dropdown-item svg {
  color: #718096;
}

.dropdown-item.logout {
  color: #e53e3e;
}

.dropdown-item.logout svg {
  color: #e53e3e;
}

.dropdown-divider {
  height: 1px;
  background: #e2e8f0;
  margin: 4px 0;
}

@media (max-width: 768px) {
  .dashboard-navbar {
    padding: 0 16px;
  }
  
  .hamburger {
    display: block;
  }
  
  .logo span {
    font-size: 16px;
  }
  
  .user-info {
    display: none;
  }
  
  .notifications-dropdown {
    width: 280px;
    right: -50px;
  }
}
</style>