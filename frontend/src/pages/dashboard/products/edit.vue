<template>
  <div class="add-product-container">
    <!-- Header Section -->
    <div class="header-section">
      <h1>Edit Product</h1>
      <div class="action-buttons">
        <button class="btn btn-outline-secondary" @click="goBack">
          <i class="fas fa-arrow-left"></i> Back
        </button>
      </div>
    </div>

    <!-- Main Form -->
    <div class="form-container" v-if="!loading">
      <div class="card">
        <!-- Basic Information Section -->
        <div class="card-body">
          <h2 class="section-title">Basic Information</h2>
          <div class="row g-3">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Gender</label>
                <Select
                  placeholder="Select gender"
                  v-model="product.gender"
                  :options="genders"
                  option-label="title"
                  option-value="slug"
                />
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Category</label>
                <Select
                  v-model="product.category_id"
                  placeholder="Select Category"
                  :options="categories"
                  option-label="title"
                  option-value="id"
                  @update:modelValue="filterSubCategory"
                />
              </div>
            </div>
            
            <div class="col-12">
              <div class="form-group">
                <label class="form-label">Title</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="product.name"
                  placeholder="Enter product name"
                />
              </div>
            </div>
            
            <div class="col-12">
              <div class="form-group">
                <label class="form-label">Description</label>
                <QuillEditor
                  v-model:content="product.description"
                  contentType="html"
                  theme="snow"
                  :options="editorOptions"
                  placeholder="Enter product description"
                />
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Purchase Price</label>
                <div class="input-group">
                  <input
                    type="number"
                    class="form-control"
                    v-model.number="product.purchasePrice"
                    placeholder="0.00"
                    min="0"
                    step="0.01"
                  />
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Selling Price</label>
                <div class="input-group">
                  <input
                    type="number"
                    class="form-control"
                    v-model.number="product.price"
                    placeholder="0.00"
                    min="0"
                    step="0.01"
                  />
                </div>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Sub Categories</label>
                <MultiSelect
                  v-model="product.subcategory_ids"
                  :options="filteredSubCategories"
                  placeholder="Select subcategories"
                  option-label="title"
                  option-value="id"
                />
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Brand</label>
                <Select
                  v-model="product.brand"
                  placeholder="Select brand"
                  :options="brands"
                  option-label="title"
                  option-value="slug"
                />
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">SKU</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="product.sku"
                  placeholder="Product SKU"
                />
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label">Stock Quantity</label>
                <input
                  type="number"
                  class="form-control"
                  v-model.number="product.stock"
                  placeholder="Total stock"
                  min="0"
                />
              </div>
            </div>
            
            <div class="col-12">
              <div class="form-group">
                <label class="form-label">Main Product Image</label>
                <div class="image-uploader">
                  <div 
                    class="upload-area" 
                    @click="triggerMainImageUpload"
                    :class="{ 'has-image': product.mainImageUrl || product.mainImageFile }"
                  >
                    <template v-if="!product.mainImageFile && !product.mainImageUrl">
                      <i class="fas fa-cloud-upload-alt fa-3x"></i>
                      <p>Click to upload main product image</p>
                      <small class="text-muted">Recommended size: 580×630px</small>
                    </template>
                    <template v-else>
                      <img
                        :src="product.mainImageFile ? product.mainImageFile.preview : product.mainImageUrl"
                        alt="Main product image preview"
                        class="main-image-preview"
                      />
                    </template>
                    <input
                      type="file"
                      ref="mainImageInput"
                      @change="handleMainImageUpload"
                      accept="image/*"
                      style="display: none"
                    />
                  </div>
                  <button
                    v-if="product.mainImageUrl || product.mainImageFile"
                    class="btn btn-danger btn-sm mt-2"
                    @click="removeMainImage"
                  >
                    <i class="fas fa-trash"></i> Remove Image
                  </button>
                </div>
              </div>
            </div>
            
            <div class="col-12">
              <div class="form-group">
                <label class="form-label">Size Guide (PDF)</label>
                <div class="file-uploader">
                  <div 
                    class="upload-area" 
                    @click="triggerSizeGuideUpload"
                    :class="{ 'has-file': product.sizeGuideUrl || product.sizeGuideFile }"
                  >
                    <template v-if="!product.sizeGuideFile && !product.sizeGuideUrl">
                      <i class="fas fa-file-pdf fa-3x"></i>
                      <p>Click to upload size guide (PDF)</p>
                      <small class="text-muted">Max file size: 5MB</small>
                    </template>
                    <template v-else>
                      <i class="fas fa-file-pdf fa-3x"></i>
                      <p class="file-name">{{ product.sizeGuideFile ? product.sizeGuideFile.name : product.sizeGuideName }}</p>
                    </template>
                    <input
                      type="file"
                      ref="sizeGuideInput"
                      @change="handleSizeGuideUpload"
                      accept=".pdf"
                      style="display: none"
                    />
                  </div>
                  <button
                    v-if="product.sizeGuideUrl || product.sizeGuideFile"
                    class="btn btn-danger btn-sm mt-2"
                    @click="removeSizeGuide"
                  >
                    <i class="fas fa-trash"></i> Remove File
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Color Variants Section -->
        <div class="card-body">
          <h2 class="section-title">Color Variants</h2>
          <div class="color-management">
            <!-- Color Selection -->
            <div class="color-selection mb-4">
              <label class="form-label">Select Colors:</label>
              <div class="color-palette">
                <div
                  v-for="color in availableColors"
                  :key="color.hex"
                  class="color-option"
                  :class="{ selected: isColorSelected(color.hex) }"
                  :style="{ backgroundColor: color.hex }"
                  @click="toggleColor(color)"
                  :title="color.name"
                >
                  <span v-if="isColorSelected(color.hex)" class="checkmark">
                    <i class="fas fa-check"></i>
                  </span>
                </div>
              </div>
            </div>

            <!-- Variant Details Tabs -->
            <div class="variant-tabs mb-3" v-if="product.variants.length > 0">
              <div class="nav nav-pills">
                <button
                  v-for="variant in product.variants"
                  :key="variant.color"
                  class="nav-link"
                  :class="{ active: activeVariant === variant.color }"
                  @click="setActiveVariant(variant.color)"
                >
                  <span
                    class="color-swatch"
                    :style="{ backgroundColor: variant.color }"
                  ></span>
                  {{ variant.color_name || getColorName(variant.color) }}
                  <button
                    class="btn-close ms-2"
                    style="filter: invert(0); opacity: 0.7;"
                    @click.stop="removeVariant(variant.color)"
                    aria-label="Remove color"
                  ></button>
                </button>
              </div>
            </div>

            <!-- Active Variant Details -->
            <div class="variant-details" v-if="activeVariant">
              <!-- Size Selection -->
              <div class="size-selection mb-4">
                <h5 class="mb-3">Available Sizes for {{ getActiveVariant().color_name || getColorName(activeVariant) }}</h5>
                <div class="table-responsive">
