<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Profile Settings</h3>
        <button class="close-btn" @click="$emit('close')">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>
      
      <div class="modal-body">
        <div class="profile-layout">
          <!-- Left Side: User Information -->
          <div class="user-info-section">
            <div class="section-header">
              <div class="section-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                  <circle cx="12" cy="7" r="4"></circle>
                </svg>
              </div>
              <h4>Personal Information</h4>
            </div>
            
            <div class="form-grid">
              <div class="form-group">
                <label>Full Name</label>
                <div class="input-wrapper">
                  <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                  </svg>
                  <input 
                    v-model="form.name" 
                    type="text" 
                    placeholder="Enter your full name"
                    class="input-with-icon"
                  >
                </div>
              </div>
              
              <div class="form-group">
                <label>Email Address</label>
                <div class="input-wrapper">
                  <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                  </svg>
                  <input 
                    v-model="form.email" 
                    type="email" 
                    placeholder="Enter your email"
                    class="input-with-icon"
                  >
                </div>
              </div>
              
              <div class="form-group">
                <label>Phone Number</label>
                <div class="input-wrapper">
                  <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                  </svg>
                  <input 
                    v-model="form.phone" 
                    type="tel" 
                    placeholder="Enter your phone number"
                    class="input-with-icon"
                  >
                </div>
              </div>
              
              <div class="form-group">
                <label>Role</label>
                <div class="input-wrapper">
                  <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                  </svg>
                  <input 
                    :value="user.role" 
                    type="text" 
                    disabled
                    class="input-with-icon disabled-input"
                  >
                </div>
              </div>
            </div>
          </div>

          <!-- Right Side: Profile Image -->
          <div class="profile-image-section">
            <div class="image-card">
              <div class="section-header">
                <div class="section-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                    <path d="m21 15-5-5L5 21"></path>
                  </svg>
                </div>
                <h4>Profile Photo</h4>
              </div>
              
              <div class="image-upload-area">
                <div class="image-preview" @click="triggerFileInput">
                  <img :src="imagePreview || user.avatar || '/default-avatar.png'" alt="Profile" class="profile-image-large">
                  <div class="image-overlay">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                  </div>
                </div>
                
                <button class="change-photo-btn dd-button" @click="triggerFileInput">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                  </svg>
                  Change Photo
                </button>
                
                <p class="image-hint">JPG, PNG or GIF - Max 2MB</p>
              </div>
              
              <input 
                ref="fileInput"
                type="file" 
                accept="image/*"
                @change="handleImageUpload"
                style="display: none"
              >
            </div>
          </div>
        </div>

        <!-- Divider -->
        <div class="section-divider">
          <span>Security Settings</span>
        </div>

        <!-- Change Password Section -->
        <div class="password-section">
          <div class="section-header">
            <div class="section-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
            </div>
            <h4>Change Password</h4>
          </div>
          
          <div class="form-grid">
            <div class="form-group">
              <label>New Password</label>
              <div class="input-wrapper">
                <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <input 
                  v-model="passwordForm.newPassword" 
                  type="password" 
                  placeholder="Enter new password"
                  class="input-with-icon"
                  minlength="8"
                >
              </div>
            </div>
            
            <div class="form-group">
              <label>Confirm New Password</label>
              <div class="input-wrapper">
                <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <input 
                  v-model="passwordForm.confirmPassword" 
                  type="password" 
                  placeholder="Confirm new password"
                  class="input-with-icon"
                  :class="{ error: passwordForm.newPassword && passwordForm.newPassword !== passwordForm.confirmPassword }"
                >
              </div>
              <span v-if="passwordForm.newPassword && passwordForm.newPassword !== passwordForm.confirmPassword" class="error-text">
                Passwords do not match
              </span>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-actions">
        <button type="button" class="btn btn-secondary" @click="$emit('close')">
          Cancel
        </button>
        <button type="button" class="btn btn-primary dd-button" @click="saveChanges" :disabled="loading">
          <span v-if="loading" class="btn-loading">
            <svg class="spinner" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 12a9 9 0 1 1-6.219-8.56"></path>
            </svg>
          </span>
          <span v-else>Save Changes</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'

const props = defineProps({
  user: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['close', 'update'])

const fileInput = ref(null)
const imagePreview = ref(null)
const loading = ref(false)

const form = reactive({
  name: '',
  email: '',
  phone: ''
})

const passwordForm = reactive({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
})

// Initialize form with user data
onMounted(() => {
  form.name = props.user.name
  form.email = props.user.email
  form.phone = props.user.phone
})

const triggerFileInput = () => {
  fileInput.value?.click()
}

const uploadedFile = ref(null);

const handleImageUpload = (event) => {
  const file = event.target.files[0];

  if (file) {
    if (file.size > 2 * 1024 * 1024) {
      alert("Image must be less than 2MB");
      return;
    }

    uploadedFile.value = file; // <-- store file

    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreview.value = e.target.result; // show preview
    };
    reader.readAsDataURL(file);
  }
}

