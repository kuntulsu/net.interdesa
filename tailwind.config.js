/** @type {import('tailwindcss').Config} */
import preset from "./vendor/filament/support/tailwind.config.preset";

export default {
    presets: [preset],
    content: [
        "./app/Filament/**/*.php",
        "./resources/views/filament/**/*.blade.php",
        "./resources/views/components/*.blade.php",
        "./vendor/filament/**/*.blade.php",
    ],
    theme: {
        extend: {},
    },
    plugins: [],
};
