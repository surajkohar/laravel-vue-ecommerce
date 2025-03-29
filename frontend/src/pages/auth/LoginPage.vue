<script setup>
import { ref } from "vue";
import { useAuthStore } from "@/store/auth";
import { useRouter } from "vue-router";

const auth = useAuthStore();
const router = useRouter();
const email = ref("");
const password = ref("");

async function login() {
    await auth.login({ email: email.value, password: password.value });
    router.push("/");
}
</script>

<template>
    <div>
        <h2>Login</h2>
        <input v-model="email" placeholder="Email" />
        <input v-model="password" type="password" placeholder="Password" />
        <button @click="login">Login</button>
    </div>
</template>
