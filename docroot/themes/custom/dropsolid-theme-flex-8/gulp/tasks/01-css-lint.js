// Lint-scss Task
'use strict';
const gulp = require('gulp'),
    postcss = require('gulp-postcss'),
    reporter = require('postcss-reporter'),
    syntaxSCSS = require('postcss-scss'),
    stylelint = require('stylelint');

const { config } = require('../config');

/**
 * Linting for the Sass files: separate in parallel tasks
 */

// ** All Sass files

gulp.task('css:lint', function () {
  var processors = [
    stylelint(config.stylelintConfig),
    reporter({
      clearMessages: true
    })
  ];

  return gulp.src(config.css.src)
    .pipe(postcss(processors, {syntax: syntaxSCSS}));
});

// ** Separated

gulp.task('css:lint-theme', function () {
  var processors = [
    stylelint(config.stylelintConfig),
    reporter({
      clearMessages: true
    })
  ];

  return gulp.src(config.css.components.theme.src)
    .pipe(postcss(processors, {syntax: syntaxSCSS}));
});

gulp.task('css:lint-paragraphs', function () {
  var processors = [
    stylelint(config.stylelintConfig),
    reporter({
      clearMessages: true
    })
  ];

  return gulp.src(config.css.components.paragraphs.src)
    .pipe(postcss(processors, {syntax: syntaxSCSS}));
});

gulp.task('css:lint-features', function () {
  var processors = [
    stylelint(config.stylelintConfig),
    reporter({
      clearMessages: true
    })
  ];

  return gulp.src(config.css.components.features.src)
    .pipe(postcss(processors, {syntax: syntaxSCSS}));
});

// run the parallel css tasks and bundle in 1 general task

gulp.task('css:lint', gulp.series('css:lint-theme', 'css:lint-paragraphs','css:lint-features'));

/**
 * Exports
 */

exports.cssLint = gulp.series('css:lint-theme', 'css:lint-paragraphs','css:lint-features');
