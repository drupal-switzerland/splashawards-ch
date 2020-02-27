'use strict';

const gulp = require('gulp'),
    notify = require('gulp-notify'),
    imagemin = require('gulp-imagemin'),
    svgo = require('imagemin-svgo');

const { config } = require('../config');
const { errorNotification } = require('./00-setup');

const $ = {
  gutil: require('gulp-util'),
  svgSprite: require('gulp-svg-sprite'),
  size: require('gulp-size'),
};


const iconsSprite = function() {
  return gulp.src(config.sprite.src)
    .pipe($.svgSprite({
      shape: {
        spacing: {
          padding: [0, 10, 10, 0]
        },
        dimension: {
          precision: 0
        }
      },
      mode: {
        css: {
          dest: './',
          layout: 'diagonal',
          sprite: config.sprite.svg,
          bust: false,
          render: {
            scss: {
              dest: config.sprite.css,
              template: config.sprite.template
            }
          }
        }
      },
      variables: {
        mapname: 'icons'
      }
    }))
    .pipe(imagemin([
      imagemin({
        plugins: [
          svgo(
            { removeUselessDefs: false },
            { cleanupIDs: false },
            { removeXMLNS: false },
            { removeViewBox: false }
          )
        ]
      }),
    ]))
    .on('error', function (err) {
      return errorNotification(this, err);
    })
    .pipe(gulp.dest('./'));
};


/**
 * This task generates a sprite and puts it in images
 *
 */
gulp.task('icons:sprite', function () {
  return iconsSprite();
});


/**
 * Exports
 *
 */
exports.iconsSprite = iconsSprite;
