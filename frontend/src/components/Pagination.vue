<!-- components/Pagination.vue -->
<template>
  <div class="pagination" v-if="meta && meta.last_page > 1">
    <button @click="prevPage" :disabled="meta.current_page === 1">Previous</button>
    <span v-for="page in visiblePages" :key="page" 
          @click="goToPage(page)" 
          :class="{ active: meta.current_page === page }">
      {{ page }}
    </span>
    <button @click="nextPage" :disabled="meta.current_page === meta.last_page">Next</button>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  meta: Object
});

const emit = defineEmits(['page-changed']);

const visiblePages = computed(() => {
  if (!props.meta) return [];
  
  const current = props.meta.current_page;
  const last = props.meta.last_page;
  const delta = 2;
  const range = [];
  
  for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
    range.push(i);
  }
  
  if (current - delta > 2) range.unshift('...');
  if (current + delta < last - 1) range.push('...');
  
  range.unshift(1);
  if (last !== 1) range.push(last);
  
  return range;
});

const prevPage = () => {
  if (props.meta.current_page > 1) {
    emit('page-changed', props.meta.current_page - 1);
  }
};

const nextPage = () => {
  if (props.meta.current_page < props.meta.last_page) {
    emit('page-changed', props.meta.current_page + 1);
  }
};

const goToPage = (page) => {
  if (page !== '...' && page !== props.meta.current_page) {
    emit('page-changed', page);
  }
};
</script>

<style scoped>
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  margin-top: 20px;
}

.pagination button {
  padding: 5px 10px;
  border: 1px solid #ddd;
  background: #fff;
  cursor: pointer;
  border-radius: 4px;
}

.pagination button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.pagination span {
  padding: 5px 10px;
  border: 1px solid #ddd;
  cursor: pointer;
  border-radius: 4px;
}

.pagination span.active {
  background: #007bff;
  color: white;
  border-color: #007bff;
}

.pagination span:hover:not(.active) {
  background: #f0f0f0;
}
</style>