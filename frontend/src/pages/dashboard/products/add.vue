<template>
  <div class="add-product-container">
    <!-- Header Section -->
    <div class="header-section-add">
      <h1>Add New Product</h1>
      <div class="action-buttons">
        <button class="cancel-button" @click="goBack">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="16"
            height="16"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
          </svg>
          Back
        </button>
      </div>
    </div>

    <!-- Main Form -->
    <div class="form-container">
      <div class="form-card">
        <!-- Basic Information Section -->
        <div class="form-section">
          <h2>Basic Information</h2>
          <div class="form-row">
            <div class="form-group">
              <label>Gender</label>
              <Select
              placeholder="Select gender"
                v-model="product.gender"
                :options="genders"
                option-label="title"
                option-value="slug"
              />
            </div>
            <div class="form-group">
              <label>Category</label>
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
          <div class="form-group full-width-field">
            <label>Title</label>
            <input
              type="text"
              v-model="product.name"
              placeholder="Enter product name"
            />
          </div>
          <div class="form-group full-width-field">
            <label>Description</label>
            <QuillEditor
              v-model:content="product.description"
              contentType="html"
              theme="snow"
              :options="editorOptions"
              placeholder="Enter product description"
            />
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Purchase Price (£)</label>
              <input
                type="number"
                v-model.number="product.purchasePrice"
                placeholder="0.00"
                min="0"
                step="0.01"
              />
            </div>
            <div class="form-group">
              <label>Selling Price (£)</label>
              <input
                type="number"
                v-model.number="product.price"
                placeholder="0.00"
                min="0"
                step="0.01"
              />
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Sub Categories</label>
              <MultiSelect
                v-model="product.subcategory_ids"
                :options="filteredSubCategories"
                placeholder="Select subcategories"
                option-label="title"
                option-value="id"
              />
            </div>
            <div class="form-group">
              <label>Brand</label>
              <Select
                v-model="product.brand"
                placeholder="Select brand"
                :options="brands"
                option-label="title"
                option-value="slug"
              />
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>SKU</label>
              <input
                type="text"
                v-model="product.sku"
                placeholder="Product SKU"
              />
            </div>
            <div class="form-group">
              <label>Stock Quantity</label>
              <input
                type="number"
                v-model.number="product.stock"
                placeholder="Total stock"
                min="0"
              />
            </div>
          </div>

          <!-- Main Product Image -->
          <div class="form-group full-width-field">
            <label>Main Product Image</label>
            <div class="image-uploader">
              <div class="upload-area" @click="triggerMainImageUpload">
                <template v-if="!product.mainImage">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path
                      d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7"
                    ></path>
                    <polyline points="16 5 22 5 22 11"></polyline>
                    <line x1="16" y1="5" x2="22" y2="11"></line>
                  </svg>
                  <p>Click to upload main product image</p>
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
                class="remove-main-image"
                @click="removeMainImage"
              >
                Remove Image
              </button>
            </div>
          </div>

          <!-- Size Guide Upload -->
          <div class="form-group full-width-field">
            <label>Size Guide (PDF)</label>
            <div class="file-uploader">
              <div class="upload-area" @click="triggerSizeGuideUpload">
                <template v-if="!product.sizeGuide">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path
                      d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                    ></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                  </svg>
                  <p>Click to upload size guide (PDF)</p>
                </template>
                <template v-else>
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path
                      d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                    ></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                  </svg>
                  <p>{{ product.sizeGuide.name }}</p>
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
                class="remove-size-guide"
                @click="removeSizeGuide"
              >
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
                <div
                  v-for="color in availableColors"
                  :key="color.hex"
                  class="color-option"
                  :class="{ selected: isColorSelected(color.hex) }"
                  :style="{ backgroundColor: color.hex }"
                  @click="toggleColor(color)"
                >
                  <span v-if="isColorSelected(color.hex)" class="checkmark"
                    >✓</span
                  >
                </div>
              </div>
            </div>

            <!-- Variant Details Tabs -->
            <div class="variant-tabs" v-if="product.variants.length > 0">
              <button
                v-for="variant in product.variants"
                :key="variant.color"
                class="variant-tab"
                :class="{ active: activeVariant === variant.color }"
                @click="setActiveVariant(variant.color)"
              >
                <span
                  class="color-swatch"
                  :style="{ backgroundColor: variant.color }"
                ></span>
                {{ getColorName(variant.color) }}
                <span
                  class="remove-color"
                  @click.stop="removeVariant(variant.color)"
                  >×</span
                >
              </button>
            </div>

            <!-- Active Variant Details -->
            <div class="variant-details" v-if="activeVariant">
              <!-- Size Selection -->
              <div class="size-selection">
                <label
                  >Available Sizes for {{ getColorName(activeVariant) }}:</label
                >
                <div class="size-options">
                  <button
                    v-for="size in sizes"
                    :key="size"
                    class="size-option"
                    :class="{ selected: isSizeSelected(activeVariant, size) }"
                    @click="toggleSize(activeVariant, size)"
                  >
                    {{ size.size_title }}
                  </button>
                </div>
              </div>

              <!-- Image Upload -->
              <div class="image-upload-section">
                <label>Images for {{ getColorName(activeVariant) }}:</label>
                <div class="image-uploader">
                  <div class="upload-area" @click="triggerFileUpload">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    >
                      <path
                        d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7"
                      ></path>
                      <polyline points="16 5 22 5 22 11"></polyline>
                      <line x1="16" y1="5" x2="22" y2="11"></line>
                    </svg>
                    <p>Click to upload images</p>
                    <input
                      type="file"
                      ref="fileInput"
                      @change="handleFileUpload"
                      multiple
                      accept="image/*"
                      style="display: none"
                    />
                  </div>
                  <div class="image-previews">
                    <div
                      v-for="(image, index) in getActiveVariant().images"
                      :key="index"
                      class="image-preview"
                    >
                      <img :src="image.preview" :alt="`Preview ${index + 1}`" />
                      <button
                        class="remove-image"
                        @click="removeImage(activeVariant, index)"
                      >
                        ×
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
          <button class="cancel-button" @click="goBack">Cancel</button>
          <button class="save-button" @click="saveProduct">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="16"
              height="16"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path
                d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"
              ></path>
              <polyline points="17 21 17 13 7 13 7 21"></polyline>
              <polyline points="7 3 7 8 15 8"></polyline>
            </svg>
            Save
          </button>
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

