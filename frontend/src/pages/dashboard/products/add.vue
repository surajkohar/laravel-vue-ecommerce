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
                option-label="name"
                option-value="id"
                @update:modelValue="updateCategory"
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
                option-label="name"
                option-value="slug"
                @update:modelValue="updateSubCategories"
              />
            </div>
            <div class="form-group">
              <label>Brand</label>
              <Select
                v-model="product.brand"
                placeholder="Select brand"
                :options="brands"
                option-label="name"
                option-value="slug"
                @update:modelValue="updateCategory"
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
                    v-for="size in availableSizes"
                    :key="size"
                    class="size-option"
                    :class="{ selected: isSizeSelected(activeVariant, size) }"
                    @click="toggleSize(activeVariant, size)"
                  >
                    {{ size }}
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
import { ref, computed } from "vue";
import { useRouter } from "vue-router";
import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import Select from "@/components/Select.vue";
import MultiSelect from "@/components/MultiSelect.vue";

const router = useRouter();
const fileInput = ref(null);
const mainImageInput = ref(null);
const sizeGuideInput = ref(null);

// Categories and subcategories structure
const categories = [
  { id: 1, slug: "t-shirt", name: "T Shirt" },
  { id: 2, slug: "hoodies", name: "Hoodies" },
  { id: 3, slug: "bottom", name: "Bottom" },
  { id: 4, slug: "headwear", name: "Headwear" },
  { id: 5, slug: "sweatshirts", name: "Sweatshirts" },
  { id: 6, slug: "jacket", name: "Jacket" },
  { id: 7, slug: "hi-vis", name: "Hi Vis" },
];

const subCategories = [
  { id: 1, slug: 'plain', name: 'Plain', category_id: 1, category_slug: 't-shirt' },
  { id: 2, slug: 'full-sleeve', name: 'Full Sleeve', category_id: 1, category_slug: 't-shirt' },
  { id: 3, slug: 'short-sleeve', name: 'Short Sleeve', category_id: 1, category_slug: 't-shirt' },
  { id: 4, slug: 'v-neck', name: 'V Neck', category_id: 1, category_slug: 't-shirt' },
  { id: 5, slug: 'round-neck', name: 'Round Neck', category_id: 1, category_slug: 't-shirt' },
  { id: 6, slug: 'vest', name: 'Vest', category_id: 7, category_slug: 'hi-vis' },
  { id: 7, slug: 'cap', name: 'Cap', category_id: 4, category_slug: 'headwear' },
  { id: 8, slug: 'shorts', name: 'Shorts', category_id: 3, category_slug: 'bottom' },
  { id: 9, slug: 'trouser', name: 'Trouser', category_id: 3, category_slug: 'bottom' },
  { id: 10, slug: 'zip-up', name: 'Zip Up', category_id: 2, category_slug: 'hoodies' },
  { id: 11, slug: 'pullover', name: 'Pullover', category_id: 2, category_slug: 'hoodies' },
  { id: 12, slug: 'sleeveless', name: 'Sleeveless', category_id: 2, category_slug: 'hoodies' },
  { id: 13, slug: 'joggers', name: 'Joggers', category_id: 3, category_slug: 'bottom' },
  { id: 14, slug: 'jeans', name: 'Jeans', category_id: 3, category_slug: 'bottom' },
  { id: 15, slug: 'beanie', name: 'Beanie', category_id: 4, category_slug: 'headwear' },
  { id: 16, slug: 'headband', name: 'Headband', category_id: 4, category_slug: 'headwear' },
  { id: 17, slug: 'crewneck', name: 'Crewneck', category_id: 5, category_slug: 'sweatshirts' },
  { id: 18, slug: 'hooded', name: 'Hooded', category_id: 5, category_slug: 'sweatshirts' },
  { id: 19, slug: 'zip', name: 'Zip', category_id: 5, category_slug: 'sweatshirts' },
  { id: 20, slug: 'bomber', name: 'Bomber', category_id: 6, category_slug: 'jacket' },
  { id: 21, slug: 'denim', name: 'Denim', category_id: 6, category_slug: 'jacket' },
  { id: 22, slug: 'parka', name: 'Parka', category_id: 6, category_slug: 'jacket' },
  { id: 23, slug: 'windbreaker', name: 'Windbreaker', category_id: 6, category_slug: 'jacket' }
]

