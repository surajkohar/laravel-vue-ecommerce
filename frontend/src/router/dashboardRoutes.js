import DashboardLayout from '@/layouts/DashboardLayout.vue'

const dashboardRoutes = [
  {
    path: '/admin',
    component: () => DashboardLayout,
    children: [
      {
        path: '',
        name: 'AdminHome',
        component: () => import('@/pages/dashboard/DashboardHome.vue')  // Dashboard Home page
      },
      {
        path: 'products',
        name: 'ProductsPage',
        component: () => import('@/pages/dashboard/products/listing.vue'),  // Product Listing page
      },
      {
        path: 'product/add',
        name: 'ProductAddPage',
        component: () => import('@/pages/dashboard/products/add.vue'),  // Product add page
      },
      {
        path: 'product/view',
        name: 'ProductViewPage',
        component: () => import('@/pages/dashboard/products/view.vue'),  // Product add page
      },
      {
        path: 'product/edit',
        name: 'ProductEditPage',
        component: () => import('@/pages/dashboard/products/edit.vue'),  // Product edit page
      },
    ]
  }
]

export default dashboardRoutes;

