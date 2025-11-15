<template>
  <div class="add-user-container">
    <!-- Header Section -->
    <div class="header-section">
      <h1>Add New User</h1>
      <div class="action-buttons">
        <button class="back-button" @click="goBack">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="16"
            height="16"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
          </svg>
          Back
        </button>
      </div>
    </div>

    <div class="form-container">
      <div class="row">
        <!-- Left Column (User Details) -->
        <div class="col-md-8">
          <div class="card details-card">
            <div class="card-body">
              <h5 class="section-title">Basic Information</h5>

              <div class="form-group">
                <label>Full Name <span class="required">*</span></label>
                <input
                  type="text"
                  v-model="user.name"
                  placeholder="Enter full name"
                  :class="{ 'error-field': errors.name }"
                />
                <span class="error-message" v-if="errors.name">{{
                  errors.name[0]
                }}</span>
              </div>

              <div class="form-group">
                <label>Email <span class="required">*</span></label>
                <input
                  type="email"
                  v-model="user.email"
                  placeholder="Enter email address"
                  :class="{ 'error-field': errors.email }"
                />
                <span class="error-message" v-if="errors.email">{{
                  errors.email[0]
                }}</span>
              </div>

              <div class="form-group">
                <label>Phone Number <span class="required">*</span></label>
                <input
                  type="tel"
                  v-model="user.phone"
                  placeholder="Enter phone number"
                  :class="{ 'error-field': errors.phone }"
                />
                <span class="error-message" v-if="errors.phone">{{
                  errors.phone[0]
                }}</span>
              </div>

              <div class="form-group">
                <label>Role</label>
                <Select
                  v-model="user.roleId"
                  placeholder="Select role"
                  :options="roles"
                  option-label="name"
                  option-value="id"
                  :class="{ 'error-field': errors.roleId }"
                />
                <span class="error-message" v-if="errors.roleId">{{
                  errors.roleId[0]
                }}</span>
              </div>

              <h5 class="section-title">Password</h5>
              <div class="form-group">
                <div class="password-options">
                  <label class="radio-option">
                    <input type="radio" v-model="passwordOption" value="auto" />
                    <span class="radio-custom"></span>
                    <span class="radio-label">Generate random password</span>
                  </label>
                  <label class="radio-option">
                    <input
                      type="radio"
                      v-model="passwordOption"
                      value="manual"
                    />
                    <span class="radio-custom"></span>
                    <span class="radio-label">Set custom password</span>
                  </label>
                </div>
              </div>

              <div class="form-group" v-if="passwordOption === 'manual'">
                <label>Password</label>
                <div class="password-input-wrapper">
                  <input
                    :type="showPassword ? 'text' : 'password'"
                    v-model="user.password"
                    placeholder="Enter password"
                    :class="{ 'error-field': errors.password }"
                  />
                  <button
                    type="button"
                    class="toggle-password"
                    @click="showPassword = !showPassword"
                  >
                    <i
                      :class="showPassword ? 'far fa-eye-slash' : 'far fa-eye'"
                    ></i>
                  </button>
                </div>
                <span class="error-message" v-if="errors.password">{{
                  errors.password[0]
                }}</span>
                <div class="password-strength" v-if="user.password">
                  Password strength:
                  <span :class="passwordStrength.class">{{
                    passwordStrength.text
                  }}</span>
                </div>
              </div>

              <div class="form-group" v-if="passwordOption === 'auto'">
                <label>Generated Password</label>
                <div class="generated-password">
                  <input type="text" v-model="generatedPassword" readonly />
                  <button
                    type="button"
                    class="regenerate-btn"
                    @click="generatePassword"
                  >
                    <i class="fas fa-sync-alt"></i> Regenerate
                  </button>
                </div>
                <div class="password-hint">
                  This password will be emailed to the user
                </div>
              </div>

              <h5 class="section-title">Status</h5>
              <div class="form-group status-toggle">
                <label class="toggle-switch">
                  <input
                    type="checkbox"
                    v-model="user.status"
                    :true-value="1"
                    :false-value="0"
                  />
                  <span class="slider round"></span>
                </label>
                <span class="toggle-label">{{
                  user.status === 1 ? "Active" : "Inactive"
                }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column (Profile Picture) -->
        <div class="col-md-4">
          <div class="card profile-card">
            <div class="card-body">
              <h5 class="section-title">Profile Picture</h5>

              <div class="profile-upload-wrapper">
                <div class="avatar-upload">
                  <div
                    class="avatar-preview"
                    :style="
                      previewImage
                        ? { 'background-image': `url(${previewImage})` }
                        : {}
                    "
                  >
                    <div v-if="!previewImage" class="initials">
                      {{ userInitials }}
                    </div>
                  </div>
                  <div class="avatar-upload-controls">
                    <label for="avatarUpload" class="upload-btn">
                      <i class="fas fa-camera"></i> Upload Photo
                    </label>
                    <input
                      id="avatarUpload"
                      type="file"
                      accept="image/*"
                      @change="handleImageUpload"
                      style="display: none"
                    />
                    <button
                      v-if="previewImage"
                      type="button"
                      class="remove-btn"
                      @click="removeImage"
                    >
                      <i class="fas fa-trash"></i> Remove
                    </button>
                  </div>
                </div>
                <div class="upload-hint">
                  Recommended size: 300x300px (JPG, PNG)
                </div>
              </div>

              <div class="additional-options">
                <h5 class="section-title">Additional Options</h5>

                <div class="form-group">
                  <label>Send Welcome Email</label>
                  <label class="switch">
                    <input type="checkbox" v-model="sendWelcomeEmail" />
                    <span class="slider round"></span>
                  </label>
                  <span class="toggle-label">{{
                    sendWelcomeEmail ? "Yes" : "No"
                  }}</span>
                </div>

                <div class="form-group" v-if="sendWelcomeEmail">
                  <label>Custom Welcome Message</label>
                  <textarea
                    v-model="welcomeMessage"
                    rows="3"
                    placeholder="Optional custom message for the welcome email"
                  ></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Form Actions -->
      <div class="form-actions">
        <button class="cancel-button" @click="goBack">Cancel</button>
        <button class="save-button" @click="saveUser" :disabled="loading">
          <i class="fas fa-save"></i>
          {{ loading ? "Saving..." : "Save User" }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import { API } from "@/utils/config";
import { toast } from "vue3-toastify";
import Select from "@/components/Select.vue";

const router = useRouter();

// State
const user = ref({
  name: "",
  email: "",
  phone: "",
  status: 1,
  roleId: "",
  password: "",
});

const errors = ref({});
const loading = ref(false);
const roles = ref([]);
const previewImage = ref(null);
const imageFile = ref(null);
const passwordOption = ref("auto");
const showPassword = ref(false);
const generatedPassword = ref("");
const sendWelcomeEmail = ref(true);
const welcomeMessage = ref("");

const token = localStorage.getItem("auth_token");

// Computed properties
const userInitials = computed(() => {
  if (!user.value.name) return "UP";
  const names = user.value.name.split(" ");
  return names
    .map((name) => name[0])
    .join("")
    .toUpperCase()
    .substring(0, 2);
});

const passwordStrength = computed(() => {
  if (!user.value.password) return { text: "", class: "" };

  const strength = calculatePasswordStrength(user.value.password);

  if (strength < 2) return { text: "Weak", class: "weak" };
  if (strength < 4) return { text: "Medium", class: "medium" };
  return { text: "Strong", class: "strong" };
});

// Methods
const calculatePasswordStrength = (password) => {
  let strength = 0;
  if (password.length > 7) strength++;
  if (password.match(/[a-z]/)) strength++;
  if (password.match(/[A-Z]/)) strength++;
  if (password.match(/[0-9]/)) strength++;
  if (password.match(/[^a-zA-Z0-9]/)) strength++;
  return strength;
};

const generatePassword = () => {
  const chars =
    "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*";
  let password = "";
  for (let i = 0; i < 12; i++) {
    password += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  generatedPassword.value = password;
  user.value.password = password;
};

const handleImageUpload = (event) => {
  const file = event.target.files[0];
  if (!file) return;

  if (!file.type.match("image.*")) {
    toast.error("Please select an image file");
    return;
  }

  if (file.size > 2 * 1024 * 1024) {
    toast.error("Image size should be less than 2MB");
    return;
  }

  imageFile.value = file;
  previewImage.value = URL.createObjectURL(file);
};

const removeImage = () => {
  previewImage.value = null;
  imageFile.value = null;
};

const prepareFormData = () => {
  const formData = new FormData();

  // Add user data
  formData.append("name", user.value.name);
  formData.append("email", user.value.email);
  formData.append("phone", user.value.phone);
  formData.append("status", user.value.status);
  formData.append("roleid", user.value.roleId);

  // Add password if manual mode
  if (passwordOption.value === "manual" && user.value.password) {
    formData.append("password", user.value.password);
  }

  // Add profile image if exists
  if (imageFile.value) {
    formData.append("profile_image", imageFile.value);
  }

  // Add welcome email options
  formData.append("send_welcome_email", sendWelcomeEmail.value);
  if (sendWelcomeEmail.value && welcomeMessage.value) {
    formData.append("welcome_message", welcomeMessage.value);
  }

  return formData;
};

const saveUser = async () => {
  loading.value = true;
  errors.value = {};

  try {
    const formData = prepareFormData();

    const response = await fetch(`${API.BACKEND_URL}/user/add`, {
      method: "POST",
      headers: {
        Accept: "application/json",
        Authorization: `Bearer ${token}`,
      },
      body: formData,
    });

    const data = await response.json();

    if (!response.ok) {
      if (response.status === 422) {
        errors.value = data.errors || {};
      }
      throw new Error(data.message || "Failed to create user");
    }

    toast.success("User created successfully!");
    setTimeout(() => {
      router.push("/admin/users");
    }, 1500);
  } catch (error) {
    toast.error(error.message || "An error occurred while creating the user");
  } finally {
    loading.value = false;
  }
};

const fetchRoles = async () => {
  try {
    loading.value = true;
    const response = await fetch(`${API.BACKEND_URL}/rolesAndPermissions`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    if (response.ok) {
      const data = await response.json();
      if (data.status) {
        roles.value = data.roles ?? [];
      }
    } else {
      throw new Error("Failed to fetch roles");
    }
  } catch (error) {
    console.error("Error fetching roles:", error);
    toast.error("Failed to load roles");
  } finally {
    loading.value = false;
  }
};

onMounted(async () => {
  if (!token) {
    router.push("/login");
  }
  fetchRoles();
  generatePassword();
});

const goBack = () => {
  router.go(-1);
};
</script>

<style scoped>
@import "@/assets/css/custom.css";

.add-user-container {
  padding: 20px;
  max-width: 1400px;
  margin: 0 auto;
}

.header-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.back-button {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  background-color: #f8f9fa;
  border: 1px solid #ddd;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.2s;
}

.back-button:hover {
  background-color: #e9ecef;
}

.form-container {
  margin-top: 20px;
}

.card {
  border: none;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  margin-bottom: 20px;
}

.details-card,
.profile-card {
  padding: 20px;
}

.section-title {
  font-size: 16px;
  font-weight: 600;
  color: #333;
  margin-bottom: 15px;
  padding-bottom: 8px;
  border-bottom: 1px solid #eee;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
  color: #495057;
}

.form-group input,
.form-group textarea,
.form-group select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  transition: border-color 0.2s;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
  border-color: #4d90fe;
  outline: none;
}

.error-field {
  border-color: #f1416c !important;
}

.error-message {
  color: #f1416c;
  font-size: 13px;
  margin-top: 5px;
  display: block;
}

.required {
  color: #f1416c;
  margin-left: 4px;
}

/* Profile Upload */
.profile-upload-wrapper {
  text-align: center;
  margin-bottom: 20px;
}

.avatar-upload {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.avatar-preview {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  background-color: #f5f5f5;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  margin-bottom: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  border: 3px solid #e9ecef;
}

.initials {
  font-size: 40px;
  font-weight: bold;
  color: #6c757d;
}

.avatar-upload-controls {
  display: flex;
  gap: 10px;
}

.upload-btn,
.remove-btn {
  padding: 8px 15px;
  border-radius: 4px;
  font-size: 14px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 5px;
  transition: all 0.2s;
}

.upload-btn {
  background-color: #10b759;
  color: white;
  border: none;
}

.upload-btn:hover {
  background-color: #5fca8e;
}

.remove-btn {
  background-color: #f8f9fa;
  border: 1px solid #ddd;
  color: #6c757d;
}

.remove-btn:hover {
  background-color: #e9ecef;
}

.upload-hint {
  font-size: 12px;
  color: #6c757d;
  margin-top: 10px;
}

/* Password Options */
/* Password Options */
.password-options {
  display: flex;
  flex-direction: row;
  gap: 20px;
  margin-bottom: 15px;
}

.password-input-wrapper {
  position: relative;
}

.toggle-password {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #6c757d;
  cursor: pointer;
}

.generated-password {
  display: flex;
  gap: 10px;
}

.generated-password input {
  flex-grow: 1;
}

.regenerate-btn {
  background-color: #f8f9fa;
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 0 12px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 13px;
}

.regenerate-btn:hover {
  background-color: #e9ecef;
}

.password-strength {
  font-size: 13px;
  margin-top: 5px;
}

.password-strength .weak {
  color: #f1416c;
}

.password-strength .medium {
  color: #f59f00;
}

.password-strength .strong {
  color: #10b759;
}

.password-hint {
  font-size: 12px;
  color: #6c757d;
  margin-top: 5px;
}



/* Form Actions */
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 15px;
  margin-top: 20px;
}

.cancel-button,
.save-button {
  padding: 10px 20px;
  border-radius: 4px;
  font-size: 14px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s;
}

.cancel-button {
  background-color: #f8f9fa;
  border: 1px solid #ddd;
  color: #495057;
}

.cancel-button:hover {
  background-color: #e9ecef;
}

.save-button {
  background-color: #10b759;
  border: none;
  color: white;
}

.save-button:hover {
  background-color: #0e9d4d;
}

.save-button:disabled {
  background-color: #a5d8b6;
  cursor: not-allowed;
}

/* Additional Options */
.additional-options {
  margin-top: 30px;
}

.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
  margin-right: 10px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.switch .slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: 0.4s;
}

.switch .slider:before {
  position: absolute;
  content: "";
  height: 16px;
  width: 16px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: 0.4s;
}

.switch input:checked + .slider {
  background-color: #10b759;
}

.switch input:checked + .slider:before {
  transform: translateX(26px);
}

.switch .slider.round {
  border-radius: 34px;
}

.switch .slider.round:before {
  border-radius: 50%;
}

@media (max-width: 768px) {
  .row {
    flex-direction: column;
  }

  .col-md-8,
  .col-md-4 {
    width: 100%;
  }

  .avatar-preview {
    width: 120px;
    height: 120px;
  }
}
</style>
