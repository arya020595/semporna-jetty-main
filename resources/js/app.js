import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { createPinia } from "pinia";
import i18n from '@/Plugins/i18n';

import AdminLayout from "./Layouts/AdminLayout.vue";

import bootstrap from "bootstrap";

createInertiaApp({
    title: (title) => `${title} - Semporna Jetty Management System`,
    progress: {
        // The delay after which the progress bar will appear
        // during navigation, in milliseconds.
        delay: 250,

        // The color of the progress bar.
        color: "#8a2529",

        // Whether the NProgress spinner will be shown.
        showSpinner: false,
    },
    resolve: (name) => {
        let page = require(`./Pages/${name}`).default;
        page.layout ??= AdminLayout;
        return page;
    },
    setup({ el, App, props, plugin }) {
        const pinia = createPinia();

        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia)
            .use(i18n)
            .mount(el);
    },
});
