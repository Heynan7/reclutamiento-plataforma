import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class', // 👈 habilitamos modo oscuro con clase "dark"

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [
        forms,             // soporte para inputs y formularios
        require("daisyui") // DaisyUI (solo para componentes como swap)
    ],

    daisyui: {
        themes: false, // 🚀 desactivamos temas de DaisyUI → no cambia tus colores
    },
}