const saveChanges = async () => {
  if (passwordForm.newPassword && passwordForm.newPassword !== passwordForm.confirmPassword) {
    alert('Passwords do not match');
    return;
  }

  loading.value = true;

  try {
    const updatedData = {
      ...form,
      ...(passwordForm.newPassword && {
        password: passwordForm.newPassword
      }),
      ...(uploadedFile.value && { profile_image: uploadedFile.value }) // MUST be File
    };

    emit("update", updatedData);
    emit("close");

  } catch (error) {
    console.error("Failed to update profile:", error);
  } finally {
    loading.value = false;
  }
}

</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  padding: 20px;
}

.modal-content {
  background: white;
  border-radius: 16px;
  width: 100%;
  max-width: 900px;
  max-height: 90vh;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px;
  border-bottom: 1px solid #f1f3f4;
  background: #fafbfc;
}

.modal-header h3 {
  margin: 0;
  font-size: 20px;
  font-weight: 600;
  color: #2d3748;
}

.close-btn {
  background: none;
  border: none;
  padding: 8px;
  cursor: pointer;
  color: #718096;
  border-radius: 6px;
  transition: all 0.2s;
}

.close-btn:hover {
  background: #f1f3f4;
  color: #2d3748;
}

.modal-body {
  padding: 0;
  overflow: auto;
  flex: 1 1 auto;
}

.profile-layout {
  display: grid;
  grid-template-columns: 1fr 320px;
  gap: 0;
}

.user-info-section {
  padding: 32px;
  border-right: 1px solid #f1f3f4;
}

.profile-image-section {
  padding: 32px;
  background: #fafbfc;
}

.section-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 24px;
}

.section-icon {
  width: 36px;
  height: 36px;
  background: #667eea;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.section-header h4 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: #2d3748;
}

.form-grid {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-group label {
  font-weight: 500;
  color: #374151;
  font-size: 14px;
}

.input-wrapper {
  position: relative;
}

.input-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
  width: 16px;
  height: 16px;
}

.input-with-icon {
  width: 100%;
  padding: 12px 12px 12px 40px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  transition: all 0.2s;
}

.input-with-icon:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.input-with-icon.disabled-input {
  background: #f9fafb;
  color: #6b7280;
  cursor: not-allowed;
}

.error-text {
  color: #ef4444;
  font-size: 12px;
  margin-top: 4px;
}

/* Image Upload Styles */
.image-card {
  text-align: center;
}

.image-upload-area {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
}

.image-preview {
  position: relative;
  width: 120px;
  height: 120px;
  cursor: pointer;
  border-radius: 50%;
  overflow: hidden;
  border: 3px solid #e5e7eb;
  transition: all 0.2s;
}

.image-preview:hover {
  border-color: #667eea;
}

.profile-image-large {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.image-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  opacity: 0;
  transition: opacity 0.2s;
}

.image-preview:hover .image-overlay {
  opacity: 1;
}

.change-photo-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background: #4CAF50;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.change-photo-btn:hover {
  background: #45a049;
}

.image-hint {
  color: #6b7280;
  font-size: 12px;
  margin: 0;
  text-align: center;
}

/* Divider */
.section-divider {
  position: relative;
  text-align: center;
  margin: 32px;
  border-top: 1px solid #e5e7eb;
}

.section-divider span {
  position: relative;
  top: -12px;
  background: white;
  padding: 0 16px;
  color: #6b7280;
  font-size: 12px;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Password Section */
.password-section {
  padding: 0 32px 32px;
}

/* Modal Actions */
.modal-actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  padding: 24px 32px;
  background: #fafbfc;
  border-top: 1px solid #f1f3f4;
}

.btn {
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  border: none;
  font-size: 14px;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-primary {
  background: #4CAF50;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #45a049;
  transform: translateY(-1px);
}

/* match project dd-button class */
.dd-button {
  background: #4CAF50 !important;
  color: #fff !important;
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-secondary {
  background: white;
  color: #374151;
  border: 1px solid #d1d5db;
}

.btn-secondary:hover {
  background: #f9fafb;
}

.btn-loading {
  display: flex;
  align-items: center;
}

.spinner {
  width: 16px;
  height: 16px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 768px) {
  .modal-overlay {
    padding: 10px;
  }
  
  .modal-content {
    border-radius: 12px;
  }
  
  .profile-layout {
    grid-template-columns: 1fr;
  }
  
  .user-info-section {
    border-right: none;
    border-bottom: 1px solid #f1f3f4;
    padding: 24px;
  }
  
  .profile-image-section {
    padding: 24px;
  }
  
  .password-section {
    padding: 24px;
  }
  
  .modal-actions {
    padding: 20px 24px;
    flex-direction: column;
  }
  
  .btn {
    justify-content: center;
  }
}
</style>