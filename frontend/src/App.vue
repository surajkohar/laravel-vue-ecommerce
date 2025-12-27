<script setup>
import { RouterView } from "vue-router"
import { useAuthStore } from '@/store/auth'
import { onMounted } from 'vue'
import SettingsLoader from '@/layouts/SettingsLoader.vue'
const authStore = useAuthStore()

onMounted(() => {
  authStore.hydrate()
})
</script>

<template>
  <SettingsLoader>
    <RouterView />
  </SettingsLoader>
</template>