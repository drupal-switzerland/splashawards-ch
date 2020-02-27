// Concatenate & Minify JS
'use strict';
const gulp = require('gulp'),
    sourcemaps = require('gulp-sourcemaps'),
    concat = require('gulp-concat'),
    rename = require('gulp-rename'),
    flatten = require('gulp-flatten'),
    uglify = require('gulp-uglify'),
    gulpif = require('gulp-if'),
    notify = require('gulp-notify'),
    bs = require('browser-sync').get('bs'),
    pump = require('pump');

const { config } = require('../config');
const { errorNotification } = require('./00-setup');

const options = {};


/**
 *
 * functions for compiling JS per part
 */

// Development

const buildJsComponentsDev = function (done) {

  const mySrc = config.js.components.src,
    myDest = config.js.dest;

  return gulp.src(mySrc)
    .pipe(sourcemaps.init())
    .on('error', function (err) {
      errorNotification(this, err);
    })
    .pipe(flatten({ includeParents: 0}))

    .pipe(gulp.dest(config.js.tmp))

    .pipe(rename({
      suffix: '.min'
    }))
    .pipe(uglify())
    .on('error', function (err) {
      errorNotification(this, err);
    })
    .pipe(
      sourcemaps.write('./' + myDest, {
        sourceMappingURL: function (file) {
          // We add a timestamp for cachebusting
          return file.relative + '.map?build=' + new Date().getTime();
        }
      })
    )

    .pipe(flatten())

    .pipe(gulp.dest(myDest))

    // .on('end', function() {
    //   // notify().write('js:components update, reloading browser');
    //   livereload.reload();
    // });

    // .pipe(bs.stream({match: '**/*.js'}))

    .pipe(gulpif(config.arg.url !== false, bs.stream({match: '**/*.js'})));
};

const buildJsGlobalDev = function (done) {

  const mySrc = config.js.global.src,
        myDest = config.js.dest;

  pump([

    gulp.src(mySrc),

    sourcemaps.init().on('error', function (err) {
      // console.error('Error in compress task', err.toString());
      errorNotification(this, err);
    }),

    // gulpif(env === 'dev' || env === 'local',
    //   sourcemaps.init()
    // ).on('error', function (err) {
    //   // console.error('Error in compress task', err.toString());
    //   errorNotification(this, err);
    // }),

    concat('scripts.js', options).on('error', function (err) {
      // console.error('Error in compress task', err.toString());
      errorNotification(this, err);
    }),

    gulp.dest(config.js.tmp),

    rename({
      suffix: '.min'
    }).on('error', function (err) {
      // console.error('Error in compress task', err.toString());
      errorNotification(this, err);
    }),

    uglify().on('error', function (err) {
      // console.error('Error in compress task', err.toString());
      errorNotification(this, err);
    }),

    // gulpif(env === 'prod' || env === 'local',
    //   uglify()
    // ).on('error', function (err) {
    //   // console.error('Error in compress task', err.toString());
    //   errorNotification(this, err);
    // }),

    sourcemaps.write('.', {
      sourceMappingURL: function (file) {
        // We add a timestamp for cachebusting
        return file.relative + '.map?build=' + new Date().getTime();
      }
    }).on('error', function (err) {
      // console.error('Error in compress task', err.toString());
      errorNotification(this, err);
    }),

    // gulpif(env === 'dev' || env === 'local',
    //   sourcemaps.write('.', {
    //     sourceMappingURL: function (file) {
    //       // We add a timestamp for cachebusting
    //       return file.relative + '.map?build=' + new Date().getTime();
    //     }
    //   })
    // ).on('error', function (err) {
    //   // console.error('Error in compress task', err.toString());
    //   errorNotification(this, err);
    // }),

    gulp.dest(myDest)
      .on('error', function (err) {
        // console.error('Error in compress task', err.toString());
        errorNotification(this, err);
      }),
    // .on('end', function() {
    //   // notify().write('js:global update, reloading browser');
    //   livereload.reload();
    // })

    gulpif(config.arg.url !== false, bs.stream({match: '**/*.js'}))

  ]);

  done();
};


// Production

const buildJsComponentsProd = function (done) {

  const mySrc = config.js.components.src,
        myDest = config.js.dest;

  return gulp.src(mySrc)

    .pipe(flatten({ includeParents: 0}))

    .pipe(gulp.dest(config.js.tmp))

    .pipe(rename({
      suffix: '.min'
    }))
    .pipe( uglify())

    .pipe(flatten())

    .pipe(gulp.dest(myDest));
};

const buildJsGlobalProd = function (done) {

  const mySrc = config.js.global.src,
        myDest = config.js.dest;

  pump([

      gulp.src(mySrc),

      concat('scripts.js', options),

      gulp.dest(config.js.tmp),

      rename({
        suffix: '.min'
      }),

      uglify(),

      gulp.dest(myDest)

    ]
  );

  done();
};

/**
 * Components
 */


gulp.task('js:components:dev', function(done) {

  return buildJsComponentsDev(done);

});

gulp.task('js:components:prod', function(done) {

  return buildJsComponentsProd(done);

});


/**
 * Global
 */

gulp.task('js:global:dev', function (done) {

  return buildJsGlobalDev(done);

});

gulp.task('js:global:prod', function (done) {

  return buildJsGlobalProd(done);

});

/**
 * Combined tasks
 */

gulp.task('js:dev', gulp.series('js:global:dev', 'js:components:dev'));
gulp.task('js:prod', gulp.series('js:global:prod', 'js:components:prod'));

/**
 * Exports
 */

exports.buildJsGlobalDev = buildJsGlobalDev;
exports.buildJsComponentsDev = buildJsComponentsDev;

exports.jsDev = gulp.series('js:global:dev', 'js:components:dev');
exports.jsProd = gulp.series('js:global:prod', 'js:components:prod');
