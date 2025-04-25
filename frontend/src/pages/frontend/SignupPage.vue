<template>
  <div class="auth-container">
    <h2>Create Your Account</h2>
    <form @submit.prevent="handleSignup">
      <input type="text" placeholder="Full Name" v-model="name" required />
      <input type="email" placeholder="Email" v-model="email" required />
      <input type="password" placeholder="Password" v-model="password" required />
      <input type="password" placeholder="Confirm Password" v-model="passwordConfirmation" required />
      <button type="submit">Sign Up</button>
    </form>
    <p>Already have an account? <router-link to="/login">Login</router-link></p>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import { API } from '@/utils/config'  // ✅ Use central config

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const router = useRouter()

const apiUrl = import.meta.env.VITE_API_URL

const handleSignup = async () => {
  try {
    const response = await axios.post(`${API.BACKEND_URL}/signup`, {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    })

    console.log('✅ Signup success:', response.data)
    router.push('/login')
  } catch (error) {
    if (error.response) {
      console.error('❌ Signup error:', error.response.data)
      alert(error.response.data.message || 'Signup failed. Check your inputs.')
    } else {
      console.error('❌ Unexpected error:', error)
      alert('Something went wrong. Please try again.')
    }
  }
}
</script>

<style scoped>
@import "@/assets/css/auth.css";
</style>
