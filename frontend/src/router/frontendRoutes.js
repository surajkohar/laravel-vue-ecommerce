// router/frontendRoutes.js
import HomePage from "@/pages/frontend/HomePage.vue"

// router/frontendRoutes.js
const frontendRoutes = [
  { 
    path: "/", 
    name: "Home", 
    component: HomePage 
  },
  { 
    path: "/login", 
    name: "Login", 
    component: () => import('@/pages/frontend/LoginPage.vue'),
    // meta: { guestOnly: true }
  },
  { 
    path: "/signup", 
    name: "Signup", 
    component: () => import('@/pages/frontend/SignupPage.vue'),
    meta: { guestOnly: true }
  },
  { 
    path: "/mycart", 
    name: "Cart", 
    component: () => import('@/pages/frontend/CartPage.vue'),
    meta: { requiresAuth: true } 
  },
  { 
    path: "/unauthorized",
    name: "Unauthorized",
    component: () => import('@/pages/unauthorized.vue')
  }
]

export default frontendRoutes