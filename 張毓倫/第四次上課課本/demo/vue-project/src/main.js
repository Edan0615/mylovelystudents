import { createApp } from 'vue'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
import App from './App.vue'

import MyInfo from './components/MyInfo.vue'

const app = createApp(App);
app.component('my-info', MyInfo);
app.mount("#app");