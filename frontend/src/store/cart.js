import { defineStore } from "pinia";
import { useAuthStore } from "./auth";
import api from "@/services/api";

export const useCartStore = defineStore("cart", {
  state: () => ({
    items: [],
    loading: false,
    cartId: null,
  }),

  getters: {
    totalItems: (state) =>
      state.items.reduce((total, item) => total + item.quantity, 0),
    subtotal: (state) =>
      state.items.reduce(
        (total, item) => total + item.price * item.quantity,
        0
      ),
    total: (state) => {
      const subtotal = state.items.reduce(
        (total, item) => total + item.price * item.quantity,
        0
      );
      return subtotal;
    },
    hasItems: (state) => state.items.length > 0,
  },

  actions: {
    async hydrate() {
      console.log("🛒 Hydrating cart...");
      const authStore = useAuthStore();
      if (authStore.isAuthenticated) {
        await this.loadCartFromBackend();
      } else {
        this.loadCartFromStorage();
      }
      console.log("🛒 Cart hydrated with items:", this.items.length);
    },

    loadCartFromStorage() {
      const savedCart = localStorage.getItem("cart");
      if (savedCart) {
        try {
          const parsedItems = JSON.parse(savedCart);
          
          // Check if items have the new structure (with product_id) or old structure
          this.items = parsedItems.map(item => {
            if (item.product_id) {
              // New structure
              return item;
            } else {
              // Old structure - migrate
              return {
                id: item.id, // This is product_id in old structure
                product_id: item.id, // Add product_id field
                name: item.name,
                price: item.price,
                image: item.image,
                quantity: item.quantity,
                variant_id: item.variant_id,
                variant_name: item.variant_name,
                size_id: item.size_id,
                size_title: item.size_title,
                stock: item.stock,
              };
            }
          });
          
          console.log("🛒 Loaded cart from storage:", this.items);
        } catch (error) {
          console.error("🛒 Error parsing cart from storage:", error);
          this.items = [];
        }
      } else {
        console.log("🛒 No cart found in storage");
        this.items = [];
      }
    },

    async loadCartFromBackend() {
      this.loading = true;
      try {
        const response = await api.get("/frontend/cart");
        console.log("🛒 Backend cart response:", response.data);

        if (response.data.status && response.data.data) {
          this.cartId = response.data.data.cart_id;
          this.items = response.data.data.items.map((item) => ({
            id: item.id, // cart_item_id
            product_id: item.product_id, // product_id
            name: item.name,
            price: parseFloat(item.price),
            image: item.image,
            quantity: item.quantity,
            variant_id: item.variant_id,
            variant_name: item.variant_name,
            size_id: item.size_id,
            size_title: item.size_title,
            stock: item.stock,
            has_stock: item.has_stock,
            created_at: item.created_at,
          }));

          console.log(
            "🛒 Mapped items:",
            this.items.map((i) => ({
              cart_item_id: i.id,
              product_id: i.product_id,
              variant_id: i.variant_id,
              size_id: i.size_id,
              quantity: i.quantity,
            }))
          );
        }
      } catch (error) {
        console.error("🛒 Error loading cart from backend:", error);
        this.items = [];
      } finally {
        this.loading = false;
      }
    },

    async addToCart(product, quantity = 1, variant = null, size = null) {
      console.log("🛒 Adding to cart:", product.name, quantity);

      const authStore = useAuthStore();

      if (authStore.isAuthenticated) {
        // Add via API
        try {
          const response = await api.post("/frontend/cart/add", {
            product_id: product.id,
            quantity: quantity,
            variant_id: variant?.id || null,
            size_id: size?.id || null,
          });

          console.log("🛒 API add response:", response.data);

          if (response.data.status) {
            await this.loadCartFromBackend();
            return {
              success: true,
              message: response.data.message,
              data: response.data.data,
            };
          } else {
            return {
              success: false,
              message: response.data.message || "Failed to add to cart",
            };
          }
        } catch (error) {
          console.error("🛒 Error adding to cart via API:", error);
          return {
            success: false,
            message: error.response?.data?.message || "Failed to add to cart",
          };
        }
      } else {
        // Add to local storage (guest user)
        const existingItem = this.items.find(
          (item) =>
            (item.product_id || item.id) === product.id &&
            item.variant_id === (variant?.id || null) &&
            item.size_id === (size?.id || null)
        );

        if (existingItem) {
          existingItem.quantity += quantity;
          console.log(
            "🛒 Updated existing item quantity:",
            existingItem.quantity
          );
        } else {
          const newItem = {
            id: product.id, // For guest, id is product_id
            product_id: product.id,
            name: product.name,
            price: size?.price || variant?.price || product.price,
            image:
              variant?.images?.[0]?.url ||
              product.main_image_url ||
              product.image,
            quantity,
            variant_id: variant?.id || null,
            variant_name: variant?.color_name || null,
            size_id: size?.id || null,
            size_title: size?.size_title || size?.size || null,
            stock: size?.stock || variant?.stock || product.stock,
          };
          this.items.push(newItem);
          console.log("🛒 Added new item to cart:", newItem);
        }

        this.persistCart();
        return {
          success: true,
          message: "Item added to cart",
          data: {
            total_items: this.totalItems,
            subtotal: this.subtotal,
          },
        };
      }
    },

    async updateQuantity(cartItemId, quantity) {
      console.log("🛒 DEBUG - updateQuantity called:", {
        cartItemId,
        quantity,
        allItems: this.items
      });

      const authStore = useAuthStore();

      if (authStore.isAuthenticated) {
        try {
          const response = await api.put(
            `/frontend/cart/update/${cartItemId}`,
            {
              quantity: quantity,
            }
          );

          console.log("🛒 API update response:", response.data);

          if (response.data.status) {
            await this.loadCartFromBackend();
            return {
              success: true,
              message: response.data.message,
              data: response.data.data,
            };
          } else {
            return {
              success: false,
              message: response.data.message || "Failed to update quantity",
            };
          }
        } catch (error) {
          console.error("🛒 Error updating quantity via API:", error);
          return {
            success: false,
            message: error.response?.data?.message || "Failed to update quantity",
          };
        }
      } else {
        // Handle guest user case
        const item = this.items.find(item => item.id === cartItemId);
        if (item) {
          if (quantity === 0) {
            this.items = this.items.filter(i => i.id !== cartItemId);
          } else {
            item.quantity = quantity;
          }
          this.persistCart();
          return {
            success: true,
            message: "Quantity updated",
            data: {
              total_items: this.totalItems,
              subtotal: this.subtotal,
            },
          };
        } else {
          return {
            success: false,
            message: "Item not found in cart",
          };
        }
      }
    },

    async removeFromCart(cartItemId) {
      const authStore = useAuthStore();

      if (authStore.isAuthenticated) {
        try {
          const response = await api.delete(
            `/frontend/cart/remove/${cartItemId}`
          );

          console.log("🛒 API remove response:", response.data);

          if (response.data.status) {
            await this.loadCartFromBackend();
            return {
              success: true,
              message: response.data.message,
              data: response.data.data,
            };
          } else {
            return {
              success: false,
              message: response.data.message || "Failed to remove item",
            };
          }
        } catch (error) {
          console.error("🛒 Error removing item via API:", error);
          return {
            success: false,
            message: error.response?.data?.message || "Failed to remove item",
          };
        }
      } else {
        this.items = this.items.filter(item => item.id !== cartItemId);
        this.persistCart();
        return {
          success: true,
          message: "Item removed from cart",
          data: {
            total_items: this.totalItems,
            subtotal: this.subtotal,
          },
        };
      }
    },

    async clearCart() {
      const authStore = useAuthStore();

      if (authStore.isAuthenticated) {
        try {
          const response = await api.post("/frontend/cart/clear");

          console.log("🛒 API clear response:", response.data);

          if (response.data.status) {
            this.items = [];
            return {
              success: true,
              message: response.data.message,
              data: response.data.data,
            };
          } else {
            return {
              success: false,
              message: response.data.message || "Failed to clear cart",
            };
          }
        } catch (error) {
          console.error("🛒 Error clearing cart via API:", error);
          return {
            success: false,
            message: error.response?.data?.message || "Failed to clear cart",
          };
        }
      } else {
        this.items = [];
        this.persistCart();
        return {
          success: true,
          message: "Cart cleared",
          data: {
            total_items: 0,
            subtotal: 0,
          },
        };
      }
    },

    async mergeGuestCart() {
      const authStore = useAuthStore();

      if (authStore.isAuthenticated && this.items.length > 0) {
        try {
          const guestItems = this.items.map((item) => ({
            product_id: item.product_id || item.id,
            quantity: item.quantity,
            variant_id: item.variant_id,
            size_id: item.size_id,
            price: item.price,
          }));

          console.log("🛒 Merging guest items:", guestItems);

          const response = await api.post("/frontend/cart/merge", {
            guest_items: guestItems,
          });

          console.log("🛒 Merge cart response:", response.data);

          if (response.data.status) {
            this.items = [];
            localStorage.removeItem("cart");
            await this.loadCartFromBackend();

            return {
              success: true,
              message: response.data.message,
              data: response.data.data,
            };
          } else {
            return {
              success: false,
              message: response.data.message || "Failed to merge cart",
            };
          }
        } catch (error) {
          console.error("🛒 Error merging cart via API:", error);
          return {
            success: false,
            message: error.response?.data?.message || "Failed to merge cart",
          };
        }
      }
      return {
        success: true,
        message: "No items to merge",
      };
    },

    persistCart() {
      console.log("🛒 Persisting cart with items:", this.items);
      const authStore = useAuthStore();

      if (!authStore.isAuthenticated) {
        localStorage.setItem("cart", JSON.stringify(this.items));
        console.log("🛒 Cart saved to localStorage");
      }
    },
  },
});