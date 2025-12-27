import axios from 'axios';
import { API } from '@/utils/config';
import { useAuthStore } from '@/store/auth';

/**
 * Frontend User API instance
 */
const api = axios.create({
  baseURL: API.BACKEND_URL,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
});

/**
 * REQUEST INTERCEPTOR
 * Attach Bearer token on every request
 */
api.interceptors.request.use(
  (config) => {
    const authStore = useAuthStore();

    // Ensure auth is hydrated
    if (!authStore._hydrated) {
      authStore.hydrate();
    }

    if (authStore.token) {
      config.headers.Authorization = `Bearer ${authStore.token}`;
    }

    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

/**
 * RESPONSE INTERCEPTOR
 * Handle token expiration / unauthorized
 */
api.interceptors.response.use(
  (response) => response,
  (error) => {
    const authStore = useAuthStore();

    if (error.response) {
      const status = error.response.status;

      // Token expired or invalid
      if (status === 401) {
        console.warn('🔐 Unauthorized - logging out user');
        authStore.logout();

        // Optional: redirect to login
        window.location.href = '/login';
      }
    }

    return Promise.reject(error);
  }
);

export default api;
