<template>
  <div class="add-product-container">
    <div class="header-section-add">
      <h1>Edit User</h1>
      <div class="action-buttons">
        <button class="cancel-button" @click="goBack">
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
      <div class="form-card">
        <div class="">
          <h5>Basic Information</h5>
          <div class="form-group full-width-field">
            <label>Full Name</label>
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

          <div class="form-group full-width-field">
            <label>Email</label>
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

          <div class="form-group full-width-field">
            <label>Phone Number</label>
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

          <div class="form-group full-width-field">
            <label>Roles</label>
            <select v-if="roles.length" v-model="user.roleId">
              <option v-for="(r, index) in roles" :value="r.id">
                {{ r.name }}
              </option>
            </select>
            <span class="error-message" v-if="errors.role">{{
              errors.phone[0]
            }}</span>
          </div>

          <h5>Status</h5>
          <div class="d-flex align-items-center">
            <label class="toggle-switch">
              <input
                type="checkbox"
                v-model="user.status"
                :true-value="1"
                :false-value="0"
              />
              <span class="slider round"></span>
            </label>
            &nbsp;&nbsp;
            <span class="toggle-label">{{
              user.status === 1 ? "Active" : "Inactive"
            }}</span>
          </div>
        </div>

        <div class="form-actions">
          <button class="cancel-button" @click="goBack">Cancel</button>
          <button class="save-button" @click="saveUser" :disabled="loading">
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
              <path
                d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"
              ></path>
              <polyline points="17 21 17 13 7 13 7 21"></polyline>
              <polyline points="7 3 7 8 15 8"></polyline>
            </svg>
            {{ loading ? "Saving..." : "Save User" }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import { API } from "@/utils/config";
import { toast } from "vue3-toastify";

const router = useRouter();
const route = useRoute();

const user = ref({
  name: "",
  email: "",
  phone: "",
  status: 1,
  roleId: "",
});
const errors = ref({});
const loading = ref(false);
const roles = ref([]);
const token = localStorage.getItem("auth_token");

const userId = ref(null);

// Fetch roles
const fetchRoles = async () => {
  try {
    const response = await fetch(`${API.BACKEND_URL}/rolesAndPermissions`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    const data = await response.json();
    if (data.status) {
      roles.value = data.roles ?? [];
    }
  } catch (error) {
    console.error("Error fetching roles:", error);
  }
};

// Fetch user details if editing
const fetchUser = async (id) => {
  try {
    const response = await fetch(`${API.BACKEND_URL}/user/${id}/edit`, {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
    });

    if (response.ok) {
      const data = await response.json();
      if (data.status) {
        user.value = {
          name: data.data.name,
          email: data.data.email,
          phone: data.data.phone,
          status: data.data.status,
          roleId: data.data.role_id, // will be null if no role assigned
        };
      }
    } else {
      throw new Error("Failed to fetch user");
    }
  } catch (error) {
    console.error("Error fetching user:", error);
  }
};

// Save or Update user
const saveUser = async () => {
  loading.value = true;
  errors.value = {};

  try {
    const response = await fetch(
      `${API.BACKEND_URL}/user/${userId.value}/update`,
      {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({
          name: user.value.name,
          email: user.value.email,
          phone: user.value.phone,
          status: user.value.status,
          roleid: user.value.roleId,
        }),
      }
    );

    const data = await response.json();

    if (!response.ok) {
      if (response.status === 422) {
        errors.value = data.errors || {};
      }
      throw new Error(data.message || "Failed to save user");
    }

    toast.success("User updated successfully!");
    setTimeout(() => {
      router.push("/admin/users");
    }, 1500);
  } catch (error) {
    toast.error(error.message || "An error occurred.");
  } finally {
    loading.value = false;
  }
};

onMounted(async () => {
  if (!token) {
    router.push("/login");
  }
  fetchRoles();

  if (route.params.id) {
    userId.value = route.params.id;
    await fetchUser(route.params.id);
  }
});

const goBack = () => {
  router.go(-1);
};
</script>

<style scoped>
@import "@/assets/css/custom.css";
</style>