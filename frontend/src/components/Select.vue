<template>
    <div class="custom-select" :class="{ 'is-open': isOpen }">
      <div class="select-header" @click="toggleDropdown">
        <span class="selected-value">
          {{ selectedOption ? getOptionLabel(selectedOption) : placeholder }}
        </span>
        <span class="arrow">▼</span>
      </div>
      <div class="select-options" v-show="isOpen">
        <input
          type="text"
          v-model="searchQuery"
          placeholder="Search..."
          class="search-input"
        />
        <div
          v-for="option in filteredOptions"
          :key="getOptionValue(option)"
          class="select-option"
          :class="{ 'is-selected': isOptionSelected(option) }"
          @click="selectOption(option)"
        >
          {{ getOptionLabel(option) }}
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, watch, onMounted, onUnmounted, computed } from 'vue'
  
  const props = defineProps({
    modelValue: {
      type: [String, Number, Object],
      default: null
    },
    options: {
      type: Array,
      required: true
    },
    placeholder: {
      type: String,
      default: 'Select an option'
    },
    optionLabel: {
      type: String,
      default: 'name'
    },
    optionValue: {
      type: String,
      default: 'id'
    }
  })
  
  const emit = defineEmits(['update:modelValue'])
  
  const isOpen = ref(false)
  const selectedOption = ref(null)
  const searchQuery = ref('')
  
  // Function definitions
  const getOptionLabel = (option) => {
    return typeof option === 'object' ? option[props.optionLabel] : option
  }
  
  const getOptionValue = (option) => {
    return typeof option === 'object' ? option[props.optionValue] : option
  }
  
  const isOptionSelected = (option) => {
    return getOptionValue(option) === getOptionValue(selectedOption.value)
  }
  
  // Watch for changes in modelValue
  watch(() => props.modelValue, (newVal) => {
    selectedOption.value = props.options.find(option => 
      getOptionValue(option) === newVal
    )
  }, { immediate: true })
  
  const toggleDropdown = () => {
    isOpen.value = !isOpen.value
  }
  
  const selectOption = (option) => {
    selectedOption.value = option
    emit('update:modelValue', getOptionValue(option))
    isOpen.value = false
  }
  
  const handleClickOutside = (event) => {
    if (!event.target.closest('.custom-select')) {
      isOpen.value = false
    }
  }
  
  onMounted(() => {
    document.addEventListener('click', handleClickOutside)
  })
  
  onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
  })
  
  // Computed property for filtered options
  const filteredOptions = computed(() => {
    if (!searchQuery.value) {
      return props.options
    }
    const query = searchQuery.value.toLowerCase()
    return props.options.filter(option =>
      getOptionLabel(option).toLowerCase().includes(query)
    )
  })
  </script>
  
  <style scoped>
  .custom-select {
    position: relative;
    width: 100%;
    font-size: 14px;
  }
  
  .select-header {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 8px 12px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: white;
  }
  
  .selected-value {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  
  .arrow {
    margin-left: 8px;
    transition: transform 0.2s;
  }
  
  .is-open .arrow {
    transform: rotate(180deg);
  }
  
  .select-options {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: #f9f9f9; /* Dropdown background color */
    z-index: 10;
    margin-top: 4px;
  }
  
  .search-input {
    width: 100%;
    padding: 8px;
    border: none;
    border-bottom: 1px solid #ddd;
    outline: none;
  }
  
  .select-option {
    padding: 8px 12px;
    cursor: pointer;
    transition: background-color 0.3s;
  }
  
  .select-option:hover {
    background-color: #f5f5f5; /* Hover effect for options */
  }
  
  .select-option.is-selected {
    background-color: #4CAF50; /* Selected option background color */
    color: white; /* Text color for contrast */
    font-weight: 500;
  }
  </style>
  