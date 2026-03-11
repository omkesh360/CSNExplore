/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: "class",
    content: [
        "./public/**/*.{html,js}",
        "./scripts/**/*.{js,html}"
    ],
    theme: {
        extend: {
            colors: {
                "primary": "#003580",
                "primary-hover": "#002357",
                "primary-dark": "#002357",
                "accent": "#FC7D05",
                "background-light": "#f5f7f8",
                "background-dark": "#0f1723",
                "text-main": "#1a1a1a",
                "text-muted": "#6b7280",
            },
            fontFamily: {
                "display": ["Be Vietnam Pro", "sans-serif"],
                "sans": ["Be Vietnam Pro", "sans-serif"],
            },
            borderRadius: {
                "DEFAULT": "0.25rem",
                "lg": "0.5rem",
                "xl": "0.75rem",
                "2xl": "1rem",
            },
            boxShadow: {
                'soft': '0 2px 8px 0 rgba(26, 26, 26, 0.16)',
                'card': '0 2px 16px 0 rgba(0,0,0,0.08)',
            }
        },
    },
    plugins: [],
}