<table class="table table-bordered">
  <thead>
    <tr>
      <th>Size</th>
      <th>Price (£)</th>
      <th>Stock</th> <!-- Add Stock column -->
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <tr v-for="size in sizes" :key="size.id">
      <td>
        <div class="form-check">
          <input
            class="form-check-input"
            type="checkbox"
            :id="`size-${size.id}`"
            :checked="isSizeSelected(activeVariant, size)"
            @change="toggleSize(activeVariant, size)"
          />
          <label class="form-check-label" :for="`size-${size.id}`">
            {{ size.size_title }}
          </label>
        </div>
      </td>
      <td>
        <div class="input-group" v-if="isSizeSelected(activeVariant, size)">
          <input
            type="number"
            class="form-control"
            v-model.number="getSizeObject(activeVariant, size).price"
            placeholder="0.00"
            min="0"
            step="0.01"
          />
        </div>
        <span v-else class="text-muted">-</span>
      </td>
      <td>
        <div class="input-group" v-if="isSizeSelected(activeVariant, size)">
          <input
            type="number"
            class="form-control"
            v-model.number="getSizeObject(activeVariant, size).stock"
            placeholder="0"
            min="0"
          />
        </div>
        <span v-else class="text-muted">-</span>
      </td>
      <td>
        <button
          class="btn btn-sm"
          :class="{
            'btn-outline-danger': isSizeSelected(activeVariant, size),
            'btn-outline-secondary': !isSizeSelected(activeVariant, size)
          }"
          @click="toggleSize(activeVariant, size)"
        >
          {{ isSizeSelected(activeVariant, size) ? 'Remove' : 'Add' }}
        </button>
      </td>
    </tr>
  </tbody>
