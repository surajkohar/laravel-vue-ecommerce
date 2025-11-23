import { defineStore } from 'pinia'
import { API } from '@/utils/config'
import axios from 'axios'

export const useProductsStore = defineStore('products', {
  state: () => ({
    products: [],
    featuredProducts: [],
    categories: [],
    brands: [],
    filters: {
      search: '',
      category: '',
      brand: '',
      gender: '',
      min_price: '',
      max_price: '',
      sort_by: 'created_at',
      sort_order: 'desc'
    },
    pagination: {
      current_page: 1,
      last_page: 1,
      per_page: 12,
      total: 0
    },
    loading: false,
    selectedProduct: null
  }),

  getters: {
    hasProducts: (state) => state.products.length > 0,
    hasFilters: (state) => {
      return Object.values(state.filters).some(value => 
        value !== '' && value !== null && value !== undefined
      )
    }
  },

  actions: {
    async fetchProducts(page = 1) {
      this.loading = true
      try {
        // For now, use mock data - replace with actual API later
        await new Promise(resolve => setTimeout(resolve, 1000)) // Simulate API delay
        
        // Mock data - replace with: const response = await axios.get(`${API.BACKEND_URL}/products`, { params })
        const mockProducts = this.generateMockProducts()
        this.products = mockProducts
        this.pagination = {
          current_page: page,
          last_page: 3,
          per_page: 12,
          total: 36
        }
      } catch (error) {
        console.error('Error fetching products:', error)
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchFeaturedProducts() {
      try {
        // Mock data - replace with actual API
        await new Promise(resolve => setTimeout(resolve, 500))
        this.featuredProducts = this.generateMockProducts().slice(0, 8)
      } catch (error) {
        console.error('Error fetching featured products:', error)
      }
    },

    async fetchCategories() {
      try {
        // Mock data - replace with actual API
        await new Promise(resolve => setTimeout(resolve, 300))
        this.categories = [
          { id: 1, name: 'Electronics', slug: 'electronics', product_count: 150 },
          { id: 2, name: 'Clothing', slug: 'clothing', product_count: 200 },
          { id: 3, name: 'Home & Garden', slug: 'home-garden', product_count: 120 },
          { id: 4, name: 'Sports', slug: 'sports', product_count: 80 },
          { id: 5, name: 'Beauty', slug: 'beauty', product_count: 90 },
          { id: 6, name: 'Toys', slug: 'toys', product_count: 60 }
        ]
      } catch (error) {
        console.error('Error fetching categories:', error)
      }
    },

    async fetchBrands() {
      try {
        // Mock data - replace with actual API
        await new Promise(resolve => setTimeout(resolve, 300))
        this.brands = ['Nike', 'Adidas', 'Apple', 'Samsung', 'Sony', 'LG', 'Dell', 'HP']
      } catch (error) {
        console.error('Error fetching brands:', error)
      }
    },

    async fetchProductById(id) {
      try {
        // Mock data - replace with actual API
        await new Promise(resolve => setTimeout(resolve, 800))
        const mockProduct = this.generateMockProductDetail(id)
        this.selectedProduct = mockProduct
        return mockProduct
      } catch (error) {
        console.error('Error fetching product:', error)
        throw error
      }
    },

    updateFilters(newFilters) {
      this.filters = { ...this.filters, ...newFilters }
    },

    resetFilters() {
      this.filters = {
        search: '',
        category: '',
        brand: '',
        gender: '',
        min_price: '',
        max_price: '',
        sort_by: 'created_at',
        sort_order: 'desc'
      }
    },

    setPage(page) {
      this.pagination.current_page = page
    },

    // Mock data generators - REMOVE THESE WHEN USING REAL API
    // In the generateMockProducts method, update to clothing-specific data:
generateMockProducts() {
  const products = []
  const categories = ['Men Fashion', 'Women Fashion', 'Kids Wear', 'Accessories', 'Footwear']
  const brands = ['Nike', 'Adidas', 'Zara', 'H&M', 'Levi\'s', 'Puma', 'Gucci', 'Prada']
  const clothingTypes = ['T-Shirt', 'Jeans', 'Dress', 'Jacket', 'Sweater', 'Shorts', 'Skirt', 'Hoodie']
  
  for (let i = 1; i <= 12; i++) {
    const type = clothingTypes[Math.floor(Math.random() * clothingTypes.length)]
    const brand = brands[Math.floor(Math.random() * brands.length)]
    
    products.push({
      id: i,
      name: `${brand} ${type} - Fashion Collection`,
      description: `Premium quality ${type.toLowerCase()} from ${brand}. Perfect for casual wear with comfortable fabric and modern design.`,
      price: (Math.random() * 100 + 20).toFixed(2),
      purchase_price: (Math.random() * 150 + 30).toFixed(2),
      sku: `FASH${1000 + i}`,
      stock: Math.floor(Math.random() * 100),
      gender: ['men', 'women', 'unisex'][Math.floor(Math.random() * 3)],
      status: 'active',
      brand: brand,
      category: categories[Math.floor(Math.random() * categories.length)],
      main_image_url: `https://picsum.photos/400/500?random=${i}&fashion=1`,
      variants_count: Math.floor(Math.random() * 5) + 1
    })
  }
  return products
},

    generateMockProductDetail(id) {
      return {
        id: id,
        name: `Product ${id} - Premium Quality Item`,
        description: `This is a detailed description of an amazing product that offers great value for money. Features include high-quality materials, excellent craftsmanship, and innovative design that sets it apart from competitors. Perfect for various uses and built to last.`,
        price: '89.99',
        purchase_price: '129.99',
        sku: `SKU${1000 + id}`,
        stock: 45,
        gender: 'unisex',
        status: 'active',
        brand: 'PremiumBrand',
        category: 'Electronics',
        main_image_url: `https://picsum.photos/600/600?random=${id}`,
        variants: [
          {
            id: 1,
            color: '#FF0000',
            color_name: 'Red',
            sizes: [
              { id: 1, size_title: 'S', price: '89.99', stock: 10 },
              { id: 2, size_title: 'M', price: '89.99', stock: 15 },
              { id: 3, size_title: 'L', price: '89.99', stock: 12 },
              { id: 4, size_title: 'XL', price: '94.99', stock: 8 }
            ],
            images: [
              { id: 1, image_name: 'red1.jpg', url: `https://picsum.photos/400/400?random=${id}1` },
              { id: 2, image_name: 'red2.jpg', url: `https://picsum.photos/400/400?random=${id}2` }
            ]
          },
          {
            id: 2,
            color: '#0000FF',
            color_name: 'Blue',
            sizes: [
              { id: 5, size_title: 'S', price: '89.99', stock: 8 },
              { id: 6, size_title: 'M', price: '89.99', stock: 20 },
              { id: 7, size_title: 'L', price: '89.99', stock: 10 }
            ],
            images: [
              { id: 3, image_name: 'blue1.jpg', url: `https://picsum.photos/400/400?random=${id}3` },
              { id: 4, image_name: 'blue2.jpg', url: `https://picsum.photos/400/400?random=${id}4` }
            ]
          }
        ]
      }
    }
  }
})