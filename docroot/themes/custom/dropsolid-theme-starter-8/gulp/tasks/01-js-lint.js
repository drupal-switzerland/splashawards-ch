// Lint-js Task
'use strict';
const gulp = require('gulp'),
    jshint = require('gulp-jshint');

const { config } = require('../config');

function jsLint() {
  return gulp.src(config.js.src) // 'js/*.js'
    .pipe(jshint())
    .pipe(jshint.reporter('default'));
}

gulp.task('js:lint', function () {
  return jsLint();
});

exports.jsLint = jsLint;
