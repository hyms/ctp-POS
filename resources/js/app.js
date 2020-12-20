import { App, plugin } from '@inertiajs/inertia-vue'
import Vue from 'vue'
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
import { InertiaProgress } from '@inertiajs/progress'

import 'bootstrap-vue/dist/bootstrap-vue.css'

Vue.use(plugin)

// Install BootstrapVue
Vue.use(BootstrapVue)
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)
InertiaProgress.init()

const el = document.getElementById('app')

new Vue({
    render: h => h(App, {
        props: {
            initialPage: JSON.parse(el.dataset.page),
            resolveComponent: name => import(`./Pages/${name}`).then(module => module.default),
        },
    }),
}).$mount(el)
