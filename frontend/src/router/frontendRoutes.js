import HomePage from "@/pages/frontend/HomePage.vue";
import LoginPage from "@/pages/frontend/LoginPage.vue";
import SignupPage from "@/pages/frontend/SignupPage.vue";

const frontendRoutes = [
  { path: "/", name: "Home", component: HomePage },
  { path: "/login", name: "Login", component: LoginPage },
  { path: "/signup", name: "Signup", component: SignupPage },
];

export default frontendRoutes;
