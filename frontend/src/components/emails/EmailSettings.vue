<template>
  <div class="email-settings">
    <div v-if="loading" class="loading">Loading templates...</div>
    <div v-else>
      <WelcomeTemplate 
        v-if="activeTab === 'welcome'"
        :template="templates.welcome"
      />
      
      <PasswordUpdateTemplate
        v-if="activeTab === 'password_update'"
        :template="templates.password_update"
      />
      
      <div class="tab-controls">
        <button
          @click="activeTab = 'welcome'"
          :class="{ active: activeTab === 'welcome' }"
        >
          Welcome Email
        </button>
        <button
          @click="activeTab = 'password_update'"
          :class="{ active: activeTab === 'password_update' }"
        >
          Password Update
        </button>
      </div>
      
      <div class="actions">
        <button @click="saveTemplates" :disabled="loading">
          {{ loading ? 'Saving...' : 'Save Templates' }}
        </button>
        <button @click="resetTemplates" class="outline">
          Reset to Defaults
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useEmailStore } from '@/stores/emailStore'
import WelcomeTemplate from './templates/WelcomeTemplate.vue'
import PasswordUpdateTemplate from './templates/PasswordUpdateTemplate.vue'

const emailStore = useEmailStore()
const activeTab = ref('welcome')
const loading = ref(false)

const { templates } = emailStore

onMounted(async () => {
  loading.value = true
  await emailStore.fetchTemplates()
  loading.value = false
})

const saveTemplates = async () => {
  try {
    loading.value = true
    await emailStore.saveTemplates()
    alert('Templates saved successfully!')
  } catch (error) {
    alert('Failed to save templates: ' + error.message)
  } finally {
    loading.value = false
  }
}

const resetTemplates = () => {
  if (confirm('Reset to default templates?')) {
    emailStore.fetchTemplates()
  }
}
</script>

<style scoped>
.email-settings {
  max-width: 800px;
  margin: 0 auto;
  padding: 1rem;
}

.loading {
  text-align: center;
  padding: 2rem;
}

.tab-controls {
  display: flex;
  gap: 0.5rem;
  margin: 1rem 0;
}

.tab-controls button {
  padding: 0.5rem 1rem;
  background: #f1f5f9;
  border: none;
  border-radius: 0.25rem;
  cursor: pointer;
}

.tab-controls button.active {
  background: #10b759;
  color: white;
}

.actions {
  display: flex;
  gap: 0.5rem;
  margin-top: 1rem;
}

.actions button {
  padding: 0.5rem 1rem;
  background: #10b759;
  color: white;
  border: none;
  border-radius: 0.25rem;
  cursor: pointer;
}

.actions button.outline {
  background: transparent;
  border: 1px solid #10b759;
  color: #10b759;
}

.actions button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}
</style>