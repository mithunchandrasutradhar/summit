import preset from './vendor/filament/filament/tailwind.config.preset';

/** @type {import('tailwindcss').Config} */
export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                // Same tokens as the public site (tailwind.config.js) so the
                // admin panel and the public site read as one product.
                brand: {
                    50: '#fff8eb',
                    100: '#ffecc6',
                    200: '#ffd888',
                    300: '#ffbe4a',
                    400: '#ffa41f',
                    500: '#f98307',
                    600: '#dd6103',
                    700: '#b74306',
                    800: '#94340c',
                    900: '#7a2c0d',
                    950: '#461404',
                },
                ink: {
                    50: '#f5f6fa',
                    100: '#e9ebf3',
                    200: '#cad0e3',
                    300: '#a3adcb',
                    400: '#7885af',
                    500: '#586494',
                    600: '#454f78',
                    700: '#383f61',
                    800: '#262b45',
                    900: '#181c30',
                    950: '#0c0e1c',
                },
            },
        },
    },
};
