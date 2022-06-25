import './bootstrap';

import Vue from 'vue';
import Vuetify from 'vuetify'
import {createInertiaApp} from '@inertiajs/inertia-vue';
import {InertiaProgress} from '@inertiajs/progress';

createInertiaApp({
    resolve: name => require(`./Pages/${name}`),
    setup({el, App, props, plugin}) {
        Vue.use(plugin)
        Vue.use(Vuetify)
        Vue.mixin({methods: {route}})
        new Vue({
            render: h => h(App, props),
            vuetify: new Vuetify({
                icons: {
                    iconfont: 'mdiSvg',
                },
                // lang:'es',
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

InertiaProgress.init({color: '#4B5563'});
