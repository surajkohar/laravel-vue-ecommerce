<template>
  <div class="pagination">
    <div class="pagination-container">
      <!-- Previous Button -->
      <button 
        class="pagination-btn pagination-prev"
        :disabled="currentPage === 1" 
        @click="changePage(currentPage - 1)"
      >
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="pagination-icon">
          <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Previous
      </button>

      <!-- Page Numbers -->
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

      <!-- Next Button -->
      <button 
        class="pagination-btn pagination-next"
        :disabled="currentPage === totalPages" 
        @click="changePage(currentPage + 1)"
      >
        Next
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="pagination-icon">
          <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
    </div>

    <!-- Results Info -->
    <div class="pagination-info">
      Showing page {{ currentPage }} of {{ totalPages }} • {{ totalItems }} items
    </div>
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
  totalItems: {
    type: Number,
    default: 0
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

  // Always show first page
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

  // Always show last page
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
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  margin: 3rem 0 2rem;
  padding: 2rem 0;
  border-top: 1px solid var(--border-color);
}

.pagination-container {
  display: flex;
  align-items: center;
  gap: 1rem;
}

/* Pagination Buttons */
.pagination-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 10px 20px;
  border: 2px solid var(--border-color);
  background: var(--primary-white);
  color: var(--primary-black);
  cursor: pointer;
  border-radius: 6px;
  font-weight: 600;
  font-size: 0.9rem;
  transition: all 0.3s ease;
  min-width: 120px;
  justify-content: center;
}

.pagination-btn:hover:not(:disabled) {
  border-color: var(--primary-red);
  color: var(--primary-red);
  background: var(--primary-white);
}

.pagination-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
  color: var(--gray-medium);
  border-color: var(--border-color);
}

.pagination-prev {
  order: 1;
}

.pagination-next {
  order: 3;
}

.pagination-icon {
  transition: transform 0.3s ease;
}

.pagination-prev:hover:not(:disabled) .pagination-icon {
  transform: translateX(-2px);
}

.pagination-next:hover:not(:disabled) .pagination-icon {
  transform: translateX(2px);
}

/* Page Numbers */
.page-numbers {
  display: flex;
  gap: 0.5rem;
  order: 2;
}

.page-number {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 8px 12px;
  border: 2px solid var(--border-color);
  background: var(--primary-white);
  color: var(--primary-black);
  cursor: pointer;
  border-radius: 6px;
  min-width: 42px;
  height: 42px;
  font-weight: 600;
  font-size: 0.9rem;
  transition: all 0.3s ease;
}

.page-number:hover:not(.ellipsis):not(.active) {
  border-color: var(--primary-red);
  color: var(--primary-red);
  background: var(--primary-white);
}

.page-number.active {
  background: var(--primary-red);
  color: var(--primary-white);
  border-color: var(--primary-red);
}

.page-number.ellipsis {
  cursor: default;
  background: transparent;
  border: none;
  color: var(--gray-medium);
  min-width: 20px;
}

.page-number.ellipsis:hover {
  background: transparent;
  color: var(--gray-medium);
}

/* Pagination Info */
.pagination-info {
  color: var(--gray-medium);
  font-size: 0.9rem;
  font-weight: 500;
  text-align: center;
  padding: 0.5rem 0;
}

/* Responsive Design */
@media (max-width: 768px) {
  .pagination {
    margin: 2rem 0 1rem;
    padding: 1.5rem 0;
    gap: 0.75rem;
  }

  .pagination-container {
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.75rem;
  }

  .pagination-btn {
    min-width: 110px;
    padding: 8px 16px;
    font-size: 0.85rem;
    order: 0;
  }

  .pagination-prev,
  .pagination-next {
    order: 0;
  }

  .page-numbers {
    order: 0;
    width: 100%;
    justify-content: center;
  }

  .page-number {
    min-width: 38px;
    height: 38px;
    padding: 6px 10px;
    font-size: 0.85rem;
  }
}

@media (max-width: 640px) {
  .pagination-container {
    flex-direction: column;
    width: 100%;
  }

  .pagination-btn {
    width: 100%;
    max-width: 200px;
  }

  .page-numbers {
    gap: 0.25rem;
  }

  .page-number {
    min-width: 36px;
    height: 36px;
    padding: 6px 8px;
    font-size: 0.8rem;
  }

  .pagination-info {
    font-size: 0.85rem;
  }
}

@media (max-width: 480px) {
  .pagination {
    margin: 1.5rem 0 1rem;
    padding: 1rem 0;
  }

  .page-numbers {
    flex-wrap: wrap;
  }

  .page-number {
    min-width: 32px;
    height: 32px;
    padding: 4px 6px;
    font-size: 0.8rem;
  }

  .pagination-btn {
    min-width: 100px;
    padding: 6px 12px;
    font-size: 0.8rem;
  }
}

/* Animation for active state */
.page-number.active {
  position: relative;
  overflow: hidden;
}

.page-number.active::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.6s ease;
}

.page-number.active:hover::before {
  left: 100%;
}

/* Focus states for accessibility */
.pagination-btn:focus-visible,
.page-number:focus-visible {
  outline: 2px solid var(--primary-red);
  outline-offset: 2px;
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  .pagination-btn,
  .page-number,
  .pagination-icon {
    transition: none;
  }
  
  .page-number.active::before {
    display: none;
  }
}
</style>