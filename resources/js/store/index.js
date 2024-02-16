import { createStore } from "vuex";
import User from "./modules/user";
import title from "./modules/title";
import profile from "./modules/profile";
import posts from "./modules/posts";
export default createStore({
    modules: {
        User,
        title,
        profile,
        posts
    }
})