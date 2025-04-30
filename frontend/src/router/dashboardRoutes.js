import DashboardLayout from '@/layouts/DashboardLayout.vue'

const dashboardRoutes = [
  {
    path: '/admin',
    component: () => DashboardLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'AdminHome',
        component: () => import('@/pages/dashboard/DashboardHome.vue'),  // Dashboard Home page
        meta: { permission: 'view dashboard' }
      },
      {
        path: 'roles',
        name: 'role.listing',
        component: () => import('@/pages/dashboard/roles/listing.vue'),
        meta: { permission: 'view roles' }

      },
      {
        path: 'role/add',
        name: 'role.add',
        component: () => import('@/pages/dashboard/roles/add.vue'),
        meta: { permission: 'add role' }

      },
      {
        path: 'role/:id/edit',
        name: 'role.edit',
        component: () => import('@/pages/dashboard/roles/edit.vue'),
        meta: { permission: 'edit role' }
      },
      {
        path: 'permissions',
        name: 'permission.listing',
        component: () => import('@/pages/dashboard/permissions/listing.vue'), 
        meta: { permission: 'view permission' }
      },
      {
        path: 'permission/add',
        name: 'permission.add',
        component: () => import('@/pages/dashboard/permissions/add.vue'), 
        meta: { permission: 'add permission' }
      },
      {
        path: 'permission/:id/edit',
        name: 'permission.edit',
        component: () => import('@/pages/dashboard/permissions/edit.vue'), 
        meta: { permission: 'edit permission' }
      },
      {
        path: 'products',
        name: 'ProductsPage',
        component: () => import('@/pages/dashboard/products/listing.vue'),  // Product Listing page
        meta: { permission: 'view products' }
      },
      {
        path: 'product/add',
        name: 'ProductAddPage',
        component: () => import('@/pages/dashboard/products/add.vue'),  // Product add page
        meta: { permission: 'add product' }
      },
      {
        path: 'product/view',
        name: 'ProductViewPage',
        component: () => import('@/pages/dashboard/products/view.vue'),  // Product view page
        meta: { permission: 'view product' }
      },
      {
        path: 'product/edit',
        name: 'ProductEditPage',
        component: () => import('@/pages/dashboard/products/edit.vue'),  // Product edit page
        meta: { permission: 'delete product' }
      },
      {
        path: 'users',
        name: 'users.listing',
        component: () => import('@/pages/dashboard/users/listing.vue'),  
        meta: { permission: 'view user' }
      },
      {
        path: 'user/add',
        name: 'users.add',
        component: () => import('@/pages/dashboard/users/add.vue'),  
        meta: { permission: 'add user' }

      },
      {
        path: 'user/:id/edit',
        name: 'user.edit',
        component: () => import('@/pages/dashboard/users/edit.vue'),  
        meta: { permission: 'edit user' }
      },
    ]
  }
]

export default dashboardRoutes;

