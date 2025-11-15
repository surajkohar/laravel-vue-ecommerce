// stores/emailStore.js
import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios'
import { API } from "@/utils/config";


export const useEmailStore = defineStore('email', () => {
  const templates = ref({
    welcome: {
      subject: 'Welcome to Our Platform',
      message: 'Thank you for joining us! Your account has been created successfully.',
      includePassword: true
    },
    password_update: {
      subject: 'Your Password Has Been Updated',
      message: 'Your password was changed successfully.'
    }
  })
  
  const loading = ref(false)
  const error = ref(null)
  const token = localStorage.getItem("auth_token");

  // Fetch templates from backend
  const fetchTemplates = async () => {
    try {
      loading.value = true
      const { data } = await axios.get(`${API.BACKEND_URL}/email/templates`)
      templates.value = data.templates
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to load templates'
    } finally {
      loading.value = false
    }
  }

  // Save templates to backend
  const saveTemplates = async () => {
    try {
      loading.value = true
      await axios.post(`${API.BACKEND_URL}/email/templates`, {
        templates: templates.value
      })
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to save templates'
      throw err
    } finally {
      loading.value = false
    }
  }

  return { 
    templates,
    loading,
    error,
    fetchTemplates,
    saveTemplates
  }
})