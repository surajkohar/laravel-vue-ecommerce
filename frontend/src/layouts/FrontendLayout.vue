<template>
  <div class="frontend-layout">
    <Navbar />
    <main class="main-content">
      <slot />
    </main>
    <Footer />
  </div>
</template>

<script setup>
import Navbar from '@/components/frontend/Navbar.vue'
import Footer from '@/components/frontend/Footer.vue'
</script>

<style scoped src="@/assets/styles/frontend.css"></style>
<style scoped src="@/assets/styles/animations.css"></style>

<style scoped>
.frontend-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.main-content {
  flex: 1;
  padding-top: 70px; /* Account for fixed navbar */
}
</style>