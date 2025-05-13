import './bootstrap';
import '../css/app.css';
import { createApp } from 'vue';
import './echo';
import App from './App.vue';
import router from './router';
import axios from 'axios';
import './bootstrap';
import { loadSlim } from '@tsparticles/slim';
import Particles from '@tsparticles/vue3';
axios.defaults.baseURL = `${window.location.origin}`;

const app = createApp(App);
app.use(router);
app.use(Particles, {
    init: async (engine) => {
        await loadSlim(engine);
    },
});
app.config.globalProperties.$axios = axios;
app.mount('#app');
