import {App, plugin} from '@inertiajs/inertia-vue'
import Vue from 'vue'
import {BootstrapVue, IconsPlugin} from 'bootstrap-vue'
import {InertiaProgress} from '@inertiajs/progress'
// import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import firebase from './firebase'

Vue.use(plugin)

// Install BootstrapVue
Vue.use(BootstrapVue)
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)
InertiaProgress.init()
//axios.defaults.withCredentials = true;
//axios.defaults.baseURL = "http://localhost:8000";
Vue.use(require('vue-moment'));
Vue.prototype.$messaging = firebase

// navigator.serviceWorker.register('./firebase-messaging-sw.js')
//     .then((registration) => {
//         console.log(registration);
//         Vue.prototype.$messaging.useServiceWorker(registration);
//     }).catch(err => {
//     console.log(err)
// })

const el = document.getElementById('app')
new Vue({
    render: h => h(App, {
        props: {
            initialPage: JSON.parse(el.dataset.page),
            resolveComponent: name => import(`./Pages/${name}`).then(module => module.default),
        },
    }),
}).$mount(el)
