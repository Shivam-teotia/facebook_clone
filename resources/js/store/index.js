import { createStore } from "vuex";
import User from "./modules/user";
import title from "./modules/title";
export default createStore({
    modules: {
        User,
        title
    }
})