<template>
  <div class="add-product-container">
    <!-- Header Section -->
    <div class="header-section-add">
      <h1>Edit Product</h1>
      <div class="action-buttons">
        <button class="cancel-button" @click="goBack">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
          </svg>
          Back
        </button>
      </div>
    </div>

    <!-- Main Form -->
    <div class="form-container" v-if="!loading">
      <div class="form-card">
        <!-- Basic Information Section -->
        <div class="form-section">
          <h2>Basic Information</h2>
          <div class="form-row">
            <div class="form-group">
              <label>Gender</label>
              <Select placeholder="Select gender" v-model="product.gender" :options="genders" option-label="title"
                option-value="slug" />
            </div>
            <div class="form-group">
              <label>Category</label>
              <Select v-model="product.category_id" placeholder="Select Category" :options="categories"
                option-label="title" option-value="id" @update:modelValue="filterSubCategory" />
            </div>
          </div>
          <div class="form-group full-width-field">
            <label>Title</label>
            <input type="text" v-model="product.name" placeholder="Enter product name" />
          </div>
          <div class="form-group full-width-field">
            <label>Description</label>
            <QuillEditor v-model:content="product.description" contentType="html" theme="snow" :options="editorOptions"
              placeholder="Enter product description" />
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Purchase Price (£)</label>
              <input type="number" v-model.number="product.purchasePrice" placeholder="0.00" min="0" step="0.01" />
            </div>
            <div class="form-group">
              <label>Selling Price (£)</label>
              <input type="number" v-model.number="product.price" placeholder="0.00" min="0" step="0.01" />
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Sub Categories</label>
              <MultiSelect v-model="product.subcategory_ids" :options="filteredSubCategories"
                placeholder="Select subcategories" option-label="title" option-value="id" />
            </div>
            <div class="form-group">
              <label>Brand</label>
              <Select v-model="product.brand" placeholder="Select brand" :options="brands" option-label="title"
                option-value="slug" />
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>SKU</label>
              <input type="text" v-model="product.sku" placeholder="Product SKU" />
            </div>
            <div class="form-group">
              <label>Stock Quantity</label>
              <input type="number" v-model.number="product.stock" placeholder="Total stock" min="0" />
            </div>
          </div>

          <!-- Main Product Image -->
          <div class="form-group full-width-field">
            <label>Main Product Image</label>
            <div class="image-uploader">
              <div class="upload-area" @click="triggerMainImageUpload">
                <template v-if="!product.mainImageFile">
                  <img v-if="product.mainImageUrl" :src="product.mainImageUrl" alt="Current main product image"
                    class="main-image-preview" />
                  <template v-else>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7"></path>
                      <polyline points="16 5 22 5 22 11"></polyline>
                      <line x1="16" y1="5" x2="22" y2="11"></line>
                    </svg>
                    <p>Click to upload new main product image</p>
                  </template>
                </template>
                <template v-else>
                  <img :src="product.mainImageFile.preview" alt="New main product image preview"
                    class="main-image-preview" />
                </template>
                <input type="file" ref="mainImageInput" @change="handleMainImageUpload" accept="image/*"
                  style="display: none" />
              </div>
              <button v-if="product.mainImageFile || product.mainImageUrl" class="remove-main-image"
                @click="removeMainImage">
                Remove Image
              </button>
            </div>
          </div>

          <!-- Size Guide Upload -->
          <div class="form-group full-width-field">
            <label>Size Guide (PDF)</label>
            <div class="file-uploader">
              <div class="upload-area" @click="triggerSizeGuideUpload">
                <template v-if="!product.sizeGuideFile">
                  <template v-if="product.sizeGuideUrl">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                      <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    <p>Current: {{ product.sizeGuideName }}</p>
                  </template>
                  <template v-else>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                      <polyline points="14 2 14 8 20 8"></polyline>
                      <line x1="16" y1="13" x2="8" y2="13"></line>
                      <line x1="16" y1="17" x2="8" y2="17"></line>
                      <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    <p>Click to upload new size guide (PDF)</p>
                  </template>
                </template>
                <template v-else>
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                  </svg>
                  <p>{{ product.sizeGuideFile.name }}</p>
                </template>
                <input type="file" ref="sizeGuideInput" @change="handleSizeGuideUpload" accept=".pdf"
                  style="display: none" />
              </div>
              <button v-if="product.sizeGuideFile || product.sizeGuideUrl" class="remove-size-guide"
                @click="removeSizeGuide">
                Remove File
              </button>
            </div>
          </div>
        </div>

        <!-- Color Variants Section -->
        <div class="form-section">
          <h2>Color Variants</h2>
          <div class="color-management">
            <!-- Color Selection -->
            <div class="color-selection">
              <label>Select Colors:</label>
              <div class="color-palette">
                <div v-for="color in availableColors" :key="color.hex" class="color-option"
                  :class="{ selected: isColorSelected(color.hex) }" :style="{ backgroundColor: color.hex }"
                  @click="toggleColor(color)">
                  <span v-if="isColorSelected(color.hex)" class="checkmark">✓</span>
                </div>
              </div>
            </div>

            <!-- Variant Details Tabs -->
            <div class="variant-tabs" v-if="product.variants.length > 0">
              <button v-for="variant in product.variants" :key="variant.color" class="variant-tab"
                :class="{ active: activeVariant === variant.color }" @click="setActiveVariant(variant.color)">
                <span class="color-swatch" :style="{ backgroundColor: variant.color }"></span>
                {{ variant.color_name || getColorName(variant.color) }}
                <span class="remove-color" @click.stop="removeVariant(variant.color)">×</span>
              </button>
            </div>

            <!-- Active Variant Details -->
            <div class="variant-details" v-if="activeVariant">
              <!-- Size Selection -->
              <div class="size-selection">
                <label>Available Sizes for {{ getActiveVariant().color_name || getColorName(activeVariant) }}:</label>
                <div class="size-options">
                  <button v-for="size in sizes" :key="size.id" class="size-option"
                    :class="{ selected: isSizeSelected(activeVariant, size) }" @click="toggleSize(activeVariant, size)">
                    {{ size.size_title }}
                  </button>
                </div>
              </div>

              <!-- Image Upload -->
              <div class="image-upload-section">
                <label>Images for {{ getActiveVariant().color_name || getColorName(activeVariant) }}:</label>
                <div class="image-uploader">
                  <div class="upload-area" @click="triggerFileUpload">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7"></path>
                      <polyline points="16 5 22 5 22 11"></polyline>
                      <line x1="16" y1="5" x2="22" y2="11"></line>
                    </svg>
                    <p>Click to upload new images</p>
                    <input type="file" ref="fileInput" @change="handleFileUpload" multiple accept="image/*"
                      style="display: none" />
                  </div>
                  <div class="image-previews">
                    <!-- Existing images -->
                    <div v-for="(image, index) in getActiveVariant().existingImages" :key="'existing-' + index"
                      class="image-preview">
                      <img :src="image.url" :alt="`Current image ${index + 1}`" />
                      <button class="remove-image" @click="markImageForDeletion(getActiveVariant(), index)">
                        ×
                      </button>
                      <span v-if="image.markedForDeletion" class="deleted-badge">Deleted</span>
                    </div>
                    <!-- New images -->
                    <div v-for="(image, index) in getActiveVariant().newImages" :key="'new-' + index"
                      class="image-preview">
                      <img :src="image.preview" :alt="`New image preview ${index + 1}`" />
                      <button class="remove-image" @click="removeNewImage(activeVariant, index)">
                        ×
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-section mt-3">
                <h2>Status</h2>
                <div class="d-flex align-items-center">
                  <label class="toggle-switch">
                    <input type="checkbox" v-model="product.status" :true-value="1" :false-value="0" />
                    <span class="slider round"></span>
                  </label>
                  &nbsp;&nbsp;
                  <span class="toggle-label">{{
                    product.status == 1 ? "Active" : "Inactive"
                    }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
          <button class="cancel-button" @click="goBack">Cancel</button>
          <button class="save-button" @click="updateProduct">
            <i v-if="saving" class="fas fa-spinner"></i>
            <svg v-if="!saving" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
              fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
              <polyline points="17 21 17 13 7 13 7 21"></polyline>
              <polyline points="7 3 7 8 15 8"></polyline>
            </svg>
            {{ !saving ? 'Update' : 'updating' }}
          </button>
        </div>
      </div>
    </div>
    <div v-else class="loading-container">
      Loading product data...
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { API } from '@/utils/config';
import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import Select from "@/components/Select.vue";
import MultiSelect from "@/components/MultiSelect.vue";
import { toast } from 'vue3-toastify'

const route = useRoute();
const router = useRouter();
const fileInput = ref(null);
const mainImageInput = ref(null);
const sizeGuideInput = ref(null);
const loading = ref(true);
const saving = ref(false);
const categories = ref([]);
const subCategories = ref([]);
const brands = ref([]);
const sizes = ref([]);
const availableColors = ref([]);
const token = localStorage.getItem('auth_token');

const genders = [
  { id: '1', slug: 'men', title: 'Men' },
  { id: '2', slug: 'women', title: 'Women' },
  { id: '3', slug: 'unisex', title: 'Unisex' },
  { id: '4', slug: 'kids', title: 'Kids' },
];

const product = ref({
  id: null,
  name: "",
  gender: "",
  description: "",
  status: 0,
  purchasePrice: 0,
  price: 0,
  category_id: null,
  subcategory_ids: [],
  brand: "",
  sku: "",
  stock: 0,
  mainImageFile: null,
  mainImageUrl: "",
  sizeGuideFile: null,
  sizeGuideUrl: "",
  sizeGuideName: "",
  variants: []
});

const activeVariant = ref(null);

const editorOptions = {
  modules: {
    toolbar: [
      ["bold", "italic", "underline", "strike"],
      ["blockquote", "code-block"],
      [{ header: 1 }, { header: 2 }],
      [{ list: "ordered" }, { list: "bullet" }],
      [{ script: "sub" }, { script: "super" }],
      [{ indent: "-1" }, { indent: "+1" }],
      [{ direction: "rtl" }],
      [{ size: ["small", false, "large", "huge"] }],
      [{ header: [1, 2, 3, 4, 5, 6, false] }],
      [{ color: [] }, { background: [] }],
      [{ font: [] }],
      [{ align: [] }],
      ["clean"],
      ["link", "image"],
    ],
  },
  placeholder: "Enter product description...",
  theme: "snow",
};

onMounted(async () => {
  await fetchData();
  await fetchProduct();
});

const fetchData = async () => {
  try {
    const response = await fetch(`${API.BACKEND_URL}/product-fetch-data`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
    });

    if (!response.ok) {
      throw new Error('Failed to fetch categories');
    }

    const data = await response.json();
    categories.value = data.category;
    subCategories.value = data.subCategory;
    brands.value = data.brands;
    sizes.value = data.sizes;
    availableColors.value = data.colors.map(c => ({
      hex: c.color_code,
      name: c.title,
    }));
  } catch (err) {
    console.error('Error fetching categories:', err);
  }
};

