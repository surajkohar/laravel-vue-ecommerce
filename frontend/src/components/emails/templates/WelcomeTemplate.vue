<template>
  <div class="email-template">
    <h3>Welcome Email Template</h3>
    
    <div class="form-group">
      <label>Subject</label>
      <input 
        type="text" 
        v-model="template.subject" 
        placeholder="Email subject"
      />
    </div>
    
    <div class="form-group">
      <label>Message</label>
      <textarea
        v-model="template.message"
        placeholder="Email body content"
        rows="5"
      ></textarea>
    </div>
    
    <div class="form-group">
      <label>
        <input 
          type="checkbox" 
          v-model="template.includePassword" 
        />
        Include password in email
      </label>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  template: {
    type: Object,
    required: true,
    default: () => ({
      subject: '',
      message: '',
      includePassword: true
    })
  }
})

const emit = defineEmits(['update'])

const localTemplate = ref({ ...props.template })

watch(localTemplate, (newVal) => {
  emit('update', newVal)
}, { deep: true })
</script>

<style scoped>
.email-template {
  padding: 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 0.5rem;
  margin-bottom: 1rem;
}

.form-group {
  margin-bottom: 1rem;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
}

input[type="text"], textarea {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #cbd5e0;
  border-radius: 0.25rem;
}

textarea {
  min-height: 100px;
}
</style>