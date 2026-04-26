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
          red:          '#D0201A',
          'red-deep':   '#A8160F',
          'red-soft':   '#F5E0DE',
          blue:         '#2563A8',
          'blue-deep':  '#1B4D85',
          'blue-soft':  '#DCE7F3',
          dark:         '#1a1a1a',
          cream:        '#f3ebde',
          'cream-deep': '#e8dcc7',
          paper:        '#ffffff',
          ink:          '#1a1a1a',
          'ink-soft':   '#3d3936',
          'ink-mute':   '#6b655e',
          'ink-faint':  '#a39d95',
          line:         '#d8cdb8',
          'line-soft':  '#e6dcc8',
        },
      },
      fontFamily: {
        sans:    ['Inter', 'system-ui', 'sans-serif'],
        display: ['Fraunces', 'Times New Roman', 'serif'],
        mono:    ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
      },
      letterSpacing: {
        tightest: '-0.035em',
      },
      boxShadow: {
        'rqbi-sm':  '0 1px 2px rgba(26,26,26,0.04), 0 2px 6px rgba(26,26,26,0.04)',
        'rqbi-md':  '0 6px 20px -8px rgba(26,26,26,0.12), 0 2px 6px rgba(26,26,26,0.05)',
        'rqbi-lg':  '0 20px 50px -16px rgba(26,26,26,0.18)',
        'rqbi-red': '0 2px 0 #A8160F, 0 8px 24px -8px rgba(208,32,26,0.45)',
      },
      animation: {
        'float-slow':   'float 7s ease-in-out infinite',
        'float-slower': 'float 9s ease-in-out infinite reverse',
        'marquee':      'marquee 32s linear infinite',
        'page-in':      'pageIn 0.5s cubic-bezier(.2,.7,.2,1)',
      },
      keyframes: {
        float: {
          '0%, 100%': { transform: 'translateY(0)' },
          '50%':      { transform: 'translateY(-12px)' },
        },
        marquee: {
          from: { transform: 'translateX(0)' },
          to:   { transform: 'translateX(-50%)' },
        },
        pageIn: {
          from: { opacity: '0', transform: 'translateY(8px)' },
          to:   { opacity: '1', transform: 'translateY(0)' },
        },
      },
    },
  },
  plugins: [],
}
