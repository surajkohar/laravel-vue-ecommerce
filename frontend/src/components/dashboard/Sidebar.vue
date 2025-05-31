<template>
  <div class="sidebar" :class="{ open: isOpen }">
    <div class="sidebar-header">
      <h2>MyShop Admin</h2>
      <button class="close-btn" @click="toggleSidebar">×</button>
    </div>
    <!-- <div class="search-box">
        <input type="text" placeholder="Search...">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
      </div> -->
    <nav>
      <RouterLink v-if="hasPermission('view dashboard')" to="/admin" @click="handleLinkClick"
        :class="{ active: isActive('/admin') }">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
          <polyline points="9 22 9 12 15 12 15 22"></polyline>
        </svg>
        <span>Dashboard</span>
      </RouterLink>

      <div class="dropdown-wrapper">
        <div class="dropdown-header" @click="toggleProductsDropdown" :class="{ active: isProductsDropdownActive }">
          <div class="dropdown-title">
            <svg class="dropdown-icon" viewBox="0 0 24 24" stroke="white" fill="none" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
              <path d="M3 9h18M9 21V9"></path>
            </svg>
            <span>Products Management</span>
          </div>

          <svg class="dropdown-arrow" viewBox="0 0 24 24" stroke="white" fill="none" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" :class="{ rotated: showProductsDropdown }">
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </div>

        <div class="dropdown-content" :class="{ show: showProductsDropdown }">
          <RouterLink to="/admin/products" class="dropdown-item" @click="handleLinkClick"
            :class="{ active: isActive('/admin/products') || isActive('/admin/product/add') || isActive('/admin/product/' + $route.params.id + '/view') ||
               isActive('/admin/product/' + $route.params.id + '/edit') }">
            <svg class="item-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="white"
              stroke-width="2">
              <circle cx="12" cy="12" r="6" />
            </svg>

            <span>Products</span>
          </RouterLink>

          <RouterLink to="/admin/product-category" class="dropdown-item" @click="handleLinkClick"
            :class="{ active: isActive('/admin/product-category') || isActive('/admin/product-category/add') || isActive('/admin/product-category/' + $route.params.id + '/edit') }">
            <svg class="item-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="white"
              stroke-width="2">
              <circle cx="12" cy="12" r="6" />
            </svg>

            <span>Categories</span>
          </RouterLink>

          <RouterLink to="/admin/product-subCategory" class="dropdown-item" @click="handleLinkClick"
            :class="{ active: isActive('/admin/product-subCategory') || isActive('/admin/product-subCategory/add') || isActive('/admin/product-subCategory/' + $route.params.id + '/edit') }">
            <svg class="item-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="white"
              stroke-width="2">
              <circle cx="12" cy="12" r="6" />
            </svg>

            <span>Sub ategories</span>
          </RouterLink>
          <RouterLink to="/admin/sizes" class="dropdown-item" @click="handleLinkClick" :class="{
            active: isActive('/admin/sizes') || isActive('/admin/sizes/add') || isActive('/admin/sizes/' + $route.params.id + '/edit')
          }">
            <svg class="item-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="white"
              stroke-width="2">
              <circle cx="12" cy="12" r="6" />
            </svg>

            <span>Sizes</span>
          </RouterLink>
        </div>
      </div>

      <RouterLink v-if="hasPermission('view users')" to="/admin/users" @click="handleLinkClick" :class="{
        active:
          isActive('/admin/users') ||
          isActive('/admin/user/add') ||
          isActive('/admin/user/' + $route.params.id + '/edit'),
      }">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
          <circle cx="9" cy="7" r="4"></circle>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
          <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
        </svg>
        <span>Users</span>
      </RouterLink>

      <RouterLink v-if="hasPermission('view roles')" to="/admin/roles" @click="handleLinkClick" :class="{
        active:
          isActive('/admin/roles') ||
          isActive('/admin/role/add') ||
          isActive('/admin/role/' + $route.params.id + '/edit'),
      }">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
          <path d="M9 12l2 2 4-4"></path>
        </svg>
        <span>Roles</span>
      </RouterLink>

      <RouterLink to="/admin/permissions" @click="handleLinkClick" :class="{ active: isActive('/admin/permissions') }">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path
            d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4">
          </path>
        </svg>
        <span>Permissions</span>
      </RouterLink>

      <RouterLink to="#" @click="handleLinkClick">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
        <span>Messages</span>
      </RouterLink>

      <RouterLink to="#" @click="handleLinkClick">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="20" x2="12" y2="10"></line>
          <line x1="18" y1="20" x2="18" y2="4"></line>
          <line x1="6" y1="20" x2="6" y2="16"></line>
        </svg>
        <span>Analytics</span>
      </RouterLink>

      <RouterLink to="#" @click="handleLinkClick">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7"></path>
          <polyline points="16 5 22 5 22 11"></polyline>
          <line x1="16" y1="5" x2="22" y2="11"></line>
        </svg>
        <span>File Manager</span>
      </RouterLink>

      <RouterLink to="/admin/orders" @click="handleLinkClick" :class="{ active: isActive('/admin/orders') }">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="9" cy="21" r="1"></circle>
          <circle cx="20" cy="21" r="1"></circle>
          <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
        <span>Order</span>
      </RouterLink>

      <RouterLink to="#" @click="handleLinkClick">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
        </svg>
        <span>Saved</span>
      </RouterLink>
    </nav>
  </div>