const fetchProduct = async () => {
  try {
    loading.value = true;
    const response = await fetch(`${API.BACKEND_URL}/product/${route.params.id}/edit`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
    });

    if (!response.ok) {
      throw new Error('Failed to fetch product data');
    }

    const data = await response.json();
    mapApiDataToForm(data.data);

  } catch (error) {
    console.error('Error fetching product:', error);
  } finally {
    loading.value = false;
  }
};

const mapApiDataToForm = (apiData) => {
  product.value = {
    ...product.value,
    id: apiData.product.id,
    name: apiData.product.name,
    gender: apiData.product.gender,
    description: apiData.product.description,
    purchasePrice: parseFloat(apiData.product.purchase_price),
    price: parseFloat(apiData.product.price),
    category_id: apiData.product.category_id,
    subcategory_ids: apiData.subcategories.map(sc => sc.id),
    brand: apiData.product.brand || "",
    sku: apiData.product.sku,
    stock: apiData.product.stock,
    status: apiData.product.status,
    mainImageUrl: apiData.product.main_image_url,
    sizeGuideUrl: apiData.product.size_guide_url,
    sizeGuideName: apiData.product.size_guide_name,
    variants: apiData.variants.map(variant => ({
      id: variant.id,
      color: variant.color,
      color_name: variant.color_name,
      sizes: variant.sizes.map(size => size.id),
      existingImages: variant.images.map(img => ({
        id: img.id,
        url: img.url,
        markedForDeletion: false
      })),
      newImages: []
    }))
  };

  // Set first variant as active if exists
  if (product.value.variants.length > 0) {
    activeVariant.value = product.value.variants[0].color;
  }

  console.log('product', product.value);
};

