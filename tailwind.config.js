import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
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
            },
        },
    },
    plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
};
