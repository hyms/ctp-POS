import './bootstrap';
import '../css/app.css';

import '@mdi/font/css/materialdesignicons.css'
import Vue from 'vue';
import Vuetify from 'vuetify'
import es from 'vuetify/lib/locale/es'
import vueMoment from 'vue-moment'
import moment from 'moment'
import * as momentLocale from 'moment/locale/es'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
// import 'vuetify/dist/vuetify.min.css'

import {createInertiaApp} from '@inertiajs/inertia-vue';
import {InertiaProgress} from '@inertiajs/progress';

createInertiaApp({
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({el, App, props, plugin}) {
        Vue.use(plugin)
        Vue.use(vueMoment, {moment});
        Vue.use(Vuetify)
        Vue.mixin({methods: {route}})
        new Vue({
            render: h => h(App, props),
            vuetify: new Vuetify({
                icons: {
                    iconfont: 'mdi',
                },
                lang: {locales: {es}, current: 'es'},
                theme: {
                    themes: {
                        light: {
                            primary: '#007b89',
                            secondary: '#5e8592',
                            accent: '#2C384A',
                            error: '#e55353',
                            info: '#39f',
                            success: '#2eb85c',
                            warning: '#f9b115',
                        },
                    },
                },
            }),
        }).$mount(el)
    },
})

InertiaProgress.init({color: '#007b89'});