const filterSubCategory = () => {
  product.value.subcategory_ids = [];
};

const filteredSubCategories = computed(() => {
  if (!product.value.category_id) return [];
  return subCategories.value.filter(sub =>
    sub.category_ids && sub.category_ids.includes(product.value.category_id)
  );
});

const selectedColors = computed(() =>
  product.value.variants.map((v) => v.color)
);

const getColorName = (hex) => {
  return availableColors.value.find((c) => c.hex === hex)?.name || hex;
};

const isColorSelected = (hex) => {
  return selectedColors.value.includes(hex);
};

const toggleColor = (color) => {
  const index = product.value.variants.findIndex((v) => v.color === color.hex);

  if (index === -1) {
    product.value.variants.push({
      color: color.hex,
      color_name: color.name,
      sizes: [],
      existingImages: [],
      newImages: [],
    });

    if (product.value.variants.length === 1) {
      activeVariant.value = color.hex;
    }
  } else {
    product.value.variants.splice(index, 1);
    if (activeVariant.value === color.hex) {
      activeVariant.value = product.value.variants[0]?.color || null;
    }
  }
};

const setActiveVariant = (color) => {
  activeVariant.value = color;
};

const getActiveVariant = () => {
  return product.value.variants.find((v) => v.color === activeVariant.value);
};

