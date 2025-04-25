<template>
  <div class="auth-container">
    <h2>Login to MyShop</h2>
    <form @submit.prevent="handleLogin">
      <input type="email" placeholder="Email" v-model="email" required />
      <input type="password" placeholder="Password" v-model="password" required />
      <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <router-link to="/signup">Sign up</router-link></p>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useAuthStore } from '@/store/auth'
import { API } from '@/utils/config'

const email = ref('')
const password = ref('')
const router = useRouter()
const authStore = useAuthStore()

const handleLogin = async () => {
  try {
    const res = await axios.post(`${API.BACKEND_URL}/login`, {
      email: email.value,
      password: password.value,
    })

    const { token, user } = res.data.data;

    authStore.login({ token, user })  //  Use Pinia action
    router.push('/admin')
  } catch (error) {
    if (error.response) {
      alert(error.response.data.message || 'Invalid credentials')
    } else {
      alert('Something went wrong. Please try again.')
    }
  }
}
</script>

<style scoped>
@import "@/assets/css/auth.css";
</style>
