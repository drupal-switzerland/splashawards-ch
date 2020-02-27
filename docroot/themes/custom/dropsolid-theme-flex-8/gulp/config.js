/*jslint node: true */

const gulp = require('gulp');
const path = require('path');
const themePath = process.cwd();
const themeName = path.basename(process.cwd());

// fetch command line arguments
// https://www.sitepoint.com/pass-parameters-gulp-tasks/
let arg = (argList => {

  let arg = {}, a, opt, thisOpt, curOpt;
  for (a = 0; a < argList.length; a++) {

    thisOpt = argList[a].trim();
    opt = thisOpt.replace(/^\-+/, '');

    if (opt === thisOpt) {

      // argument value
      if (curOpt) arg[curOpt] = opt;
      curOpt = null;

    }
    else {

      // argument name
      curOpt = opt;
      arg[curOpt] = true;

    }

  }

  return arg;

})(process.argv);


// module.exports = {

let config = {
  // we can set an environment when running gulp
  // Use in Terminal: NODE_ENV=prod gulp build (or any other gulp task)
  // we can also set a default value like this:
  // default: { env: process.env.NODE_ENV || 'dev' }
  // Example to use in module.exports:
  // env: process.env.NODE_ENV || 'local',

  // only share certain arguments from Terminal
  // and give them a default value
  arg: {
    delay: arg.projectDelay || false,
    url: arg.projectUrl || false // 'http://PROJECT_NAME.docker.localhost:81'
  },

  // file locations
  paths: {
    base: './',
    theme: themePath,
  },
  themeName: themeName,
  iconfont: {
    src: 'icons/**/*.svg',
    dest: 'fonts/iconfont',
    fontName: 'iconfont',
    sass: './../../components/00-theme/00-base/01-helpers/02-mixins/_mixins-iconfont.scss',
    template: './gulp/templates/_iconfont.scss'
  },
  sprite: {
    src: 'icons/**/*.svg',
    svg: 'images/generated/sprite.svg',
    css: './components/00-theme/00-base/00-config/00-variables/_vars-sprite.scss',
    template: './gulp/templates/_sprite.scss'
  },
  fonts: {
    src: './css/style.fonts.css',
    dest: './css'
  },
  images: {
    src: './images/source/**/*',
    dest: './images/generated'
  },
  css: {
    src: ['components/00-theme/**/*.scss',
      'components/01-paragraphs/**/*.scss',
      'components/02-features/f001-news/**/*.scss',
      'components/02-features/f002-blog/**/*.scss',
      'components/02-features/f006-office/**/*.scss',
      'components/02-features/f008-service/**/*.scss',
      'components/02-features/f009-product/**/*.scss',
      'components/02-features/f014-event/**/*.scss',
      '!components/*/00-base/_libs.scss',
      '!components/*/00-base/01-helpers/05-templates/*.scss',
      '!components/*/00-base/04-vendors/**/*.scss',
      '!components/*/**/example-*.scss',
      '!components/**/tpl/**/*.scss',
      '!components/**/css/**/*.scss'
    ],
    dest: 'css',
    base: {
      src: ['components/00-theme/00-base/**/*.scss',
        'components/00-theme/01-atoms/**/*-helpers.scss',
        '!components/*/00-base/_libs.scss',
        '!components/*/00-base/01-helpers/05-templates/*.scss',
        '!components/*/00-base/04-vendors/**/*.scss',
        '!components/*/**/example-*.scss',
        '!components/**/tpl/**/*.scss',
        '!components/**/css/**/*.scss'
      ]
    },
    components: {
      theme: {
        src: ['components/00-theme/**/*.scss',
          '!components/*/00-base/_libs.scss',
          '!components/*/00-base/01-helpers/05-templates/*.scss',
          '!components/*/00-base/04-vendors/**/*.scss',
          '!components/*/**/example-*.scss',
          '!components/**/tpl/**/*.scss',
          '!components/**/css/**/*.scss'
        ]
      },
      paragraphs: {
        src: [
          'components/01-paragraphs/**/*.scss',
          '!components/*/**/example-*.scss'
        ],
      },
      features: {
        src: [
          'components/02-features/f001-news/**/*.scss',
          'components/02-features/f002-blog/**/*.scss',
          'components/02-features/f006-office/**/*.scss',
          'components/02-features/f008-service/**/*.scss',
          'components/02-features/f009-product/**/*.scss',
          'components/02-features/f012-job/**/*.scss',
          'components/02-features/f014-event/**/*.scss',
          '!components/02-features/*/**/example-*.scss'
        ]
      }
    },
    mail: {
      src: ['css/style.mail.css'],
      dest: './'
    },
    editor: {
      src: ['css/style.editor.css'],
      dest: 'css',
    },
  },
  js: {
    src: ['components/00-theme/00-base/**/*.js',
      'components/00-theme/**/*.js',
      'components/01-paragraphs/**/*.js',
      'components/02-features/f001-news/**/*.js',
      'components/02-features/f002-blog/**/*.js',
      'components/02-features/f006-office/**/*.js',
      'components/02-features/f008-service/**/*.js',
      'components/02-features/f009-product/**/*.js',
      'components/02-features/f012-job/**/*.js',
      'components/02-features/f014-event/**/*.js',
      '!components/00-theme/**/*.min.js',
      '!components/00-theme/00-base/**/*.min.js',
      '!components/00-theme/00-base/**/*.js'
    ],
    dest: 'js/min',
    tmp: 'js/tmp',
    global: {
      src: ['components/00-theme/00-base/**/*.js',
        '!components/00-theme/00-base/**/*.min.js'
      ]
    },
    components: {
      src: ['components/00-theme/**/*.js',
        'components/01-paragraphs/**/*.js',
        'components/02-features/f001-news/**/*.js',
        'components/02-features/f002-blog/**/*.js',
        'components/02-features/f006-office/**/*.js',
        'components/02-features/f008-service/**/*.js',
        'components/02-features/f009-product/**/*.js',
        'components/02-features/f012-job/**/*.js',
        'components/02-features/f014-event/**/*.js',
        '!components/00-theme/00-base/**/*.js',
        '!components/00-theme/**/*.min.js'],
      theme: {
        src: ['components/00-theme/**/*.js',
          '!components/00-theme/00-base/**/*.js',
          '!components/00-theme/**/*.min.js']
      },
      paragraphs: {
        src: ['components/01-paragraphs/**/*.js'],
      },
      features: {
        src: ['components/02-features/f001-news/**/*.js',
          'components/02-features/f002-blog/**/*.js',
          'components/02-features/f006-office/**/*.js',
          'components/02-features/f008-service/**/*.js',
          'components/02-features/f009-product/**/*.js',
          'components/02-features/f014-event/**/*.js']
      }
    }
  },
  favicon: {
    datafile: './favicons/generated/faviconData.json',
    master: './favicons/source/favicon.png',
    src: './favicons/source/',
    dest: './favicons/generated/',
    srcico: './favicons/generated/favicon.ico'
  },
  stylelintConfig: {
    // Stylelint plugins
    "plugins": [
    ],
    // Stylelint config rules
    "rules": {
      // -- More can be found at http://stylelint.io/user-guide/rules/
      // -- Colours
      "color-hex-case": "upper",
      "color-no-invalid-hex": true,
      // -- Functions
      "function-calc-no-unspaced-operator": true,
      "function-comma-space-after": "always",
      "function-max-empty-lines": 0,
      "function-name-case": "lower",
      "function-parentheses-space-inside": "never",
      "function-url-quotes": "always",
      // -- Numbers
      "number-no-trailing-zeros": true,
      // - Strings
      "string-quotes": "single",
      // -- Units
      "unit-case": "lower",
      "unit-no-unknown": true,
      // -- Value list
      "value-keyword-case": "lower",
      "value-list-comma-space-after": "always",
      // -- Shorthand property
      "shorthand-property-no-redundant-values": true,
      // -- Property
      "property-case": "lower",
      // -- Declaration block
      "declaration-block-no-duplicate-properties": [ true, {
        "ignore": ["consecutive-duplicates"]
      } ],
      // "declaration-block-no-ignored-properties": true, // deprecated and unreliable
      "declaration-block-no-shorthand-property-overrides": true,
      "declaration-block-semicolon-newline-after": "always",
      "declaration-block-trailing-semicolon": "always",
      // -- Block
      "block-closing-brace-newline-after": [ "always", {
        "ignoreAtRules": ["if", "else", "elseif"]
      } ],
      "block-no-empty": true,
      "block-opening-brace-newline-after": "always",
      "block-opening-brace-space-before": "always",
      // -- Selector
      "selector-combinator-space-after": "always",
      "selector-combinator-space-before": "always",
      "selector-pseudo-class-case": "lower",
      "selector-pseudo-element-case": "lower",
      "selector-pseudo-element-colon-notation": "single",
      "selector-type-case": "lower",
      "selector-max-empty-lines": 0,
      // -- Selector-list
      "selector-list-comma-newline-after": "always",
      // -- Comment
      "comment-whitespace-inside": "always",
      // --  General
      "indentation": 2,
      "max-empty-lines": 2,
      "max-nesting-depth": 7,
      "no-duplicate-selectors": true,
      "no-extra-semicolons": true,
    }
  }
};

exports.config = config;
