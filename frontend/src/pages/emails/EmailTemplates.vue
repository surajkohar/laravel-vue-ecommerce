<template>
  <div class="email-templates-admin">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-left">
        <h1>Email Templates</h1>
        <p>Manage system email templates and notifications</p>
      </div>
      <div class="header-actions">
        <button class="btn btn-primary" @click="saveTemplates" :disabled="loading">
          <i class="fas fa-save"></i> Save Changes
        </button>
      </div>
    </div>

    <!-- Main Content -->
    <div class="template-container">
      <!-- Template Tabs -->
      <div class="template-tabs">
        <button 
          v-for="tab in tabs" 
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="{ active: activeTab === tab.id }"
        >
          {{ tab.label }}
        </button>
      </div>

      <!-- Template Content -->
      <div class="template-content">
        <div v-if="loading" class="loading-overlay">
          <div class="spinner"></div>
        </div>

        <template v-if="!loading">
          <WelcomeTemplate 
            v-show="activeTab === 'welcome'"
            :template="templates.welcome"
            @update="updateWelcomeTemplate"
          />
          
          <PasswordUpdateTemplate
            v-show="activeTab === 'password_update'"
            :template="templates.password_update"
            @update="updatePasswordTemplate"
          />
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useEmailStore } from '@/store/emailStore'
import WelcomeTemplate from '@/components/emails/templates/WelcomeTemplate.vue'
import PasswordUpdateTemplate from '@/components/emails/templates/PasswordUpdateTemplate.vue'

const router = useRouter()
const emailStore = useEmailStore()

const tabs = [
  { id: 'welcome', label: 'Welcome Email' },
  { id: 'password_update', label: 'Password Update' }
]

const activeTab = ref('welcome')
const loading = ref(false)
const token = localStorage.getItem("auth_token");

// Initialize with store data
const templates = ref({
  welcome: { ...emailStore.templates.welcome },
  password_update: { ...emailStore.templates.password_update }
})

// Fetch templates when component mounts
onMounted(async () => {
  if (!token) {
    router.push("/login");
  }
  
  loading.value = true
  try {
    await emailStore.fetchTemplates()
    templates.value = { ...emailStore.templates }
  } catch (error) {
    console.error('Failed to load templates:', error)
  } finally {
    loading.value = false
  }
})

// Update methods for child components
const updateWelcomeTemplate = (updated) => {
  templates.value.welcome = { ...updated }
}

const updatePasswordTemplate = (updated) => {
  templates.value.password_update = { ...updated }
}

// Save all templates
const saveTemplates = async () => {
  loading.value = true
  try {
    await emailStore.saveTemplates(templates.value)
    alert('Templates saved successfully!')
  } catch (error) {
    alert('Failed to save templates: ' + error.message)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.email-templates-admin {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  padding-bottom: 15px;
  border-bottom: 1px solid #eaeaea;
}

.header-left h1 {
  font-size: 24px;
  margin-bottom: 5px;
}

.header-left p {
  color: #6c757d;
  margin: 0;
}

.template-container {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  overflow: hidden;
}

.template-tabs {
  display: flex;
  border-bottom: 1px solid #eaeaea;
}

.template-tabs button {
  padding: 12px 20px;
  background: none;
  border: none;
  cursor: pointer;
  font-weight: 500;
  color: #6c757d;
  position: relative;
}

.template-tabs button.active {
  color: #10b759;
}

.template-tabs button.active::after {
  content: '';
  position: absolute;
  bottom: -1px;
  left: 0;
  right: 0;
  height: 2px;
  background: #10b759;
}

.template-content {
  padding: 20px;
  min-height: 300px;
  position: relative;
}

.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255,255,255,0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #10b759;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.btn {
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.btn-primary {
  background: #10b759;
  color: white;
  border: none;
}

.btn-primary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.btn-primary:hover:not(:disabled) {
  background: #0e9d4d;
}
</style>