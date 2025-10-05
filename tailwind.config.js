import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                background: '#E7ECF5',
                surface: '#FFFFFF',
                surfaceMuted: '#F7F7F7',
                text: '#1A1A1A',
                muted: '#6B7280',
                primary: '#2642A4',
                border: '#D1D5DB',
                accent: '#F3C726',
                icon: '#9CA3AF',
            },
            fontFamily: {
                sans: ['"Noto Sans JP"', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                surface: '0 2px 8px rgba(17, 24, 39, 0.08)',
            },
            borderRadius: {
                pill: '9999px',
            },
        },
    },

    plugins: [forms],
};
