<template>
    <div class="dashboard">
        <DashboardNavbar />
        <div class="dashboard-container">
            <Sidebar />
            <main class="dashboard-content">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import DashboardNavbar from "@/components/dashboard/DashboardNavbar.vue";
import Sidebar from "@/components/dashboard/Sidebar.vue";
</script>

<style scoped>
@import "@/assets/css/dashboard.css";
</style>
