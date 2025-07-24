<template>
  <div class="add-product-container">
    <!-- Header Section -->
    <div class="header-section">
      <h1>Add New Product</h1>
      <div class="action-buttons">
        <button class="btn btn-outline-secondary" @click="goBack">
          <i class="fas fa-arrow-left"></i> Back
        </button>
      </div>
    </div>

    <!-- Main Form -->
    <div class="form-container">
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
                    :class="{ 'has-image': product.mainImage }"
                  >
                    <template v-if="!product.mainImage">
                      <i class="fas fa-cloud-upload-alt fa-3x"></i>
                      <p>Click to upload main product image</p>
                      <small class="text-muted">Recommended size: 580×630px</small>
                    </template>
                    <template v-else>
                      <img
                        :src="product.mainImage.preview"
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
                    v-if="product.mainImage"
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
                    :class="{ 'has-file': product.sizeGuide }"
                  >
                    <template v-if="!product.sizeGuide">
                      <i class="fas fa-file-pdf fa-3x"></i>
                      <p>Click to upload size guide (PDF)</p>
                      <small class="text-muted">Max file size: 5MB</small>
                    </template>
                    <template v-else>
                      <i class="fas fa-file-pdf fa-3x"></i>
                      <p class="file-name">{{ product.sizeGuide.name }}</p>
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
                    v-if="product.sizeGuide"
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
                  {{ getColorName(variant.color) }}
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
                <h5 class="mb-3">Available Sizes for {{ getColorName(activeVariant) }}</h5>
                <div class="table-responsive">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>Size</th>
                        <th>Price (£)</th>
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
                              :value="getSizePrice(activeVariant, size)"
                              @input="updateSizePrice(activeVariant, size, $event)"
                              placeholder="0.00"
                              min="0"
                              step="0.01"
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
                <h5 class="mb-3">Images for {{ getColorName(activeVariant) }}</h5>
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
                    <div
                      v-for="(image, index) in getActiveVariant().images"
                      :key="index"
                      class="image-preview"
                    >
                      <img :src="image.preview" :alt="`Preview ${index + 1}`" />
                      <button
                        class="btn btn-danger btn-sm remove-image"
                        @click="removeImage(activeVariant, index)"
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
            <button class="btn btn-primary" @click="saveProduct">
              <i class="fas fa-save"></i> Save Product
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import { API } from '@/utils/config';
import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import Select from "@/components/Select.vue";
import MultiSelect from "@/components/MultiSelect.vue";

const router = useRouter();
const fileInput = ref(null);
const mainImageInput = ref(null);
const sizeGuideInput = ref(null);
const loading = ref(false);
const categories = ref([]);
const subCategories = ref([]);
const brands = ref([]);
const sizes = ref([]);
const availableColors = ref([]);
const token = localStorage.getItem('auth_token');
const errors = ref([])

onMounted(async () => {
  await fetchData();
});

const fetchData = async () => {
  try {
    loading.value = true;
    const token = localStorage.getItem('auth_token');
    if (!token) {
      router.push('/login');
    }

    const response = await fetch(`${API.BACKEND_URL}/product-fetch-data`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
    });

    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.message || 'Failed to fetch categories');
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
  } finally {
    loading.value = false;
  }
};

const genders = [
  { id: '1', slug: 'men', title: 'Men' },
  { id: '2', slug: 'women', title: 'Women' },
  { id: '3', slug: 'unisex', title: 'Unisex' },
  { id: '4', slug: 'kids', title: 'Kids' },
]

// Editor configuration
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

// Reactive State
const product = ref({
  name: "",
  gender: "",
  description: "",
  purchasePrice: 0,
  price: 0,
  category: "",
  subCategory: "",
  brand: "",
  sku: "",
  stock: 0,
  mainImage: null,
  sizeGuide: null,
  variants: [],
  category_id: null,
  category_slug: "",
  subcategory_ids: [],
  subcategory_slugs: [],
});

const activeVariant = ref(null);

const filterSubCategory = () => {
  product.value.subcategory_ids = [];
};

// Computed property to filter subcategories based on selected category
const filteredSubCategories = computed(() => {
  if (!product.value.category_id) return [];
  const filtered = subCategories.value.filter(sub =>
    sub.category_ids && sub.category_ids.includes(product.value.category_id)
  );
  return filtered;
});

// Computed
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
    // Add new variant
    product.value.variants.push({
      color: color.hex,
      sizes: [],
      images: [],
    });
    // Set as active if first variant
    if (product.value.variants.length === 1) {
      activeVariant.value = color.hex;
    }
  } else {
    // Remove variant
    product.value.variants.splice(index, 1);
    // Update active variant if needed
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
  const variant = product.value.variants.find((v) => v.color === color);
  if (!variant || !variant.sizes) return false;

  return variant.sizes.some(s => s.id === size.id);
};

