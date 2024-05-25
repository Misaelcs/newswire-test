import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import { Toast } from './helpers/toast';
import { OhVueIcon, addIcons } from "oh-vue-icons";
import { FaEllipsisH, BiPlusCircle, BiPlusCircleFill, HiSolidPencil } from "oh-vue-icons/icons";
import VueSweetalert2 from 'vue-sweetalert2';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import 'sweetalert2/dist/sweetalert2.min.css';

const toast = new Toast();
addIcons(FaEllipsisH, BiPlusCircle, BiPlusCircleFill, HiSolidPencil);

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) });

        vueApp
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(VueSweetalert2)
            .provide('toast', toast)
            .component('VueDatePicker', VueDatePicker)
            .component("v-icon", OhVueIcon)
            .mount(el);

        router.on('start', (event) => {
            toast.clear();
        });
    },
    progress: {
        color: '#4B5563',
    },
});
