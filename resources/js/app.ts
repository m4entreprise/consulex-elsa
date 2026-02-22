import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import '../css/app.css';
import { initializeTheme, updateTheme, type Appearance } from './composables/useAppearance';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

const PUBLIC_FRONT_PATHS = new Set([
    '/',
    '/partenaires',
    '/mentions-legales',
    '/confidentialite',
    '/inscription/spectateurs',
    '/inscription/candidats',
]);

function normalizePathname(pathname: string): string {
    if (!pathname) {
        return '/';
    }

    if (pathname.length > 1 && pathname.endsWith('/')) {
        return pathname.slice(0, -1);
    }

    return pathname;
}

function isPublicFrontPath(pathname: string): boolean {
    return PUBLIC_FRONT_PATHS.has(normalizePathname(pathname));
}

function forceLightMode(): void {
    if (typeof document === 'undefined') {
        return;
    }

    document.documentElement.classList.remove('dark');
}

let themeInitialized = false;

function applyThemeForCurrentPage(): void {
    if (typeof window === 'undefined') {
        return;
    }

    if (isPublicFrontPath(window.location.pathname)) {
        forceLightMode();
        return;
    }

    if (!themeInitialized) {
        initializeTheme();
        themeInitialized = true;
        return;
    }

    const savedAppearance = localStorage.getItem('appearance') as Appearance | null;
    updateTheme(savedAppearance || 'system');
}

applyThemeForCurrentPage();

if (typeof document !== 'undefined') {
    document.addEventListener('inertia:navigate', applyThemeForCurrentPage);
}

if (typeof window !== 'undefined') {
    const mediaQueryList = window.matchMedia('(prefers-color-scheme: dark)');
    mediaQueryList.addEventListener('change', () => {
        setTimeout(applyThemeForCurrentPage, 0);
    });
}
