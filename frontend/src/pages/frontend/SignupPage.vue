<template>
  <FrontendLayout>
    <div class="auth-page">
      <div class="auth-container">
        <div class="auth-card">
          <div class="auth-header">
            <h1>Create Account</h1>
            <p>Join our fashion community and start shopping today</p>
          </div>

          <form @submit.prevent="handleSignup" class="auth-form">
            <div class="form-group">
              <label for="name">Full Name</label>
              <input 
                type="text" 
                id="name"
                v-model="name" 
                placeholder="Enter your full name" 
                required
                class="form-input"
              >
            </div>

            <div class="form-group">
              <label for="email">Email Address</label>
              <input 
                type="email" 
                id="email"
                v-model="email" 
                placeholder="Enter your email" 
                required
                class="form-input"
              >
            </div>

            <div class="form-group">
              <label for="password">Password</label>
              <input 
                type="password" 
                id="password"
                v-model="password" 
                placeholder="Create a password" 
                required
                class="form-input"
              >
            </div>

            <div class="form-group">
              <label for="confirmPassword">Confirm Password</label>
              <input 
                type="password" 
                id="confirmPassword"
                v-model="passwordConfirmation" 
                placeholder="Confirm your password" 
                required
                class="form-input"
              >
            </div>

            <div class="form-options">
              <label class="terms-agree">
                <input type="checkbox" v-model="agreeTerms" required>
                <span>I agree to the <a href="#" class="terms-link">Terms of Service</a> and <a href="#" class="terms-link">Privacy Policy</a></span>
              </label>
            </div>

            <button type="submit" class="auth-btn primary" :disabled="loading">
              <span v-if="loading" class="btn-spinner"></span>
              {{ loading ? 'Creating Account...' : 'Create Account' }}
            </button>
          </form>

          <div class="auth-divider">
            <span>Or continue with</span>
          </div>

          <div class="social-auth">
            <button class="social-btn google">
              <span class="social-icon">🔍</span>
              Google
            </button>
            <button class="social-btn facebook">
              <span class="social-icon">📘</span>
              Facebook
            </button>
          </div>

          <div class="auth-footer">
            <p>
              Already have an account? 
              <router-link to="/login" class="auth-link">Sign in here</router-link>
            </p>
          </div>
        </div>

        <div class="auth-hero">
          <div class="hero-content">
            <h2>Welcome to FashionStore</h2>
            <p>Create your account and discover amazing benefits</p>
            <div class="hero-features">
              <div class="feature">
                <span class="feature-icon">🎁</span>
                <span>Exclusive Offers</span>
              </div>
              <div class="feature">
                <span class="feature-icon">⭐</span>
                <span>Member Rewards</span>
              </div>
              <div class="feature">
                <span class="feature-icon">🚀</span>
                <span>Fast Checkout</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </FrontendLayout>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import { API } from '@/utils/config'
import { toast } from 'vue3-toastify'

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const agreeTerms = ref(false)
const loading = ref(false)
const router = useRouter()

const handleSignup = async () => {
  if (password.value !== passwordConfirmation.value) {
    toast.error('Passwords do not match');
    return;
  }

  if (!agreeTerms.value) {
    toast.error('Please agree to the terms and conditions');
    return;
  }

  loading.value = true;
  try {
    const response = await axios.post(`${API.BACKEND_URL}/signup`, {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    })

    toast.success('Account created successfully! Please sign in.');
    router.push('/login');
  } catch (error) {
    if (error.response) {
      const errorMessage = error.response.data.message || 'Signup failed. Please try again.';
      toast.error(errorMessage);
    } else {
      console.error('❌ Unexpected error:', error);
      toast.error('Something went wrong. Please try again.');
    }
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.auth-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 0;
}

.auth-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  max-width: 1000px;
  width: 100%;
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
  margin: 0 20px;
}

.auth-card {
  padding: 3rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.auth-header {
  text-align: center;
  margin-bottom: 2.5rem;
}

.auth-header h1 {
  color: #2c3e50;
  margin-bottom: 0.5rem;
  font-size: 2.2rem;
}

.auth-header p {
  color: #6c757d;
  font-size: 1.1rem;
}

.auth-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  color: #2c3e50;
  font-weight: 500;
  font-size: 0.9rem;
}

.form-input {
  padding: 12px 16px;
  border: 2px solid #e9ecef;
  border-radius: 10px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: #f8f9fa;
}

.form-input:focus {
  outline: none;
  border-color: #3498db;
  background: white;
  box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.form-options {
  margin: 0.5rem 0;
}

.terms-agree {
  display: flex;
  align-items: flex-start;
  gap: 0.5rem;
  color: #5d6d7e;
  cursor: pointer;
  font-size: 0.9rem;
  line-height: 1.4;
}

.terms-agree input {
  margin-top: 0.2rem;
  flex-shrink: 0;
}

.terms-link {
  color: #3498db;
  text-decoration: none;
  font-weight: 500;
}

.terms-link:hover {
  text-decoration: underline;
}

.auth-btn {
  padding: 14px;
  border: none;
  border-radius: 10px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.auth-btn.primary {
  background: #3498db;
  color: white;
}

.auth-btn.primary:hover:not(:disabled) {
  background: #2980b9;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(52, 152, 219, 0.3);
}

.auth-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.btn-spinner {
  width: 18px;
  height: 18px;
  border: 2px solid transparent;
  border-top: 2px solid white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.auth-divider {
  display: flex;
  align-items: center;
  margin: 2rem 0;
  color: #6c757d;
  font-size: 0.9rem;
}

.auth-divider::before,
.auth-divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: #e9ecef;
}

.auth-divider span {
  padding: 0 1rem;
}

.social-auth {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
}

.social-btn {
  flex: 1;
  padding: 12px;
  border: 2px solid #e9ecef;
  border-radius: 10px;
  background: white;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  font-weight: 500;
}

.social-btn:hover {
  border-color: #3498db;
  transform: translateY(-2px);
}

.social-btn.google:hover {
  border-color: #db4437;
}

.social-btn.facebook:hover {
  border-color: #4267b2;
}

.social-icon {
  font-size: 1.1rem;
}

.auth-footer {
  text-align: center;
  color: #6c757d;
}

.auth-link {
  color: #3498db;
  text-decoration: none;
  font-weight: 500;
}

.auth-link:hover {
  text-decoration: underline;
}

.auth-hero {
  background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
  color: white;
  padding: 3rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.hero-content {
  text-align: center;
}

.hero-content h2 {
  font-size: 2.2rem;
  margin-bottom: 1rem;
}

.hero-content p {
  font-size: 1.1rem;
  opacity: 0.9;
  margin-bottom: 2.5rem;
}

.hero-features {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.feature {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 10px;
  backdrop-filter: blur(10px);
}

.feature-icon {
  font-size: 1.5rem;
}

/* Responsive */
@media (max-width: 768px) {
  .auth-container {
    grid-template-columns: 1fr;
    margin: 1rem;
  }
  
  .auth-hero {
    display: none;
  }
  
  .auth-card {
    padding: 2rem;
  }
  
  .social-auth {
    flex-direction: column;
  }
}

@media (max-width: 480px) {
  .auth-card {
    padding: 1.5rem;
  }
}
</style>