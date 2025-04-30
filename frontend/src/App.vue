<script setup>
import { RouterView } from "vue-router"
import { useAuthStore } from '@/store/auth'
import { onMounted } from 'vue'

const authStore = useAuthStore()

onMounted(() => {
  authStore.hydrate()
})
</script>

<template>
  <RouterView />
</template>