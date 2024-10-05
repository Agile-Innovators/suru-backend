/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class', 
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors:{
        white: '#F8F8F8',
        primary: '#0A1029',
        secondary: '#003C55',
        'light-blue': '#20B7DD',
        'translucid-blue': 'rgba(0, 201, 247, 0.25)',
        grey: '#6D7585',
        'light-grey': '#C3C8D2'
      },
    },
  },
  plugins: [],
}

