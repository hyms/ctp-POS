import "./bootstrap";

import "@mdi/font/css/materialdesignicons.css";
import { createApp, h } from "vue";
import "vuetify/styles";
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
import * as labs from "vuetify/labs/components";
import { es } from "vuetify/locale";
import moment from "moment";

// import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
// import 'vuetify/dist/vuetify.min.css'

moment.locale("es");

import { createInertiaApp } from "@inertiajs/vue3";

const customLight = {
    dark: false,
    colors: {
        // background: "#2C384A",
        surface: "#f7f7f7",
        primary: "#3c858d",
        secondary: "#5e8592",
        error: "#e75f5f",
        info: "#39f",
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
        messages: { es },
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
});

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            // .use({moment})
            .use(vuetify)
            // .mixin({methods: {route}})
            .mount(el);
    },
    progress: {
        color: "#007b89",
    },
});
