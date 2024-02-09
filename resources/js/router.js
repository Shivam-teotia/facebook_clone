import { createRouter, createWebHistory } from "vue-router";
import Start from "./vues/Start.vue"
export default createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: "/",
            name: "home",
            component: Start,
        },
    ],
});