import { createRouter, createWebHistory } from "vue-router";
import NewsFeed from "./vues/NewsFeed.vue";
import UserShow from "./vues/Users/Show.vue";
export default createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: "/",
            name: "home",
            component: NewsFeed,
        },
        {
            path: "/users/:userId",
            name: "user.show",
            component: UserShow,
        },
    ],
});