const dashboardRoutes = [
    {
      path: "/admin",
      name: "AdminHome",
      component: () => import("@/pages/dashboard/DashboardHome.vue"),
    },
  ];
  
  export default dashboardRoutes;
  