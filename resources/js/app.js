import('./bootstrap');
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, Link } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import Antd from 'ant-design-vue';
import 'ant-design-vue/lib/popover/style/index.css';
import 'ant-design-vue/lib/dropdown/style/index.css';
import 'ant-design-vue/lib/tooltip/style/index.css';
import 'v-calendar/dist/style.css';
import VCalendar from 'v-calendar';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
  setup({ el, app, props, plugin }) {
    return createApp({ render: () => h(app, props) })
      .use(plugin)
      .use(ZiggyVue, Ziggy)
      .use(Antd)
      .use(VCalendar)
      .component('inertia-link', Link)
      .mount(el);
  },
});

InertiaProgress.init({ color: '#4B5563' });
