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
  path: '/product/:id',
  name: 'ProductDetail',
  component: () => import('@/pages/frontend/ProductDetail.vue'),
  meta: { title: 'Product Details - Pinders' }
},
{
  path: '/wishlist',
  name: 'Wishlist',
  component: () => import('@/pages/frontend/Wishlist.vue'),
  meta: { title: 'My Wishlist - Pinders' }
},
{
  path: '/checkout',
  name: 'Checkout',
  component: () => import('@/pages/frontend/CheckoutPage.vue'),
  meta: { title: 'My Wishlist - Pinders' }
},
{
  path: '/myorders',
  name: 'My Orders',
  component: () => import('@/pages/frontend/MyOrders.vue'),
  meta: { title: 'My Wishlist - Pinders' }
},
{
  path: '/order-history',
  name: 'History',
  component: () => import('@/pages/frontend/OrderHistory.vue'),
  meta: { title: 'My Wishlist - Pinders' }
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