</table>
                </div>
              </div>

              <!-- Image Upload -->
              <div class="image-upload-section">
                <h5 class="mb-3">Images for {{ getActiveVariant().color_name || getColorName(activeVariant) }}</h5>
                <div class="image-uploader">
                  <div class="upload-area" @click="triggerFileUpload">
                    <i class="fas fa-images fa-3x"></i>
                    <p>Click to upload images</p>
                    <small class="text-muted">Recommended size: 580×630px</small>
                    <input
                      type="file"
                      ref="fileInput"
                      @change="handleFileUpload"
                      multiple
                      accept="image/*"
                      style="display: none"
                    />
                  </div>
                  
                  <div class="image-previews mt-3">
                    <!-- Existing images -->
                    <div
                      v-for="(image, index) in getActiveVariant().existingImages"
                      :key="'existing-' + index"
                      class="image-preview"
                    >
                      <img :src="image.url" :alt="`Preview ${index + 1}`" />
                      <button
                        class="btn btn-danger btn-sm remove-image"
                        @click="markImageForDeletion(getActiveVariant(), index)"
                      >
                        <i class="fas fa-times"></i>
                      </button>
                      <span v-if="image.markedForDeletion" class="deleted-badge">Deleted</span>
                    </div>
                    
                    <!-- New images -->
                    <div
                      v-for="(image, index) in getActiveVariant().newImages"
                      :key="'new-' + index"
                      class="image-preview"
                    >
                      <img :src="image.preview" :alt="`Preview ${index + 1}`" />
                      <button
                        class="btn btn-danger btn-sm remove-image"
                        @click="removeNewImage(activeVariant, index)"
                      >
                        <i class="fas fa-times"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="card-footer bg-transparent">
          <div class="d-flex justify-content-between">
            <button class="btn btn-outline-secondary" @click="goBack">
              <i class="fas fa-times"></i> Cancel
            </button>
            <button class="btn btn-primary" @click="updateProduct" :disabled="saving">
              <i class="fas fa-save"></i> {{ saving ? 'Saving...' : 'Update Product' }}
            </button>
          </div>
        </div>
      </div>
    </div>
    <div v-else class="loading">
      <div class="spinner" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-2">Loading product data...</p>
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
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

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
    toast.error('Failed to load product data');
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
    toast.error('Failed to load product');
    router.push('/admin/products');
  } finally {
    loading.value = false;
  }
};