const isSizeSelected = (color, size) => {
  const variant = product.value.variants.find(v => v.color === color);
  if (!variant) return false;
  return variant.sizes.includes(size.id);
};

const toggleSize = (color, size) => {
  const variant = product.value.variants.find((v) => v.color === color);
  if (!variant) return;

  const index = variant.sizes.indexOf(size.id);
  if (index === -1) {
    variant.sizes.push(size.id);
  } else {
    variant.sizes.splice(index, 1);
  }
};

const triggerFileUpload = () => {
  fileInput.value.click();
};

const handleFileUpload = async (e) => {
  const files = e.target.files;
  if (!files || !activeVariant.value) return;

  const variant = getActiveVariant();
  if (!variant) return;

  for (const file of files) {
    const reader = new FileReader();
    reader.onload = (e) => {
      variant.newImages.push({
        file,
        preview: e.target.result,
      });
    };
    reader.readAsDataURL(file);
  }

  e.target.value = "";
};

const markImageForDeletion = (variant, index) => {
  variant.existingImages[index].markedForDeletion = !variant.existingImages[index].markedForDeletion;
};

const removeNewImage = (color, index) => {
  const variant = product.value.variants.find((v) => v.color === color);
  if (variant) {
    variant.newImages.splice(index, 1);
  }
};

const removeVariant = (color) => {
  const index = product.value.variants.findIndex((v) => v.color === color);
  if (index !== -1) {
    product.value.variants.splice(index, 1);
    if (activeVariant.value === color) {
      activeVariant.value = product.value.variants[0]?.color || null;
    }
  }
};

const triggerMainImageUpload = () => {
  mainImageInput.value.click();
};

const handleMainImageUpload = (e) => {
  const file = e.target.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = (e) => {
    product.value.mainImageFile = {
      file,
      preview: e.target.result,
    };
  };
  reader.readAsDataURL(file);

  e.target.value = "";
};

const removeMainImage = () => {
  if (product.value.mainImageFile) {
    product.value.mainImageFile = null;
  } else {
    product.value.mainImageUrl = "";
  }
};

const triggerSizeGuideUpload = () => {
  sizeGuideInput.value.click();
};

const handleSizeGuideUpload = (e) => {
  const file = e.target.files[0];
  if (!file) return;

  product.value.sizeGuideFile = file;
  e.target.value = "";
};

const removeSizeGuide = () => {
  if (product.value.sizeGuideFile) {
    product.value.sizeGuideFile = null;
  } else {
    product.value.sizeGuideUrl = "";
    product.value.sizeGuideName = "";
  }
};

