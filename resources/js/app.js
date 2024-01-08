import "./bootstrap";
import { createApp, h } from "vue";
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
import * as labs from "vuetify/labs/components";
import { es } from "vuetify/locale";
import { aliases, fa } from "vuetify/iconsets/fa-svg";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { fas } from "@fortawesome/free-solid-svg-icons";
import { far } from "@fortawesome/free-regular-svg-icons";
import moment from "moment/moment";
import "moment/locale/es";

import "typeface-roboto/index.css";

import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";

// import 'vuetify/_styles.scss'
import "@/../css/main.scss";

import { createInertiaApp } from "@inertiajs/vue3";

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
        messages: { es },
    },
    icons: {
        defaultSet: "fa",
        aliases,
        sets: {
            fa,
        },
    },
    theme: {
        defaultTheme: "customLight",
        themes: {
            customLight,
        },
    },
    defaults: {
        VBtn: { size: "small" },
        VCard: { density: "compact" },
        VTable: { density: "compact" },
        VTextarea: {
            density: "compact",
            variant: "outlined",
            color: "primary",
        },
        VTextField: {
            density: "compact",
            variant: "outlined",
            color: "primary",
        },
        VCheckbox: { density: "compact", color: "primary" },
        VSelect: { density: "compact", variant: "outlined", color: "primary" },
        VAutocomplete: {
            density: "compact",
            variant: "outlined",
            color: "primary",
        },
        VDataTable: { density: "compact" },
        VSwitch: { density: "compact", color: "primary" },
        VToolbar: { density: "comfortable" },
    },
});

createInertiaApp({
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup: ({ el, App, props, plugin }) => {
        moment.locale("es");
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(vuetify)
            .provide("moment", moment);
        app.component("font-awesome-icon", FontAwesomeIcon);
        library.add(fas); // Include needed solid icons
        library.add(far); // Include needed regular icons
        app.mount(el);
    },
    progress: {
        color: "#007b89",
    },
});
