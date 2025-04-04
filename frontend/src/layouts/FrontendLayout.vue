<script setup>
import Navbar from "@/components/frontend/Navbar.vue";
import Footer from "@/components/frontend/Footer.vue";
</script>

<template>
  <div class="layout-wrapper">
    <Navbar />
    <main class="main-content">
      <slot />
    </main>
    <Footer />
  </div>
</template>

<style scoped src="@/assets/styles/layouts/frontendLayout.css"></style>
