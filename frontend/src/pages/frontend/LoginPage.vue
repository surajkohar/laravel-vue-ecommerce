<template>
  <div class="auth-container">
    <h2>Login to MyShop</h2>
    <form @submit.prevent="handleLogin">
      <input type="email" placeholder="Email" v-model="email" required />
      <input
        type="password"
        placeholder="Password"
        v-model="password"
        required
      />
      <button type="submit">Login</button>
    </form>
    <p>
      Don't have an account? <router-link to="/signup">Sign up</router-link>
    </p>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { useAuthStore } from "@/store/auth";
import { API } from "@/utils/config";
import { toast } from "vue3-toastify";

const email = ref("");
const password = ref("");
const loading = ref(false);
const router = useRouter();
const authStore = useAuthStore();

const handleLogin = async () => {
  try {
    const res = await axios.post(`${API.BACKEND_URL}/login`, {
      email: email.value,
      password: password.value,
    });

    const { token, user, permissions } = res.data.data;

    authStore.login({ token, user, permissions }); //  Use Pinia action
    const redirectPath = user.is_admin ? "/admin" : "/";
    router.push(redirectPath);
  } catch (error) {
    if (error.response) {
      const errorMessage =
        error.response.data.message ||
        error.response.data.error ||
        "Invalid credentials";
      toast.error(errorMessage);
    } else {
      console.error("Login error:", error);
      toast.error("Something went wrong. Please try again.");
    }
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
@import "@/assets/css/auth.css";
</style>
