import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                eden: {
                    'azul-oscuro': '#1A2E4A',
                    'azul-medio': '#2D5F8A',
                    'azul-claro': '#4A90C4',
                    'verde': '#27AE60',
                    'naranja': '#E67E22',
                    'gris': '#F2F4F7',
                    'rojo': '#C0392B',
                },
            },
        },
    },

    plugins: [forms, typography],
};
