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
                antigravity: {
                    DEFAULT: '#7132f5',
                    dark: '#5741d8',
                    darker: '#5b1ecf',
                },
                'brand-neutral': {
                    900: '#101114',
                    500: '#686b82',
                    400: '#9497a9',
                },
                success: {
                    DEFAULT: '#149e61',
                    dark: '#026b3f',
                }
            },
            fontFamily: {
                sans: ['"Antigravity-Product"', ...defaultTheme.fontFamily.sans],
                display: ['"Antigravity-Display"', ...defaultTheme.fontFamily.sans],
                product: ['"Antigravity-Product"', ...defaultTheme.fontFamily.sans],
            },
            borderRadius: {
                standard: '12px',
            },
            boxShadow: {
                whisper: '0px 4px 24px rgba(0,0,0,0.03)',
            }
        },
    },

    plugins: [forms],
};
