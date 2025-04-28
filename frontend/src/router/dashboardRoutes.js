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
        path: 'roles',
        name: 'role.listing',
        component: () => import('@/pages/dashboard/roles/listing.vue'),  
      },
      {
        path: 'role/add',
        name: 'role.add',
        component: () => import('@/pages/dashboard/roles/add.vue'),  
      },
      {
        path: 'role/:id/edit',
        name: 'role.edit',
        component: () => import('@/pages/dashboard/roles/edit.vue'),  
      },
      {
        path: 'permissions',
        name: 'permission.listing',
        component: () => import('@/pages/dashboard/permissions/listing.vue'),  
      },
      {
        path: 'permission/add',
        name: 'permission.add',
        component: () => import('@/pages/dashboard/permissions/add.vue'),  
      },
      {
        path: 'permission/:id/edit',
        name: 'permission.edit',
        component: () => import('@/pages/dashboard/permissions/edit.vue'),  
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
        component: () => import('@/pages/dashboard/products/view.vue'),  // Product view page
      },
      {
        path: 'product/edit',
        name: 'ProductEditPage',
        component: () => import('@/pages/dashboard/products/edit.vue'),  // Product edit page
      },
      {
        path: 'users',
        name: 'users.listing',
        component: () => import('@/pages/dashboard/users/listing.vue'),  
      },
      {
        path: 'user/add',
        name: 'users.add',
        component: () => import('@/pages/dashboard/users/add.vue'),  
      },
      {
        path: 'user/:id/edit',
        name: 'user.edit',
        component: () => import('@/pages/dashboard/users/edit.vue'),  
      },
    ]
  }
]

export default dashboardRoutes;

