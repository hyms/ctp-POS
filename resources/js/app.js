import "./bootstrap";

import "@mdi/font/css/materialdesignicons.css";
import {createApp, h} from "vue";
// import "vuetify/styles";
import {createVuetify} from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
import * as labs from "vuetify/labs/components";
import {es} from "vuetify/locale";
import moment from "moment";
import 'typeface-roboto/index.css';

import "@/../css/main.scss";
// import "@/../css/app.css";

import {resolvePageComponent} from "laravel-vite-plugin/inertia-helpers";
// import 'vuetify/dist/vuetify.min.css'

moment.locale("es");

import {createInertiaApp} from "@inertiajs/vue3";

const customLight = {
    colors: {
        // background: "rgba(0, 0, 21, 0.2)",
        // surface: "#f7f7f7",
        surface: "rgba(245,245,245,0)",
        primary: "#3c858d",
        secondary: "#5e8592",
        error: "#e75f5f",
        info: "#3d71a5",
        success: "#4fbb73",
        warning: "#ffc340",
    },
};
const vuetify = createVuetify({
    components: {
        ...components,
        ...labs,
    },
    directives,
    locale: {
        locale: "es",
        fallback: "es",
        messages: {es},
    },
    icons: {
        defaultSet: "mdi",
    },
    theme: {
        defaultTheme: "customLight",
        themes: {
            customLight,
        },
    },
    defaults: {
        VBtn: {size:'small'},
        VCard: {density: 'compact'},
        VTable: {density: 'compact'},
        VTextField: {density: 'compact',variant:'outlined'},
        VCheckbox: {density: 'compact'},
        VSelect: {density: 'compact',variant:'outlined'},
        VAutocomplete: {density: 'compact',variant:'outlined'},
        VDataTable: {density: 'compact'},
        VSwitch: {density: 'compact'},
        VToolbar: {density: 'compact'},
    },
});


createInertiaApp({
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({el, App, props, plugin}) {
        createApp({render: () => h(App, props)})
            .use(plugin)
            .use(vuetify)
            .mount(el);
    },
    progress: {
        color: "#007b89",
        showSpinner: true,
    },
});
