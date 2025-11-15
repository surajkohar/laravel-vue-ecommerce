// src/composables/useEmailTemplateForm.js
import { ref, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { API } from '@/utils/config';

export default function useEmailTemplateForm() {
  const router = useRouter();
  const route = useRoute();

  // Quill Editor Configuration
  const editorOptions = {
    modules: {
      toolbar: [
        ["bold", "italic", "underline", "strike"],
        ["blockquote", "code-block"],
        [{ header: 1 }, { header: 2 }],
        [{ list: "ordered" }, { list: "bullet" }],
        [{ script: "sub" }, { script: "super" }],
        [{ indent: "-1" }, { indent: "+1" }],
        [{ direction: "rtl" }],
        [{ size: ["small", false, "large", "huge"] }],
        [{ header: [1, 2, 3, 4, 5, 6, false] }],
        [{ color: [] }, { background: [] }],
        [{ font: [] }],
        [{ align: [] }],
        ["clean"],
        ["link", "image"],
      ],
    },
    placeholder: "Enter email template content...",
    theme: "snow",
  };

  // Form state
  const form = ref({
    title: '',
    slug: '',
    type: 'admin',
    subject: '',
    description: '',
    short_codes: ''
  });

  const loading = ref(false);
  const error = ref(null);

  // Computed properties
  const isEditMode = computed(() => !!route.params.id);
  const templateId = computed(() => route.params.id);

  // Fetch template data if in edit mode
  const fetchTemplate = async () => {
    if (!isEditMode.value) return;
    
    try {
      loading.value = true;
      const token = localStorage.getItem('auth_token');
      const response = await fetch(`${API.BACKEND_URL}/email-templates/${templateId.value}`, {
        headers: {
          'Authorization': `Bearer ${token}`,
        },
      });

      if (!response.ok) {
        throw new Error('Failed to fetch template');
      }

      const data = await response.json();
      
      if (data.data) {
        form.value = { ...data.data };
      } else if (data.page && data.page.data) {
        form.value = { ...data.page.data };
      } else {
        form.value = { ...data };
      }
    } catch (err) {
      error.value = err.message;
      console.error('Error fetching template:', err);
      router.push('/admin/email-templates');
    } finally {
      loading.value = false;
    }
  };

  // Form submission
  const submitForm = async () => {
    try {
      loading.value = true;
      const token = localStorage.getItem('auth_token');
      
      const url = isEditMode.value 
        ? `${API.BACKEND_URL}/email-templates/${templateId.value}`
        : `${API.BACKEND_URL}/email-templates`;
        
      const method = isEditMode.value ? 'PUT' : 'POST';

      const response = await fetch(url, {
        method,
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`,
        },
        body: JSON.stringify(form.value)
      });

      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || 'Failed to save template');
      }

      router.push('/admin/email-templates');
    } catch (err) {
      error.value = err.message;
      console.error('Error saving template:', err);
      alert(`Failed to ${isEditMode.value ? 'update' : 'save'} template: ${err.message}`);
    } finally {
      loading.value = false;
    }
  };

  return {
    editorOptions,
    form,
    loading,
    error,
    isEditMode,
    templateId,
    fetchTemplate,
    submitForm
  };
}