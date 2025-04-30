import { createRouter, createWebHistory } from "vue-router";
import frontendRoutes from "./frontendRoutes";
import dashboardRoutes from "./dashboardRoutes";
import { useAuthStore } from '@/store/auth'

const routes = [...frontendRoutes, ...dashboardRoutes];

const router = createRouter({
    history: createWebHistory(),
    routes,
});
// Update the router.beforeEach in router/index.js
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore()
    
    await authStore.hydrate()

    // Check guest-only routes (like login/signup)
    if (to.meta.guestOnly && authStore.isAuthenticated) {
      return next('/') // Redirect to home if logged-in user tries to access guest-only pages
    }
  
    // Check authenticated routes
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
      return next({
        name: 'Login',
        query: { redirect: to.fullPath }
      })
    }
  
    // Check admin permissions
    if (to.path.startsWith('/admin') && to.meta.permission) {
      if (!authStore.hasPermission(to.meta.permission)) {
        return next('/unauthorized')
      }
    }
  
    next()
  })
  

export default router;
