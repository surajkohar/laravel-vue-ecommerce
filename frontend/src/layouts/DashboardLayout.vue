<template>
  <div class="app-container">
    <Sidebar ref="sidebarRef" />
    <div class="main-content">
      <DashboardNavbar @toggleSidebar="toggleSidebar" />
      <div class="content-scroll">
        <router-view />
      </div>
    </div>
  </div>
</template>

<script setup>
import Sidebar from '@/components/dashboard/Sidebar.vue'
import DashboardNavbar from '@/components/dashboard/DashboardNavbar.vue'
import { ref } from 'vue'

const sidebarRef = ref()

const toggleSidebar = () => {
  sidebarRef.value?.toggleSidebar()
}
</script>

<style scoped>
.app-container {
  display: flex;
  height: 100vh;
  overflow: hidden; /* Prevent double scrollbars */
}

.main-content {
  flex: 1;
  background-color: #f8f9fa;
  display: flex;
  flex-direction: column;
  width: calc(100% - 250px); /* Calculate remaining width */
}

.content-scroll {
  flex: 1;
  overflow-y: auto; /* Enable scrolling for content */
  padding: 20px;
}

/* Mobile styles */
@media (max-width: 768px) {
  .main-content {
    margin-left: 0;
    width: 100%;
  }
}
</style>