const mapApiDataToForm = (apiData) => {
  console.log(' apiData.variants', apiData.variants);
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
    mainImageUrl: apiData.product.main_image_url,
    sizeGuideUrl: apiData.product.size_guide_url,
    sizeGuideName: apiData.product.size_guide_name,
    variants: apiData.variants.map(variant => ({
      id: variant.id,
      color: variant.color,
      color_name: variant.color_name,
      sizes: variant.sizes.map(size => ({
        id: size.id,
        size_title: size.size_title,
        price: parseFloat(size.price ?? 0),  // ✅ Convert string price to float
        stock: parseInt(size.stock ?? 0)
      })),
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
  return variant.sizes.some(s => s.id === size.id);
};

const toggleSize = (color, size) => {
  const variant = product.value.variants.find((v) => v.color === color);
  if (!variant) return;

  const index = variant.sizes.findIndex(s => s.id === size.id);
  if (index === -1) {
    // Add new size with default price and stock
    variant.sizes.push({
      id: size.id,
      size_title: size.size_title,
      price: product.value.price, // Default to product base price
      stock: 0 // Initialize with 0 stock
    });
  } else {
    // Remove size
    variant.sizes.splice(index, 1);
  }
};

const getSizeObject = (variantColor, size) => {
  const variant = product.value.variants.find(v => v.color === variantColor);
  return variant?.sizes.find(s => s.id === size.id) || { price: 0 };
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
    toast.error("Please fill in all required fields");
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

    // Sizes with prices
    variant.sizes.forEach((size, j) => {
      formData.append(`variants[${i}][sizes][${j}][id]`, size.id);
      formData.append(`variants[${i}][sizes][${j}][price]`, size.price);
      formData.append(`variants[${i}][sizes][${j}][stock]`, size.stock || 0); // Add stock
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
    }, 1500);
  } catch (error) {
    console.error('Error updating product:', error);
    toast.error(error.message || 'An error occurred while updating the product');
  } finally {
    saving.value = false;
  }
};

const goBack = () => {
  router.go(-1);
};
</script>

<style scoped>
@import "@/assets/css/custom.css";

.add-product-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.header-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.section-title {
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 20px;
  padding-bottom: 10px;
  border-bottom: 1px solid #eee;
}

.card {
  border: none;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
  margin-bottom: 30px;
  border-radius: 10px;
  overflow: hidden;
}

.card-body {
  padding: 30px;
}

/* Form Styles */
.form-label {
  font-weight: 500;
  margin-bottom: 8px;
}

.form-control {
  border-radius: 8px;
  padding: 10px 15px;
}

/* Upload Areas */
.upload-area {
  border: 2px dashed #ddd;
  border-radius: 10px;
  padding: 30px;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s ease;
  background-color: #f8f9fa;
}

.upload-area:hover {
  border-color: #0d6efd;
  background-color: #f0f7ff;
}

.upload-area.has-image,
.upload-area.has-file {
  padding: 20px;
}

.upload-area i {
  color: #6c757d;
  margin-bottom: 15px;
}

.upload-area p {
  margin-bottom: 5px;
  font-weight: 500;
}

.main-image-preview {
  max-width: 100%;
  max-height: 200px;
  border-radius: 8px;
  object-fit: contain;
}

.file-name {
  font-weight: 500;
  word-break: break-all;
}

/* Color Management */
.color-palette {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  margin-top: 10px;
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
  position: relative;
}

.color-option.selected {
  border-color: #212529;
  box-shadow: 0 0 0 2px white, 0 0 0 4px #212529;
}

.color-option:hover {
  transform: scale(1.1);
}

.checkmark {
  color: white;
  font-size: 14px;
  text-shadow: 0 0 3px rgba(0, 0, 0, 0.5);
}

/* Variant Tabs */
.variant-tabs .nav-pills {
  gap: 8px;
}

.variant-tabs .nav-link {
  display: flex;
  align-items: center;
  padding: 8px 15px;
  border-radius: 20px;
  background-color: #f8f9fa;
  color: #212529;
  border: 1px solid #dee2e6;
}

.variant-tabs .nav-link.active {
  background-color: #4487ed;
  color: white;
  border-color: #0d6efd;
}

.color-swatch {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  border: 1px solid rgba(255, 255, 255, 0.3);
  margin-right: 8px;
}

/* Close button styles */
.btn-close {
  opacity: 0.5;
  transition: opacity 0.2s ease;
  font-size: 12px;
  padding: 0.5em;
}

.btn-close:hover {
  opacity: 1;
}

.btn-close-black {
  filter: invert(1);
}

.nav-link.active .btn-close-black {
  filter: none;
}

/* Size Selection */
.size-selection table {
  margin-bottom: 0;
}

.size-selection th {
  font-weight: 500;
  background-color: #f8f9fa;
}

.size-selection td {
  vertical-align: middle;
}

/* Image Previews */
.image-previews {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  gap: 15px;
}

.image-preview {
  position: relative;
  aspect-ratio: 1/1;
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid #dee2e6;
}

.image-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.remove-image {
  position: absolute;
  top: 5px;
  right: 5px;
  width: 25px;
  height: 25px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  opacity: 0.8;
}

.remove-image:hover {
  opacity: 1;
}

/* Quill Editor Styles */
:deep(.ql-toolbar) {
  border-radius: 8px 8px 0 0 !important;
  border: 1px solid #dee2e6 !important;
}

:deep(.ql-container) {
  border-radius: 0 0 8px 8px !important;
  border: 1px solid #dee2e6 !important;
  height: 200px;
}

:deep(.ql-editor) {
  font-size: 14px;
  min-height: 150px;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
  .header-section {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }
  
  .card-body {
    padding: 20px;
  }
  
  .image-previews {
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
  }
}

.loading-container {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  height: 200px;
  font-size: 1.2rem;
  color: #666;
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

.size-price-input {
  margin-top: 8px;
}

.size-price-input label {
  display: block;
  margin-bottom: 4px;
  font-size: 12px;
  color: #666;
}

.size-price-input input {
  width: 100%;
  padding: 6px;
  border: 1px solid #ddd;
  border-radius: 4px;
}
</style>