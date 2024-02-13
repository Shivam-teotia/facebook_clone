import './bootstrap';
import { createApp } from 'vue';
import router from "./router";
import App from "./components/App.vue"
import store from './store';

const app = createApp({});
app.component("App", App);

app.use(router);
app.use(store);
app.mount('#app');
