<template>
    <div class="multi-select" :class="{ 'is-open': isOpen }">
      <div class="select-header" @click="toggleDropdown">
        <div class="selected-values">
          <span v-if="selectedOptions.length === 0" class="">
            {{ placeholder }}
          </span>
          <span
            v-for="option in selectedOptions"
            :key="getOptionValue(option)"
            class="selected-tag"
          >
            {{ getOptionLabel(option) }}
            <span class="remove-tag" @click.stop="removeOption(option)">×</span>
          </span>
        </div>
        <span class="arrow">
                  <svg class="arrow-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
  <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.06l3.71-3.83a.75.75 0 111.08 1.04l-4.25 4.4a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
</svg>
        </span>
      </div>
      <div class="select-options" v-show="isOpen">
        <input
          type="text"
          v-model="searchQuery"
          placeholder="Search..."
          class="search-input"
        />
        <div
        class="select-option"
        @click="clearSelection"
      >
        Select
      </div>
        <div
          v-for="option in filteredOptions"
          :key="getOptionValue(option)"
          class="select-option"
          :class="{ 'is-selected': isOptionSelected(option) }"
          @click="toggleOption(option)"
        >
          {{ getOptionLabel(option) }}
        </div>
      </div>
    </div>
  </template>
  
 <script setup>
import { ref, watch, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  },
  options: {
    type: Array,
    required: true
  },
  placeholder: {
    type: String,
    default: 'Select options'
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
const selectedOptions = ref([])
const searchQuery = ref('')

//  MOVE THESE FUNCTIONS BEFORE USING THEM BELOW
const getOptionLabel = (option) => {
  return typeof option === 'object' ? option[props.optionLabel] : option
}

const getOptionValue = (option) => {
  return typeof option === 'object' ? option[props.optionValue] : option
}

//  NOW SAFE TO USE getOptionValue() IN WATCH
watch(() => props.modelValue, (newVal) => {
  if (!Array.isArray(props.options)) {
    selectedOptions.value = []
    return
  }

  selectedOptions.value = props.options.filter(option =>
    newVal.includes(getOptionValue(option))
  )
}, { immediate: true })

const isOptionSelected = (option) => {
  return selectedOptions.value.some(
    selected => getOptionValue(selected) === getOptionValue(option)
  )
}

const clearSelection = () => {
  emit('update:modelValue', [])
}

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
}

const toggleOption = (option) => {
  const optionValue = getOptionValue(option)
  const currentValue = props.modelValue
  const newValue = currentValue.includes(optionValue)
    ? currentValue.filter(val => val !== optionValue)
    : [...currentValue, optionValue]

  emit('update:modelValue', newValue)
}

const removeOption = (option) => {
  const optionValue = getOptionValue(option)
  const newValue = props.modelValue.filter(val => val !== optionValue)
  emit('update:modelValue', newValue)
}

const handleClickOutside = (event) => {
  if (!event.target.closest('.multi-select')) {
    isOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

const filteredOptions = computed(() => {
  if (!searchQuery.value) return props.options

  const query = searchQuery.value.toLowerCase()
  return props.options.filter(option =>
    getOptionLabel(option).toLowerCase().includes(query)
  )
})
</script>

  
  <style scoped>
  .multi-select {
    position: relative;
    width: 100%;
    font-size: 14px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: white;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  }
  
  .select-header {
    padding: 8px 12px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    min-height: 38px;
  }
  
  .selected-values {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    flex-grow: 1;
  }
  
  .placeholder {
    color: #999;
  }
  
  .selected-tag {
    background-color: #4CAF50; /* Selected tag background color */
    color: white; /* Text color for contrast */
    border-radius: 4px;
    padding: 4px 8px;
    display: flex;
    align-items: center;
    gap: 4px;
    transition: background-color 0.3s;
  }
  
  .remove-tag {
    cursor: pointer;
    font-size: 12px;
    line-height: 1;
  }
  
  .remove-tag:hover {
    color: #ff4444;
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
    max-height: 250px;
    overflow-y: auto;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: #f9f9f9; /* Dropdown background color */
    z-index: 10;
    margin-top: 4px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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
    background-color: #e0e0e0; /* Hover effect for options */
  }
  
  .select-option.is-selected {
    background-color: #e9e9e9;
    font-weight: 500;
  }
  </style>
  