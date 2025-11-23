<template>
  <div class="pagination">
    <button 
      class="pagination-btn prev"
      :disabled="currentPage === 1" 
      @click="changePage(currentPage - 1)"
    >
      ← Previous
    </button>
    
    <div class="page-numbers">
      <span 
        v-for="page in visiblePages" 
        :key="page"
        :class="['page-number', { 
          active: page === currentPage,
          ellipsis: page === '...'
        }]"
        @click="page !== '...' && changePage(page)"
      >
        {{ page }}
      </span>
    </div>
    
    <button 
      class="pagination-btn next"
      :disabled="currentPage === totalPages" 
      @click="changePage(currentPage + 1)"
    >
      Next →
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  currentPage: {
    type: Number,
    required: true,
    default: 1
  },
  totalPages: {
    type: Number,
    required: true,
    default: 1
  },
  maxVisiblePages: {
    type: Number,
    default: 5
  }
})

const emit = defineEmits(['page-change'])

const visiblePages = computed(() => {
  const pages = []
  const half = Math.floor(props.maxVisiblePages / 2)
  let start = Math.max(props.currentPage - half, 1)
  let end = Math.min(start + props.maxVisiblePages - 1, props.totalPages)

  if (end - start < props.maxVisiblePages - 1) {
    start = Math.max(end - props.maxVisiblePages + 1, 1)
  }

  // Add first page and ellipsis if needed
  if (start > 1) {
    pages.push(1)
    if (start > 2) {
      pages.push('...')
    }
  }

  // Add page numbers
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }

  // Add last page and ellipsis if needed
  if (end < props.totalPages) {
    if (end < props.totalPages - 1) {
      pages.push('...')
    }
    pages.push(props.totalPages)
  }

  return pages
})

const changePage = (page) => {
  if (page >= 1 && page <= props.totalPages && page !== props.currentPage) {
    emit('page-change', page)
  }
}
</script>

<style scoped>
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin: 3rem 0;
  flex-wrap: wrap;
}

.pagination-btn {
  padding: 10px 16px;
  border: 1px solid #dee2e6;
  background: white;
  color: #667eea;
  cursor: pointer;
  border-radius: 6px;
  font-weight: 500;
  transition: all 0.3s ease;
  min-width: 100px;
}

.pagination-btn:hover:not(:disabled) {
  background: #667eea;
  color: white;
  border-color: #667eea;
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  color: #6c757d;
}

.page-numbers {
  display: flex;
  gap: 0.5rem;
}

.page-number {
  padding: 8px 12px;
  border: 1px solid #dee2e6;
  background: white;
  cursor: pointer;
  border-radius: 6px;
  min-width: 40px;
  text-align: center;
  transition: all 0.3s ease;
  font-weight: 500;
}

.page-number:hover:not(.ellipsis) {
  background: #f8f9fa;
  border-color: #667eea;
}

.page-number.active {
  background: #667eea;
  color: white;
  border-color: #667eea;
}

.page-number.ellipsis {
  cursor: default;
  background: transparent;
  border: none;
}

.page-number.ellipsis:hover {
  background: transparent;
}

/* Responsive */
@media (max-width: 768px) {
  .pagination {
    gap: 0.5rem;
  }
  
  .pagination-btn {
    min-width: 80px;
    padding: 8px 12px;
    font-size: 0.9rem;
  }
  
  .page-number {
    padding: 6px 10px;
    min-width: 35px;
    font-size: 0.9rem;
  }
}

@media (max-width: 480px) {
  .page-numbers {
    gap: 0.25rem;
  }
  
  .page-number {
    padding: 6px 8px;
    min-width: 30px;
    font-size: 0.8rem;
  }
  
  .pagination-btn {
    min-width: 70px;
    padding: 6px 10px;
    font-size: 0.8rem;
  }
}
</style>