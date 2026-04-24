/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './assets/**/*.{vue,ts,js}',
    './templates/**/*.twig',
  ],
  theme: {
    extend: {
      colors: {
        rqbi: {
          red:   '#D0201A',
          blue:  '#2563A8',
          dark:  '#1a1a1a',
          light: '#f5f5f5',
        },
      },
      fontFamily: {
        sans:    ['Nunito', 'sans-serif'],
        display: ['Syne', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
