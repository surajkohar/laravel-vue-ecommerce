import { createApp } from "vue";
import { createPinia } from "pinia";
import router from "@/router"; // ✅ Import single router
import App from "./App.vue";

import "@/assets/styles/main.css"; // ✅ Ensure this file exists

const app = createApp(App);
app.use(createPinia());
app.use(router); // ✅ Use single router
app.mount("#app");
