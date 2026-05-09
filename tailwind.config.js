module.exports = {
  darkMode: 'class',
  content: [
    "./*.php",
    "./**/*.php",
    "./js/**/*.js",
    "./php/**/*.php",
    "./listing-detail/*.html"
  ],
  theme: {
    extend: {
      colors: { 
        "primary": "#ec5b13", 
        "whatsapp": "#25D366", 
        "background-dark": "#0a0705" 
      },
      fontFamily: { 
        "display": ["Inter", "sans-serif"], 
        "serif": ["Playfair Display", "serif"] 
      }
    },
  },
  plugins: [
    require('@tailwindcss/container-queries'),
  ],
}
