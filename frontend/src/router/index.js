import { createRouter, createWebHistory } from "vue-router";
import frontendRoutes from "./frontendRoutes";
import dashboardRoutes from "./dashboardRoutes";

const routes = [...frontendRoutes, ...dashboardRoutes];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
