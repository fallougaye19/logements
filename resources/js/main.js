import { createApp } from 'vue'
import App from './app.vue'
import router from './router'
import { createPinia } from 'pinia'
import '../css/app.css'
import axios from 'axios';

const app = createApp(App)
app.use(createPinia())
app.use(router)
app.mount('#app')

const token = localStorage.getItem("token");
if (token) {
    axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
}
