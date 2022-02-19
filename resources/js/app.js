import {App, plugin} from '@inertiajs/inertia-vue'
import Vue from 'vue'
import {BootstrapVue, IconsPlugin} from 'bootstrap-vue'
import {InertiaProgress} from '@inertiajs/progress'
import CoreuiVue from '@coreui/vue';
import firebase from './firebase'
import store from './store'
import 'bootstrap-vue/dist/bootstrap-vue.css'

Vue.config.performance = true

Vue.use(plugin)
// Install BootstrapVue
Vue.use(BootstrapVue)
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)
Vue.use(CoreuiVue)
InertiaProgress.init()
Vue.use(require('vue-moment'));
Vue.prototype.$messaging = firebase

const el = document.getElementById('app')
new Vue({
    render: h => h(App, {
        props: {
            initialPage: JSON.parse(el.dataset.page),
            resolveComponent: name => import(`./Pages/${name}`).then(module => module.default),
        },
    }),
    store,
}).$mount(el)
