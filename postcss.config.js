const purgecss = require('@fullhuman/postcss-purgecss').default;
const cssnano = require('cssnano');

module.exports = {
  plugins: [
    purgecss({
      content: ['./*.html', './**/*.html', './js/**/*.js'],
      safelist: [
        /^fa-/, /^fas/, /^fab/, /^far/,
        'theme-btn', 'btn-style-one', 'active'
      ],
      defaultExtractor: content => content.match(/[\w-/:]+(?<!:)/g) || []
    }),
    cssnano({ preset: 'default' })
  ]
};