onMounted( async() => {
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
        subCategories.value =data.subCategory;
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

const genders =[
    { id: '1', slug:'men', title: 'Men' },
    { id: '2',slug:'women', title: 'Women' },
    { id: '3',slug:'unisex', title: 'Unisex' },
    { id: '4',slug:'kids', title: 'Kids' },
]
// const availableColors = [
//   { hex: "#FF0000", name: "Red" },
//   { hex: "#00FF00", name: "Green" },
//   { hex: "#0000FF", name: "Blue" },
//   { hex: "#FFFF00", name: "Yellow" },
//   { hex: "#FF00FF", name: "Magenta" },
//   { hex: "#00FFFF", name: "Cyan" },
//   { hex: "#000000", name: "Black" },
//   { hex: "#FFFFFF", name: "White" },
//   { hex: "#808080", name: "Gray" },
//   { hex: "#FFA500", name: "Orange" },
// ];

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

// const updateCategory = (categoryId) => {
//   const category = categories.find((c) => c.id === categoryId);
//   product.value.category_id = category?.id || null;
//   product.value.category_slug = category?.slug || "";
//   product.value.subcategory_ids = [];
//   product.value.subcategory_slugs = [];
// };

// const updateSubCategories = (selectedIds) => {
//   product.value.subcategory_ids = selectedIds;
// };

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
  return variant?.sizes.includes(size) || false;
};

const toggleSize = (color, size) => {
  const variant = product.value.variants.find((v) => v.color === color);
  if (!variant) return;

  const index = variant.sizes.indexOf(size);
  if (index === -1) {
    variant.sizes.push(size);
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

  // Variants
  product.value.variants.forEach((variant, i) => {
    formData.append(`variants[${i}][color]`, variant.color);
    formData.append(`variants[${i}][color_name]`, getColorName(variant.color));

    variant.sizes.forEach((size, j) => {
      formData.append(`variants[${i}][sizes][${j}]`, size.id);
    });

variant.images.forEach((img, k) => {
  const file = img instanceof File ? img : img.file; // if wrapped in a .file key
  if (file instanceof File) {
    formData.append(`variants[${i}][images][${k}]`, file);
  } else {
    console.warn('Skipping non-file image:', img);
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



const save = async (submissionData) =>
{
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
</style>