const updateProduct = async () => {
  if (!product.value.name || !product.value.category_id || !product.value.price) {
    alert("Please fill in all required fields");
    return;
  }

  const formData = new FormData();
  formData.append('_method', 'PUT');

  // Basic product info
  formData.append('name', product.value.name);
  formData.append('description', product.value.description);
  formData.append('price', product.value.price);
  formData.append('purchase_price', product.value.purchasePrice);
  formData.append('sku', product.value.sku);
  formData.append('stock', product.value.stock);
  formData.append('gender', product.value.gender);
  formData.append('status', product.value.status);
  formData.append('category_id', product.value.category_id);
  formData.append('brand', product.value.brand);

  // Main image
  if (product.value.mainImageFile) {
    formData.append('main_image', product.value.mainImageFile.file);
  }

  // Size guide
  if (product.value.sizeGuideFile) {
    formData.append('size_guide', product.value.sizeGuideFile);
  }

  // Subcategories
  product.value.subcategory_ids.forEach((id, index) => {
    formData.append(`subcategory_ids[${index}]`, id);
  });

  // Variants
  product.value.variants.forEach((variant, i) => {
    formData.append(`variants[${i}][id]`, variant.id || '');
    formData.append(`variants[${i}][color]`, variant.color);
    formData.append(`variants[${i}][color_name]`, variant.color_name || getColorName(variant.color));

    // Sizes
    variant.sizes.forEach((sizeId, j) => {
      formData.append(`variants[${i}][sizes][${j}]`, sizeId);
    });

    // Images marked for deletion
    variant.existingImages
      .filter(img => img.markedForDeletion)
      .forEach((img, k) => {
        formData.append(`variants[${i}][deleted_images][${k}]`, img.id);
      });

    // New images
    variant.newImages.forEach((img, k) => {
      formData.append(`variants[${i}][new_images][${k}]`, img.file);
    });
  });

  try {
    saving.value = true;
    const response = await fetch(`${API.BACKEND_URL}/product/${product.value.id}/update`, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
      body: formData,
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || 'Failed to update product');
    }
    toast.success('Product updated successfully!');
    setTimeout(() => {
      router.push("/admin/products");
    }, 500);
  } catch (error) {
    console.error('Error updating product:', error);
    toast.error(error.message || 'An error occurred while updating the product');
  } finally {
    loading.value = false;
  }
};



const goBack = () => {
  router.go(-1);
};
</script>

<style scoped>
@import "@/assets/css/custom.css";

/* Color Management Styles */
.color-management {
  margin-top: 1rem;
}

.color-selection {
  margin-bottom: 2rem;
}

.color-palette {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
  margin-top: 0.5rem;
}

.color-option {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid transparent;
  transition: all 0.2s ease;
}

.color-option.selected {
  border-color: #333;
  box-shadow: 0 0 0 2px white, 0 0 0 4px #333;
}

.checkmark {
  color: white;
  text-shadow: 0 0 3px rgba(0, 0, 0, 0.5);
  font-size: 1.2rem;
}

/* Variant Tabs */
.variant-tabs {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
  margin-bottom: 1rem;
}

.variant-tab {
  padding: 0.5rem 1rem;
  border: 1px solid #ddd;
  border-radius: 20px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: white;
  transition: all 0.2s ease;
}

.variant-tab.active {
  border-color: #333;
  background: #333;
  color: white;
}

.color-swatch {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  border: 1px solid #ddd;
}

.remove-color {
  margin-left: 0.5rem;
  font-size: 1.2rem;
  padding: 0 0.25rem;
}

/* Size Selection */
.size-options {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
  margin-top: 0.5rem;
}

.size-option {
  padding: 0.5rem 1rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  cursor: pointer;
  background: white;
}

.size-option.selected {
  background: #333;
  color: white;
  border-color: #333;
}

/* Image Upload */
.image-uploader,
.file-uploader {
  margin-top: 1rem;
}

.upload-area {
  border: 2px dashed #ddd;
  border-radius: 8px;
  padding: 2rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s ease;
  position: relative;
}

.upload-area:hover {
  border-color: #333;
  background: #f8f8f8;
}

.main-image-preview {
  max-width: 100%;
  max-height: 200px;
  border-radius: 4px;
}

.remove-main-image,
.remove-size-guide {
  margin-top: 0.5rem;
  padding: 0.5rem 1rem;
  background: #ff4444;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.remove-main-image:hover,
.remove-size-guide:hover {
  background: #cc0000;
}

.image-previews {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
  gap: 1rem;
  margin-top: 1rem;
}

.image-preview {
  position: relative;
  aspect-ratio: 1/1;
}

.image-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 4px;
}

.remove-image {
  position: absolute;
  top: -8px;
  right: -8px;
  background: red;
  color: white;
  border: none;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  padding: 0;
}

.deleted-badge {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: rgba(255, 0, 0, 0.7);
  color: white;
  padding: 2px 5px;
  border-radius: 3px;
  font-size: 12px;
}

/* Quill Editor Styles */
:deep(.ql-toolbar) {
  border-radius: 8px 8px 0 0;
  border: 1px solid #ddd !important;
}

:deep(.ql-container) {
  border-radius: 0 0 8px 8px;
  border: 1px solid #ddd !important;
  height: 200px;
}

:deep(.ql-editor) {
  font-size: 14px;
}

.loading-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 200px;
  font-size: 1.2rem;
  color: #666;
}
</style>
