import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/store/auth";
import HomePage from "@/pages/HomePage.vue";
import LoginPage from "@/pages/auth/LoginPage.vue";

const routes = [
    { path: "/", name: "Home", component: HomePage },
    { path: "/login", name: "Login", component: LoginPage },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const auth = useAuthStore();
    if (to.meta.requiresAuth && !auth.token) {
        next("/login");
    } else {
        next();
    }
});

export default router;
