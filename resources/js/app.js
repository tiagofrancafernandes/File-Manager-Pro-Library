import './bootstrap'
import '../css/app.css'

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'
import { createPinia } from 'pinia'
import { Head, Link } from '@inertiajs/vue3'
import vClickOutside from 'click-outside-vue3';
import { formatSize } from '@/Helpers/formaters';

globalThis['formatSize'] = formatSize;

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: title => `${title} - ${appName}`,
    resolve: name =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue')
        ),
    setup ({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        return app.use(plugin)
        .use(createPinia())
        .use(ZiggyVue)
        .use(vClickOutside)
        .component('Head', Head)
        .component('Link', Link)
        .mount(el);

    },
    progress: {
        color: '#4B5563'
    }
})
