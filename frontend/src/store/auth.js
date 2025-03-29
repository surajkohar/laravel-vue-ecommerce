import { defineStore } from "pinia";
import api from "@/services/api";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: null,
        token: localStorage.getItem("token") || "",
    }),
    actions: {
        async login(credentials) {
            try {
                const { data } = await api.post("login", credentials);
                this.token = data.token;
                localStorage.setItem("token", this.token);
            } catch (error) {
                console.error("Login failed:", error);
            }
        },
        async logout() {
            await api.post("logout");
            this.token = "";
            localStorage.removeItem("token");
        },
    },
});