const brands = [
  {id:1, slug: 'awdis', name: 'Awdis' },
  {id:2, slug: 'uneek', name: 'Uneek' },
  {id:3, slug: 'beechfield', name: 'Beechfield' }
]

const availableSizes = ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL', '6XL']
const genders =[
    { id: '1', slug:'men', title: 'Men' },
    { id: '2',slug:'women', title: 'Women' },
    { id: '3',slug:'unisex', title: 'Unisex' },
    { id: '4',slug:'kids', title: 'Kids' },
]
const availableColors = [
  { hex: "#FF0000", name: "Red" },
  { hex: "#00FF00", name: "Green" },
  { hex: "#0000FF", name: "Blue" },
  { hex: "#FFFF00", name: "Yellow" },
  { hex: "#FF00FF", name: "Magenta" },
  { hex: "#00FFFF", name: "Cyan" },
  { hex: "#000000", name: "Black" },
  { hex: "#FFFFFF", name: "White" },
  { hex: "#808080", name: "Gray" },
  { hex: "#FFA500", name: "Orange" },
];

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
const filteredSubCategories = computed(() => {
  if (!product.value.category_id) return [];
  return subCategories.filter(
    (sub) => sub.category_id === product.value.category_id
  );
});
// Computed
const selectedColors = computed(() =>
  product.value.variants.map((v) => v.color)
);
// Methods

const updateCategory = (categoryId) => {
  const category = categories.find((c) => c.id === categoryId);
  product.value.category_id = category?.id || null;
  product.value.category_slug = category?.slug || "";
  product.value.subcategory_ids = [];
  product.value.subcategory_slugs = [];
};

const updateSubCategories = (selectedIds) => {
  product.value.subcategory_ids = selectedIds;
  product.value.subcategory_slugs = selectedIds
    .map((id) => {
      const sub = subCategories.find((s) => s.id === id);
      return sub?.slug || "";
    })
    .filter((slug) => slug);
};

const getColorName = (hex) => {
  return availableColors.find((c) => c.hex === hex)?.name || hex;
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
  // Validate required fields
  if (!product.value.name || !product.value.category_id || !product.value.price) {
    alert("Please fill in all required fields");
    return;
  }

  // Prepare data for submission
  const submissionData = {
    name: product.value.name,
    gender: product.value.gender,
    description: product.value.description,
    purchasePrice: product.value.purchasePrice,
    price: product.value.price,
    category_id:product.value.category_id,
    subcategory_ids:product.value.subcategory_ids,
    category:
      categories.find((c) => c.id === product.value.category)?.name ||
      product.value.category,
    subCategory:
      subCategories.find((sc) => sc.id === product.value.subCategory)?.name ||
      product.value.subCategory,
    brand:
      brands.find((b) => b.id === product.value.brand)?.name ||
      product.value.brand,
    sku: product.value.sku,
    stock: product.value.stock,
    mainImage: product.value.mainImage
      ? {
          name: product.value.mainImage.file.name,
          type: product.value.mainImage.file.type,
          size: product.value.mainImage.file.size,
          preview: product.value.mainImage.preview,
        }
      : null,
    sizeGuide: product.value.sizeGuide
      ? {
          name: product.value.sizeGuide.name,
          type: product.value.sizeGuide.type,
          size: product.value.sizeGuide.size,
        }
      : null,
    variants: product.value.variants.map((variant) => ({
      color: variant.color,
      colorName: getColorName(variant.color),
      sizes: variant.sizes,
      images: variant.images.map((img) => ({
        name: img.file.name,
        type: img.file.type,
        size: img.file.size,
        preview: img.preview,
      })),
    })),
    createdAt: new Date().toISOString(),
  };

  // Log the submission data to console
  console.log(
    "Product Submission Data:",
    JSON.parse(JSON.stringify(submissionData))
  );

  return;

  // In a real app, you would upload files to a server here
  // and then submit the product data with the resulting URLs

  // For demo purposes, we'll just navigate back
  router.push("/admin/products");
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