</template>

<script setup>
import { ref, defineExpose, onMounted, computed } from "vue";
import { useRoute } from "vue-router";
import { useAuthStore } from "@/store/auth";

const isOpen = ref(false);
const route = useRoute();
const showProductsDropdown = ref(false);
const authStore = useAuthStore();

const toggleSidebar = () => {
  isOpen.value = !isOpen.value;
};

const handleLinkClick = () => {
  if (window.innerWidth < 768) {
    isOpen.value = false;
  }
};

const isActive = (path) => {
  return route.path === path;
};

defineExpose({ toggleSidebar });

const hasPermission = (permission) => {
  return authStore.hasPermission(permission);
};

const toggleProductsDropdown = () => {
  showProductsDropdown.value = !showProductsDropdown.value;
};

const isProductsDropdownActive = computed(() => {
  return isActive("/admin/products") || isActive("/admin/product/add") || isActive(`/admin/product/${route.params.id}/edit`) || isActive("/admin/product-category") ||
  isActive("/admin/product-category/add") || isActive(`/admin/product-category/${route.params.id}/edit`) ||
   isActive("/admin/product-subCategory") || isActive("/admin/product-subCategory/add") || isActive(`/admin/product-subCategory/${route.params.id}/edit`)

});

onMounted(() => {
  authStore.hydrate();
});
</script>

<style scoped>
.sidebar {
  width: 250px;
  background-color: #1e1e2f;
  color: #fff;
  padding: 20px 0;
  display: flex;
  flex-direction: column;
  height: 100vh;
  transition: transform 0.3s ease;
  box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
  overflow-y: auto;
}

/* Header */
.sidebar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 20px 20px;
  border-bottom: 1px solid #2f2f48;
}

.sidebar-header h2 {
  font-size: 18px;
  margin: 0;
  color: #fff;
}

.close-btn {
  background: none;
  border: none;
  color: #fff;
  font-size: 24px;
  cursor: pointer;
  display: none;
}

/* Search Box */
.search-box {
  position: relative;
  padding: 15px 20px;
  border-bottom: 1px solid #2f2f48;
}

.search-box input {
  width: 100%;
  padding: 8px 30px 8px 10px;
  background-color: #2f2f48;
  border: none;
  border-radius: 4px;
  color: #fff;
}

.search-box svg {
  position: absolute;
  right: 30px;
  top: 50%;
  transform: translateY(-50%);
  color: #ccc;
}

/* Nav Links */
.sidebar nav {
  padding: 15px 0;
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.sidebar nav a {
  color: #ccc;
  text-decoration: none;
  padding: 12px 20px 12px 25px;
  border-radius: 0;
  font-weight: 500;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  gap: 12px;
}

/* Add highlight strip on the left */
.sidebar nav a::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 3px;
  height: 100%;
  background-color: transparent;
  transition: background-color 0.3s ease;
}

/* Active effect */
.sidebar nav a.active {
  background-color: rgba(255, 255, 255, 0.05);
  color: #fff;
}

.sidebar nav a.active::before {
  background-color: white;
}

.sidebar nav a:hover {
  background-color: rgba(255, 255, 255, 0.05);
  color: #fff;
}

.sidebar nav a svg {
  flex-shrink: 0;
}

/* Mobile Styles */
@media (max-width: 768px) {
  .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
    transform: translateX(-100%);
    overflow-y: auto;
  }

  .sidebar.open {
    transform: translateX(0);
  }

  .close-btn {
    display: block;
  }
}

/* Dropdown Styles */
.dropdown-wrapper {
  width: 100%;
  margin-bottom: 5px;
}

.dropdown-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 20px;
  color: #ccc;
  cursor: pointer;
  transition: all 0.3s ease;
  border-left: 3px solid transparent;
}

.dropdown-header:hover {
  background-color: rgba(255, 255, 255, 0.05);
  color: #fff;
}

.dropdown-header.active {
  background-color: rgba(255, 255, 255, 0.05);
  color: #fff;
  border-left-color: white;
}

.dropdown-title {
  display: flex;
  align-items: center;
  gap: 12px;
}

.dropdown-icon {
  width: 16px;
  height: 16px;
}

.dropdown-arrow {
  width: 14px;
  height: 14px;
  transition: transform 0.3s ease;
}

.dropdown-arrow.rotated {
  transform: rotate(90deg);
  /* Right arrow becomes down arrow */
}



.dropdown-content {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.3s ease;
  background-color: rgba(0, 0, 0, 0.2);
}

.dropdown-content.show {
  max-height: 500px;
}

.dropdown-item {
  display: flex;
  align-items: center;
  padding: 10px 20px 10px 50px;
  color: #ccc;
  text-decoration: none;
  transition: all 0.3s ease;
  gap: 10px;
}

.dropdown-item:hover {
  background-color: rgba(255, 255, 255, 0.03);
  color: #fff;
}

.dropdown-item.active {
  background-color: rgba(255, 255, 255, 0.05);
  color: #fff;
}

.item-icon {
  width: 14px;
  height: 14px;
}
</style>