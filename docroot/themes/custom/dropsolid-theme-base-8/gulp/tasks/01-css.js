// Compile Our Sass
'use strict';
const gulp = require('gulp'),
    sass = require('gulp-sass'),
    rename = require('gulp-rename'),
    flatten = require('gulp-flatten'),
    sourcemaps = require('gulp-sourcemaps'),
    // concat = require('gulp-concat'),
    autoprefixer = require('gulp-autoprefixer'),
    sassGlob = require('gulp-sass-glob'),
    cleanCSS = require('gulp-clean-css'),
    gulpif = require('gulp-if'),
    notify = require('gulp-notify'),
    bs = require('browser-sync').get('bs');

const { config } = require('../config');
const { errorNotification } = require('./00-setup');

/**
 *
 * functions for compiling css per part
 */
function buildCSSProd(mySrc, myDest) {

  return gulp.src( mySrc )
    // Use globbing so we don't need to manually add the imports
    .pipe(sassGlob())

    .pipe(sass({
      outputStyle: 'expanded'
    }))

    .pipe(autoprefixer({
        browsers: ['> 1%', 'last 3 versions', 'IE > 10'],
        cascade: false,
        grid: false
    }))

    .pipe(cleanCSS({compatibility: '*'})) //'*' = default, includes IE10+

    .pipe(flatten())

    .pipe(gulp.dest(myDest));
}

function buildCSSDev(mySrc, myDest) {

  return gulp.src( mySrc )

  // Use globbing so we don't need to manually add the imports
    .pipe(sassGlob().on('error', function (err) {
      // Message errors to Mac OS X, Linux or Windows
      notify().write(err.formatted);
      this.emit('end');
    }))

    // add sourcemaps
    .pipe(sourcemaps.init({
      // largeFile: true,
    }))

    // An identity sourcemap will be generated at this step
    .pipe(sourcemaps.identityMap())

    .pipe(sass({
      outputStyle: 'expanded' // don't minify here because errors with maps
    }).on('error', function (err) {
      errorNotification(this, err);
    }))

    // prefixes IE10 and higher
    .pipe(autoprefixer({
      browsers: ['> 1%', 'last 3 versions', 'IE > 10'],
      cascade: false,
      grid: false
    }).on('error', function (err) {
      errorNotification(this, err);
    }))

    .pipe(
      cleanCSS({compatibility: '*'})
    ) //'*' = default, includes IE10+

    // .pipe(gulpif(env === 'dev' || env === 'local',
    //   sourcemaps.mapSources(function(sourcePath, file) {
    //     // notify().write(sourcePath);
    //     // notify().write(file);
    //     return '' + sourcePath;
    //   })
    // ))

    .pipe(
      sourcemaps.write('./' + myDest, {
        // includeContent: false,
        // sourceRoot: '.',
        sourceMappingURL: function (file) {
          // We add a timestamp for cachebusting
          return file.relative + '.map?build=' + new Date().getTime();
        }
      }).on('error', function (err) {
        errorNotification(this, err);
      })
    )

    .pipe(flatten())

    .pipe(gulp.dest(myDest))

    .pipe(
      gulpif(
        config.arg.url !== false,
        bs.stream({match: '**/*.css'})
      )
    );

}

/**
 * Separate tasks so we can run them async
 */

// -- for development

const buildCSSThemeDev = function() {
  return buildCSSDev(config.css.components.theme.src, config.css.dest);
};

const buildCSSParagraphsDev = function() {
  return buildCSSDev(config.css.components.paragraphs.src, config.css.dest);
};

const buildCSSFeaturesDev = function() {
  return buildCSSDev(config.css.components.features.src, config.css.dest);
};

gulp.task('css:theme:dev', function () {
  return buildCSSThemeDev();
});

gulp.task('css:paragraphs:dev', function () {
  return buildCSSParagraphsDev();
});

gulp.task('css:features:dev', function () {
  return buildCSSFeaturesDev();
});

// -- for production

gulp.task('css:theme:prod', function () {
  return buildCSSProd(config.css.components.theme.src, config.css.dest);
});

gulp.task('css:paragraphs:prod', function () {
  return buildCSSProd(config.css.components.paragraphs.src, config.css.dest);
});

gulp.task('css:features:prod', function () {
  return buildCSSProd(config.css.components.features.src, config.css.dest);
});

/**
 * bundle the css tasks into 1 big task
 */

gulp.task('css:dev', gulp.series('css:theme:dev', 'css:paragraphs:dev','css:features:dev'));
gulp.task('css:prod', gulp.series('css:theme:prod', 'css:paragraphs:prod','css:features:prod'));


/**
 *  Copy & rename the css-files needed for mail and editor
 *  the 'watch' task should also listen to changes in that src,
 *  in order to properly update properly
 */

const cssMail = function() {
  return gulp.src(config.css.mail.src)
    .pipe(rename('mail.css'))
    .pipe(gulp.dest(config.paths.base));
};

const cssEditor = function() {
  return gulp.src(config.css.editor.src)
    .pipe(rename('editor.css'))
    .pipe(gulp.dest(config.css.dest));
};

gulp.task('css:mail', function () {
  return cssMail();
});

gulp.task('css:editor', function () {
  return cssEditor();
});


/**
 * Exports
 */

exports.cssDev = gulp.series('css:theme:dev', 'css:paragraphs:dev','css:features:dev');
exports.cssProd = gulp.series('css:theme:prod', 'css:paragraphs:prod','css:features:prod');;

exports.buildCSSThemeDev = buildCSSThemeDev;
exports.buildCSSParagraphsDev = buildCSSParagraphsDev;
exports.buildCSSFeaturesDev = buildCSSFeaturesDev;

exports.cssMail = cssMail;
exports.cssEditor = cssEditor;