// Update toggleSize to initialize with price
const toggleSize = (color, size) => {
  const variant = product.value.variants.find((v) => v.color === color);
  console.log('variant', variant);
  if (!variant) return;

  const sizeIndex = variant.sizes.findIndex(s => s.id === size.id);

  if (sizeIndex === -1) {
    // Add new size with base price
    variant.sizes.push({
      id: size.id,
      size_title: size.size_title,
      price: product.value.price // Initialize with base product price
    });
  } else {
    // Remove size
    variant.sizes.splice(sizeIndex, 1);
  }
};

// Helper function to get size price
const getSizePrice = (color, size) => {
  const variant = product.value.variants.find((v) => v.color === color);
  if (!variant) return 0;

  const sizeObj = variant.sizes.find(s => s.id === size.id);
  return sizeObj?.price || product.value.price;
};

// Update size price
const updateSizePrice = (color, size, event) => {
  const variant = product.value.variants.find((v) => v.color === color);
  if (!variant) return;

  const sizeObj = variant.sizes.find(s => s.id === size.id);
  if (sizeObj) {
    sizeObj.price = parseFloat(event.target.value) || product.value.price;
  }
}

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
      variant.images.push({
        file,
        preview: e.target.result,
      });
    };
    reader.readAsDataURL(file);
  }

  // Reset file input
  e.target.value = "";
};

const removeImage = (color, index) => {
  const variant = product.value.variants.find((v) => v.color === color);
  if (variant) {
    variant.images.splice(index, 1);
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

// Main image handling
const triggerMainImageUpload = () => {
  mainImageInput.value.click();
};

const handleMainImageUpload = (e) => {
  const file = e.target.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = (e) => {
    product.value.mainImage = {
      file,
      preview: e.target.result,
    };
  };
  reader.readAsDataURL(file);

  // Reset file input
  e.target.value = "";
};

const removeMainImage = () => {
  product.value.mainImage = null;
};

// Size guide handling
const triggerSizeGuideUpload = () => {
  sizeGuideInput.value.click();
};

const handleSizeGuideUpload = (e) => {
  const file = e.target.files[0];
  if (!file) return;

  product.value.sizeGuide = file;

  // Reset file input
  e.target.value = "";
};

const removeSizeGuide = () => {
  product.value.sizeGuide = null;
};

const saveProduct = () => {
  if (!product.value.name || !product.value.category_id || !product.value.price) {
    alert("Please fill in all required fields");
    return;
  }

  const formData = new FormData();

  // Product Fields (match database)
  formData.append('name', product.value.name);
  formData.append('gender', product.value.gender);
  formData.append('description', product.value.description);
  formData.append('purchase_price', product.value.purchasePrice);
  formData.append('price', product.value.price);
  formData.append('category_id', product.value.category_id);
  formData.append('category_slug', categories.value.find(c => c.id === product.value.category_id)?.slug || '');
  formData.append('sku', product.value.sku);
  formData.append('stock', product.value.stock);
  formData.append('brand', product.value.brand);

  // Main Image
  const mainFile = product.value.mainImage instanceof File
    ? product.value.mainImage
    : product.value.mainImage?.file;

  if (mainFile instanceof File) {
    formData.append('main_image', mainFile);
  } else {
    console.warn("⚠️ mainImage is not a File", product.value.mainImage);
  }


  // Size Guide
  if (product.value.sizeGuide instanceof File) {
    formData.append('size_guide', product.value.sizeGuide);
  }

  // Subcategories
  product.value.subcategory_ids.forEach((id, index) => {
    formData.append(`subcategory_ids[${index}]`, id);
  });

// Variants with size prices - restructure the data
  product.value.variants.forEach((variant, i) => {
    formData.append(`variants[${i}][color]`, variant.color);
    formData.append(`variants[${i}][color_name]`, getColorName(variant.color));

    // Restructure sizes array to be simple IDs for validation
    variant.sizes.forEach((size, j) => {
      formData.append(`variants[${i}][sizes][]`, size.id);
      // Add prices separately
      formData.append(`variants[${i}][size_prices][${size.id}]`, size.price);
    });

    // Images
    variant.images.forEach((img, k) => {
      const file = img instanceof File ? img : img.file;
      if (file instanceof File) {
        formData.append(`variants[${i}][images][${k}]`, file);
      }
    });
  });


  // ✅ Proper FormData log
  console.log('--- FormData Contents ---');
  for (let [key, value] of formData.entries()) {
    console.log(key, value);
  }

  save(formData);
};



const save = async (submissionData) => {
  try {
    const response = await fetch(`${API.BACKEND_URL}/product/add`, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
      body: submissionData,

    })

    const data = await response.json()

    if (!response.ok) {
      if (response.status === 422) {
        errors.value = data.errors || {}
      }
      throw new Error(data.message || 'Failed to save')
    }

    toast.success('Category created successfully!')
    setTimeout(() => {
      router.push("/admin/products");
    }, 500)
  } catch (error) {
    if (!errors.value.title) {
      toast.error(error.message || 'An error occurred while creating the role');
    }
  } finally {
    loading.value = false
  }
}

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
</style>