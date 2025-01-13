const defaultTheme = require('tailwindcss/defaultTheme');
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
            gridTemplateRows: {
               '8': 'repeat(8, minmax(0, 1fr))',
               '9': 'repeat(9, minmax(0, 1fr))',
            },
            gridRowStart: {
                '8': '8',
                '9': '9',
            },
        },
    },

    plugins: [forms, typography],
};
