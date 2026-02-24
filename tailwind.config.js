module.exports = {
  content: [
    './app/Views/**/*.php',
    './app/Views/**/*.html',
    './public/**/*.{html,js,php}',
    './app/Controllers/**/*.php'
  ],
  theme: {
    extend: {
      colors: {
        indigo: {
          // replace default indigo-600/700 with the new primary
          600: '#002C76',
          700: '#001F5C'
        }
      }
    },
  },
  plugins: [],
};
