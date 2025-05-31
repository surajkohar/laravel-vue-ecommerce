<template>
  <div v-if="visible" class="dialog-overlay" @click.self="onCancel">
    <div class="dialog">
      <div class="dialog-header">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="dialog-icon">
          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm0-4h-2V7h2v8z"/>
        </svg>
        <h3>{{ title }}</h3>
      </div>
      <div class="dialog-content">
        <p>{{ message }}</p>
      </div>
      <div class="dialog-actions">
        <button class="btn-cancel" @click="onCancel">Cancel</button>
        <button class="btn-confirm" @click="onConfirm">Confirm</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const visible = ref(false);
const title = ref('');
const message = ref('');
let resolveFn = null;

const show = ({ title: t, message: m }) => {
  title.value = t || 'Confirm';
  message.value = m || 'Are you sure?';
  visible.value = true;

  return new Promise((resolve) => {
    resolveFn = resolve;
  });
};

const onConfirm = () => {
  visible.value = false;
  resolveFn?.(true);
};

const onCancel = () => {
  visible.value = false;
  resolveFn?.(false);
};

defineExpose({ show });
</script>

<style scoped>
.dialog-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  backdrop-filter: blur(0.1px);
  animation: fadeIn 0.2s ease-out;
}

.dialog {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 380px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  animation: slideUp 0.3s ease-out;
  transform-origin: center bottom;
}

.dialog-header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 20px 24px 0;
}

.dialog-icon {
  width: 24px;
  height: 24px;
  flex-shrink: 0;
  fill: #FF6B6B;
}

.dialog h3 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
  color: #2D3748;
}

.dialog-content {
  padding: 16px 24px;
}

.dialog p {
  margin: 0;
  font-size: 0.95rem;
  line-height: 1.5;
  color: #4A5568;
}

.dialog-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 16px 24px;
  border-top: 1px solid #EDF2F7;
}

button {
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
  outline: none;
}

.btn-cancel {
  background-color: #d8dde4;
  color: #4A5568;
}

.btn-cancel:hover {
  background-color: #E2E8F0;
}

.btn-confirm {
  background-color: #4caf50;
  color: white;
}

.btn-confirm:hover {
  background-color: #388e3c;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideUp {
  from { 
    transform: translateY(10px) scale(0.98);
    opacity: 0.8;
  }
  to { 
    transform: translateY(0) scale(1);
    opacity: 1;
  }
}
</style>