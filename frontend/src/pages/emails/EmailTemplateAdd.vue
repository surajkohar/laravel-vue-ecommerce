<template>
  <div class="products-listing">
    <div class="header-section">
      <h1>{{ isEditMode ? 'Edit' : 'Add' }} Email Template</h1>
      <div class="action-buttons">
        <button class="back-button" @click="router.push('/admin/email-templates')">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
          </svg>
          Back
        </button>
      </div>
    </div>

    <div class="table-container">
      <form @submit.prevent="submitForm">
        <div class="form-row">
          <div class="form-group">
            <label>Title*</label>
            <input type="text" v-model="form.title" required class="form-control">
          </div>

          <div class="form-group">
            <label>Slug*</label>
            <input type="text" v-model="form.slug" required class="form-control">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Type*</label>
            <select v-model="form.type" required class="form-control">
              <option value="admin">Admin</option>
              <option value="client">Client</option>
            </select>
          </div>

          <div class="form-group">
            <label>Subject*</label>
            <input type="text" v-model="form.subject" required class="form-control">
          </div>
        </div>

        <div class="form-group">
          <label>Content*</label>
          <QuillEditor
            v-model:content="form.description"
            contentType="html"
            theme="snow"
            :options="editorOptions"
            placeholder="Enter email template content"
            class="quill-editor"
          />
          <div class="short-codes">
            <small>Available Short Codes: {{ form.short_codes }}</small>
          </div>
        </div>

        
        <div class="form-actions">
        <button class="cancel-button" @click="router.push('/admin/email-templates')">Cancel</button>
        <button class="save-button" @click="saveUser" :disabled="loading">
          <i v-if="!loading" class="fas fa-save"></i>
          <i v-else class="fas fa-spinner fa-spin"></i>
          {{ isEditMode ? 'Update' : 'Save' }} Template
        </button>
      </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { API } from '@/utils/config';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import useEmailTemplateForm from '@/pages/emails/EmailTemplateAdd'
// Form state
const {
  editorOptions,
  form,
  loading,
  error,
  isEditMode,
  templateId,
  fetchTemplate,
  submitForm
} = useEmailTemplateForm();

onMounted(() => {
  fetchTemplate();
});
</script>

<style scoped>
@import "@/assets/css/custom.css";

.products-listing {
  padding: 20px;
}

.header-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.action-buttons {
  display: flex;
  gap: 10px;
}

.back-button {
  display: flex;
  align-items: center;
  gap: 5px;
  padding: 8px 12px;
  background: #f0f0f0;
  border: 1px solid #ddd;
  border-radius: 4px;
  cursor: pointer;
}

.table-container {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.form-row {
  display: flex;
  gap: 20px;
  margin-bottom: 15px;
}

.form-group {
  flex: 1;
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: 500;
}

.form-control {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.form-control:focus {
  border-color: #007bff;
  outline: none;
}

/* Quill Editor Custom Styles */
.quill-editor {
  height: 300px;
  margin-bottom: 15px;
}

.quill-editor :deep(.ql-toolbar) {
  border-top-left-radius: 4px;
  border-top-right-radius: 4px;
  border: 1px solid #ddd !important;
}

.quill-editor :deep(.ql-container) {
  border-bottom-left-radius: 4px;
  border-bottom-right-radius: 4px;
  border: 1px solid #ddd !important;
  height: calc(100% - 42px);
  font-family: inherit;
}

.short-codes {
  margin-top: 5px;
  color: #666;
  font-size: 13px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
}

.cancel-button {
  padding: 8px 16px;
  background: #f8f9fa;
  border: 1px solid #ddd;
  border-radius: 4px;
  cursor: pointer;
}

.submit-button {
  padding: 8px 16px;
  background: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
}

.submit-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.error-message {
  color: #dc3545;
  margin-top: 10px;
  font-size: 14px;
}
</style>