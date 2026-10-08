/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './templates/**/*.twig',
    '../../../modules/custom/**/src/**/*.php',
  ],
  theme: {
    container: {
      center: true,
      screens: {
        DEFAULT: "1440px",
      },
    },
    extend: {
      colors: {
        'saw-light-blue': '#0078FC',
        'saw-dark-blue': '#120B47',
        'saw-bright-blue': '#3232FF',
        'saw-pale-blue': '#D5E0FE',
        'saw-green': '#31D57C',
        'saw-peach': '#F97777',
        'saw-yellow': '#FCC66E',
      },
      fontFamily: {
        sans: ['Barlow', 'sans-serif'],
        serif: ['Merriweather', 'serif'],
        mono: ['Fira Code', 'monospace'],
      },
    },
  },
  plugins: [
    require('@tailwindcss/typography'),
  ],
}
