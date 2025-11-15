import DashboardLayout from '@/layouts/DashboardLayout.vue'
import EmailTemplates from '@/pages/emails/EmailTemplatesList.vue'
import EmailTemplateAdd from '@/pages/emails/EmailTemplateAdd.vue'

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
        path: 'product/:id/view',
        name: 'ProductViewPage',
        component: () => import('@/pages/dashboard/products/view.vue'),  // Product view page
        meta: { permission: 'view products' }
      },
      {
        path: 'product/:id/edit',
        name: 'ProductEditPage',
        component: () => import('@/pages/dashboard/products/edit.vue'),  // Product edit page
        meta: { permission: 'delete product' }
      },
      {
        path: 'sizes',
        name: 'sizes.listing',
        component: () => import('@/pages/dashboard/sizes/listing.vue'), 
        meta: { permission: 'view sizes' }
      },
      {
        path: 'brands',
        name: 'brands.listing',
        component: () => import('@/pages/dashboard/brands/listing.vue'), 
        meta: { permission: 'view sizes' }
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
      {
        path: 'product-category',
        name: 'productCategory',
        component: () => import('@/pages/dashboard/productCategory/listing.vue'),  
        // meta: { permission: 'edit user' }
      },
      {
        path: 'product-category/add',
        name: 'productCategory.add',
        component: () => import('@/pages/dashboard/productCategory/add.vue'),  
        // meta: { permission: 'edit user' }
      },
      {
        path: 'product-category/:id/edit',
        name: 'productCategory.edit',
        component: () => import('@/pages/dashboard/productCategory/edit.vue'),  
        // meta: { permission: 'edit user' }
      },
      {
        path: 'product-subCategory',
        name: 'productSubCategory',
        component: () => import('@/pages/dashboard/productSubCategory/listing.vue'),  
        // meta: { permission: 'edit user' }
      },
      {
        path: 'product-subCategory/add',
        name: 'productSubCategory.add',
        component: () => import('@/pages/dashboard/productSubCategory/add.vue'),  
        // meta: { permission: 'edit user' }
      },
      {
        path: 'product-subCategory/:id/edit',
        name: 'productSubCategory.edit',
        component: () => import('@/pages/dashboard/productSubCategory/edit.vue'),  
        // meta: { permission: 'edit user' }
      },
      {
        path: 'email-templates',
        name: 'EmailTemplates',
        component: EmailTemplates,
        // meta: { requiresAuth: true, permission: 'manage_email_templates' }
      },
      {
    path: '/admin/email-template/add',
    name: 'EmailTemplateAdd',
    component: EmailTemplateAdd
  },
  {
    path: '/admin/email-template/:id/edit',
    name: 'EmailTemplateEdit',
    component: EmailTemplateAdd, // Same component as add
    props: true
  }
    ]
  }
]

export default dashboardRoutes;

