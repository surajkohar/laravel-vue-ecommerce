const frontendRoutes = [
  { 
    path: "/", 
    name: "Home", 
    component: () => import('@/pages/frontend/HomePage.vue')
  },
  { 
    path: "/products", 
    name: "Products", 
    component: () => import('@/pages/frontend/ProductsPage.vue')
  },
  { 
    path: "/products/:id", 
    name: "ProductDetail", 
    component: () => import('@/pages/frontend/ProductDetailPage.vue')
  },
  { 
    path: "/login", 
    name: "Login", 
    component: () => import('@/pages/frontend/LoginPage.vue'),
    meta: { guestOnly: true }
  },
  { 
    path: "/signup", 
    name: "Signup", 
    component: () => import('@/pages/frontend/SignupPage.vue'),
    meta: { guestOnly: true }
  },
  { 
    path: "/cart", 
    name: "Cart", 
    component: () => import('@/pages/frontend/CartPage.vue'),
    meta: { requiresAuth: true } 
  },
  { 
    path: "/profile", 
    name: "Profile", 
    component: () => import('@/pages/frontend/ProfilePage.vue'),
    meta: { requiresAuth: true }
  },
  { 
    path: "/unauthorized",
    name: "Unauthorized",
    component: () => import('@/pages/unauthorized.vue')
  }
]

export default frontendRoutes