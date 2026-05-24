/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        // Kamu bisa menamainya 'primary', 'brand', atau apa pun
        'primary': '#1e3f7c',
        'primary-dark': '#152d59', // Opsional: untuk efek hover yang lebih gelap
      },
    },
  },
  plugins: [],
}