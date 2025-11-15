<template>
  <div class="send-email">
    <h1>Send Email</h1>
    
    <div class="form-container">
      <div class="template-selection">
        <div class="form-group">
          <label>Select Template</label>
          <select v-model="selectedTemplate" @change="loadTemplateVariables">
            <option value="">-- Select Template --</option>
            <option 
              v-for="template in templates" 
              :key="template.id" 
              :value="template.id"
            >
              {{ template.title }} ({{ template.slug }})
            </option>
          </select>
        </div>

        <div class="template-preview" v-if="selectedTemplateContent">
          <h3>Template Preview</h3>
          <div class="subject">{{ selectedTemplateContent.subject }}</div>
          <div class="content" v-html="previewContent"></div>
        </div>
      </div>

      <div class="recipient-details">
        <div class="form-group">
          <label>To Email</label>
          <input v-model="emailData.to" type="email" required>
        </div>

        <div class="form-group">
          <label>CC Emails (comma separated)</label>
          <input v-model="emailData.cc" type="text">
        </div>

        <div class="form-group">
          <label>BCC Emails (comma separated)</label>
          <input v-model="emailData.bcc" type="text">
        </div>

        <div class="form-group">
          <label>Subject</label>
          <input v-model="emailData.subject" type="text" required>
        </div>
      </div>

      <div class="variables-section">
        <h3>Template Variables</h3>
        <div class="variable-inputs">
          <div class="form-group" v-for="variable in variables" :key="variable">
            <label>{{ variable }}</label>
            <input 
              v-model="emailData.variables[variable]" 
              type="text" 
              :required="isRequiredVariable(variable)"
            >
          </div>
        </div>
      </div>

      <div class="attachments">
        <h3>Attachments</h3>
        <input type="file" multiple @change="handleFileUpload">
        <div class="file-list" v-if="emailData.attachments.length">
          <div v-for="(file, index) in emailData.attachments" :key="index" class="file-item">
            {{ file.name }}
            <button @click="removeAttachment(index)">×</button>
          </div>
        </div>
      </div>

      <div class="form-actions">
        <button type="button" class="btn btn-primary" @click="sendEmail">
          Send Email
        </button>
        <button type="button" class="btn btn-secondary" @click="resetForm">
          Reset
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const templates = ref([])
const selectedTemplate = ref('')
const selectedTemplateContent = ref(null)
const variables = ref([])

const emailData = ref({
  to: '',
  cc: '',
  bcc: '',
  subject: '',
  variables: {},
  attachments: []
})

const previewContent = computed(() => {
  if (!selectedTemplateContent.value) return ''
  
  let content = selectedTemplateContent.value.description
  for (const [key, value] of Object.entries(emailData.value.variables)) {
    content = content.replace(new RegExp(`{${key}}`, 'g'), value || `{${key}}`)
  }
  return content
})

const isRequiredVariable = (variable) => {
  return ['first_name', 'email', 'password'].includes(variable)
}

const loadTemplates = async () => {
  try {
    const response = await axios.get('/api/email-templates')
    templates.value = response.data.data
  } catch (error) {
    console.error('Failed to load templates:', error)
  }
}

const loadTemplateVariables = async () => {
  if (!selectedTemplate.value) {
    selectedTemplateContent.value = null
    variables.value = []
    emailData.value.variables = {}
    return
  }

  try {
    const response = await axios.get(`/api/email-templates/${selectedTemplate.value}/variables`)
    selectedTemplateContent.value = response.data.template
    variables.value = response.data.variables
    
    // Initialize variables object
    emailData.value.variables = {}
    variables.value.forEach(v => {
      emailData.value.variables[v] = ''
    })
    
    // Set subject from template
    emailData.value.subject = selectedTemplateContent.value.subject
  } catch (error) {
    console.error('Failed to load template variables:', error)
  }
}

const handleFileUpload = (event) => {
  emailData.value.attachments = Array.from(event.target.files)
}

const removeAttachment = (index) => {
  emailData.value.attachments.splice(index, 1)
}

const sendEmail = async () => {
  try {
    const payload = {
      template_id: selectedTemplate.value,
      to: emailData.value.to,
      variables: emailData.value.variables,
      cc: emailData.value.cc.split(',').map(e => e.trim()).filter(e => e),
      bcc: emailData.value.bcc.split(',').map(e => e.trim()).filter(e => e),
      subject: emailData.value.subject,
      attachments: emailData.value.attachments
    }

    await axios.post('/api/email-templates/send', payload, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    alert('Email sent successfully!')
    resetForm()
  } catch (error) {
    console.error('Failed to send email:', error)
    alert('Failed to send email: ' + error.response?.data?.message || error.message)
  }
}

const resetForm = () => {
  selectedTemplate.value = ''
  selectedTemplateContent.value = null
  variables.value = []
  emailData.value = {
    to: '',
    cc: '',
    bcc: '',
    subject: '',
    variables: {},
    attachments: []
  }
}

onMounted(loadTemplates)
</script>

<style scoped>
.send-email {
  padding: 20px;
}

.form-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 30px;
}

.template-selection {
  grid-column: 1 / -1;
}

.template-preview {
  margin-top: 20px;
  padding: 15px;
  border: 1px solid #eee;
  border-radius: 4px;
}

.template-preview .subject {
  font-weight: bold;
  margin-bottom: 10px;
}

.template-preview .content {
  border: 1px solid #eee;
  padding: 15px;
  min-height: 200px;
}

.variables-section {
  grid-column: 1 / -1;
}

.variable-inputs {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 15px;
}

.attachments {
  grid-column: 1 / -1;
}

.file-list {
  margin-top: 10px;
}

.file-item {
  display: flex;
  justify-content: space-between;
  padding: 8px;
  background: #f8f9fa;
  margin-bottom: 5px;
  border-radius: 4px;
}

.file-item button {
  background: none;
  border: none;
  color: #dc3545;
  cursor: pointer;
}

.form-actions {
  grid-column: 1 / -1;
  display: flex;
  gap: 10px;
  margin-top: 20px;
}

.btn {
  padding: 10px 20px;
  border-radius: 4px;
  cursor: pointer;
}

.btn-primary {
  background: #0d6efd;
  color: white;
  border: none;
}

.btn-secondary {
  background: #6c757d;
  color: white;
  border: none;
}
</style>