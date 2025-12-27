<!-- resources/js/components/layout/SettingsLoader.vue -->
<template>
  <div v-if="settingsStore.isLoading" class="settings-loader">
    <div class="loader-spinner"></div>
    <p>Loading settings...</p>
  </div>
  <slot v-else-if="!settingsStore.error"></slot>
  <div v-else class="settings-error">
    <p>⚠️ Failed to load settings. Please refresh the page.</p>
    <button @click="retry" class="btn btn-sm btn-outline">Retry</button>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useSettingsStore } from '@/store/settings'

const settingsStore = useSettingsStore()

const retry = async () => {
  await settingsStore.fetchSettings()
}

onMounted(async () => {
  if (!settingsStore.isLoaded && !settingsStore.isLoading) {
    await settingsStore.fetchSettings()
  }
})
</script>

<style scoped>
.settings-loader {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  padding: 2rem;
}

.loader-spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #f3f3f3;
  border-top: 3px solid #E31B23;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.settings-error {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  padding: 2rem;
  text-align: center;
  color: #dc2626;
}

.settings-error p {
  margin-bottom: 1rem;
}
</style>