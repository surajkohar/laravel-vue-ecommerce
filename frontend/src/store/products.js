// store/products.js
import { defineStore } from "pinia";
import { API } from "@/utils/config";

export const useProductsStore = defineStore("products", {
  state: () => ({
    products: [],
    productDetails: {}, // Store detailed product data by ID
    featuredProducts: [],
    categories: [],
    brands: [],
    pagination: {
      current_page: 1,
      last_page: 1,
      per_page: 12,
      total: 0,
    },
    loading: false,
    loadingDetail: false,
    filters: {
      category: "",
      brand: "",
      min_price: "",
      max_price: "",
      sort_by: "newest",
      search: "",
    },
  }),

  getters: {
    hasProducts: (state) => state.products.length > 0,

    // Get product detail by ID
    getProductDetail: (state) => (productId) => {
      return state.productDetails[productId] || null;
    },

    // Check if product has any stock (considering variants)
    hasProductStock: (state) => (productId) => {
      const product = state.products.find((p) => p.id === productId);
      if (!product) return false;

      // Check product stock
      if (product.stock > 0) return true;

      // Check if we have detailed data with variants
      const detail = state.productDetails[productId];
      if (detail?.variants) {
        // Check if any variant has any size with stock > 0
        return detail.variants.some((variant) =>
          variant.sizes.some((size) => size.stock > 0)
        );
      }

      return product.stock > 0;
    },

    // Filtered products
    filteredProducts: (state) => {
      let filtered = [...state.products];

      // Filter by category
      if (state.filters.category) {
        filtered = filtered.filter(
          (product) => product.category_id == state.filters.category
        );
      }

      // Filter by brand
      if (state.filters.brand) {
        filtered = filtered.filter(
          (product) => product.brand_id == state.filters.brand
        );
      }

      // Filter by price range
      if (state.filters.min_price) {
        filtered = filtered.filter((product) => {
          const price = product.price || product.min_price;
          return price >= parseFloat(state.filters.min_price);
        });
      }

      if (state.filters.max_price) {
        filtered = filtered.filter((product) => {
          const price = product.price || product.max_price;
          return price <= parseFloat(state.filters.max_price);
        });
      }

      // Filter by search
      if (state.filters.search) {
        const searchTerm = state.filters.search.toLowerCase();
        filtered = filtered.filter(
          (product) =>
            product.name.toLowerCase().includes(searchTerm) ||
            product.description?.toLowerCase().includes(searchTerm) ||
            product.sku.toLowerCase().includes(searchTerm)
        );
      }

      // Sort products
      switch (state.filters.sort_by) {
        case "price_low":
          filtered.sort(
            (a, b) => (a.price || a.min_price) - (b.price || b.min_price)
          );
          break;
        case "price_high":
          filtered.sort(
            (a, b) => (b.price || b.max_price) - (a.price || a.max_price)
          );
          break;
        case "name_asc":
          filtered.sort((a, b) => a.name.localeCompare(b.name));
          break;
        case "name_desc":
          filtered.sort((a, b) => b.name.localeCompare(a.name));
          break;
        case "newest":
        default:
          filtered.sort(
            (a, b) => new Date(b.created_at) - new Date(a.created_at)
          );
          break;
      }

      return filtered;
    },
  },

  actions: {
    // Update filters
    updateFilters(newFilters) {
      this.filters = { ...this.filters, ...newFilters };
    },

    // Reset all filters
    resetFilters() {
      this.filters = {
        category: "",
        brand: "",
        min_price: "",
        max_price: "",
        sort_by: "newest",
        search: "",
      };
    },

    // FRONTEND API: Get all products for customers
// In fetchProducts action:
async fetchProducts(page = 1) {
  this.loading = true
  try {
    const params = new URLSearchParams({
      page: page,
      per_page: this.pagination.per_page,
      ...Object.fromEntries(
        Object.entries(this.filters).filter(([_, v]) => v !== '' && v !== null)
      )
    })

    const response = await fetch(`${API.FRONTEND_URL}/products?${params}`)
    
    if (!response.ok) {
      throw new Error('Failed to fetch products')
    }

    const data = await response.json()
    
    if (data.status && data.data) {
      // Ensure each product has a slug
      this.products = data.data.map(product => ({
        ...product,
        // Add fallback slug if missing
        slug: product.slug || `product-${product.id}`
      }))
      this.updatePagination(data.meta || {})
    }
  } catch (error) {
    console.error('Error fetching products:', error)
    throw error
  } finally {
    this.loading = false
  }
},

    // Add fetchFeaturedProducts action
    async fetchFeaturedProducts() {
      this.loading = true;
      try {
        const response = await fetch(`${API.FRONTEND_URL}/products/featured`);

        if (!response.ok) {
          throw new Error("Failed to fetch featured products");
        }

        const data = await response.json();
        if (data.status && data.data) {
          this.featuredProducts = data.data;
        }
      } catch (error) {
        console.error("Error fetching featured products:", error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // FRONTEND API: Get single product details with variants
    async fetchProductBySlug(slug) {
      this.loadingDetail = true;
      try {
        const response = await fetch(`${API.FRONTEND_URL}/products/${slug}`);

        if (!response.ok) {
          throw new Error("Product not found");
        }

        const data = await response.json();
        if (data.status && data.data) {
          const productData = data.data;

          // Store product details by ID
          this.productDetails[productData.product.id] = productData;

          return productData;
        }
      } catch (error) {
        console.error("Error fetching product:", error);
        throw error;
      } finally {
        this.loadingDetail = false;
      }
    },

    // FRONTEND API: Get product by ID (with variants)
    async fetchProductById(id) {
      this.loadingDetail = true;
      try {
        const response = await fetch(`${API.FRONTEND_URL}/products/id/${id}`);

        if (!response.ok) {
          throw new Error("Product not found");
        }

        const data = await response.json();
        if (data.status && data.data) {
          const productData = data.data;
          this.productDetails[productData.id] = productData;
          return productData;
        }
      } catch (error) {
        console.error("Error fetching product:", error);
        throw error;
      } finally {
        this.loadingDetail = false;
      }
    },

    // For fetching categories
    async fetchCategories() {
      try {
        const response = await fetch(`${API.FRONTEND_URL}/categories`);
        if (response.ok) {
          const data = await response.json();
          if (data.status && data.data) {
            this.categories = data.data;
          }
        }
      } catch (error) {
        console.error("Error fetching categories:", error);
      }
    },

    // For fetching brands
    async fetchBrands() {
      try {
        const response = await fetch(`${API.FRONTEND_URL}/brands`);
        if (response.ok) {
          const data = await response.json();
          if (data.status && data.data) {
            this.brands = data.data;
          }
        }
      } catch (error) {
        console.error("Error fetching brands:", error);
      }
    },

    // Update pagination helper
    updatePagination(meta) {
      this.pagination = {
        current_page: meta.current_page || 1,
        last_page: meta.last_page || 1,
        per_page: meta.per_page || 12,
        total: meta.total || 0,
      };
    },

    // Calculate product availability based on variants
    calculateProductAvailability(productId) {
      const detail = this.productDetails[productId];
      if (!detail || !detail.variants) {
        const product = this.products.find((p) => p.id === productId);
        return {
          inStock: product?.stock > 0 || false,
          totalStock: product?.stock || 0,
          availableColors: [],
          availableSizes: [],
        };
      }

      let totalStock = 0;
      const availableColors = [];
      const availableSizes = new Set();

      detail.variants.forEach((variant) => {
        let variantStock = 0;

        variant.sizes.forEach((size) => {
          if (size.stock > 0) {
            variantStock += size.stock;
            availableSizes.add(size.size_title);
          }
        });

        if (variantStock > 0) {
          totalStock += variantStock;
          availableColors.push({
            id: variant.id,
            color: variant.color,
            name: variant.color_name,
            stock: variantStock,
          });
        }
      });

      return {
        inStock: totalStock > 0,
        totalStock,
        availableColors,
        availableSizes: Array.from(availableSizes),
      };
    },

    // Get price range for product
    getProductPriceRange(productId) {
      const detail = this.productDetails[productId];
      if (!detail || !detail.variants) {
        const product = this.products.find((p) => p.id === productId);
        const price = product?.price || 0;
        return { min: price, max: price };
      }

      let minPrice = Infinity;
      let maxPrice = 0;

      detail.variants.forEach((variant) => {
        variant.sizes.forEach((size) => {
          const price = parseFloat(size.price);
          if (price < minPrice) minPrice = price;
          if (price > maxPrice) maxPrice = price;
        });
      });

      return {
        min: minPrice === Infinity ? 0 : minPrice,
        max: maxPrice,
      };
    },

    // Clear product details cache
    clearProductDetails(productId = null) {
      if (productId) {
        delete this.productDetails[productId];
      } else {
        this.productDetails = {};
      }
    },
  },
});
