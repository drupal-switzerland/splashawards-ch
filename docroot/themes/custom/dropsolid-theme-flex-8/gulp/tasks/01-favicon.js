// Favicon generation task
'use strict';

const gulp = require('gulp'),
    fs = require('fs'),
    notify = require('gulp-notify'),
    sizeOf = require('image-size'),
    realFavicon = require('gulp-real-favicon');

const { config } = require('../config');

const masterPicture = config.favicon.master;

// File where the favicon markups are stored
const FAVICON_DATA_FILE = config.favicon.datafile; //'./favicons/generated/faviconData.json';

const copyManifest = function() {

  notify().write('copy manifest.json from source to generated');

  return gulp.src(config.favicon.src + 'manifest.json')
    .pipe(gulp.dest(config.favicon.dest));
};

gulp.task('favicon', function (done) {

  // Check if the masterPicture exists
  fs.stat(masterPicture, function (err, stat) {

    if (err == null) {

      notify().write('Generating favicons… (this may take a while)');

      // The file exists but check if it meets our minimum dimensions
      var dimensions = sizeOf(masterPicture);

      if (dimensions.width == dimensions.height && dimensions.height > 249) {
        // The dimensions are correct so do your magic
        realFavicon.generateFavicon({
          masterPicture: masterPicture,
          dest: config.favicon.dest,
          iconsPath: '%path%',
          design: {
            ios: {
              pictureAspect: 'backgroundAndMargin',
              backgroundColor: '#ffffff',
              margin: '18%'
            },
            desktopBrowser: {},
            windows: {
              pictureAspect: 'whiteSilhouette',
              backgroundColor: '#2b5797',
              onConflict: 'override'
            },
            androidChrome: {
              pictureAspect: 'noChange',
              themeColor: '#ffffff',
              manifest: {
                name: '%sitename%',
                display: 'browser',
                orientation: 'notSet',
                onConflict: 'override',
                declared: true
              }
            },
            safariPinnedTab: {
              pictureAspect: 'silhouette',
              themeColor: '#5bbad5'
            }
          },
          settings: {
            scalingAlgorithm: 'Mitchell',
            errorOnImageTooSmall: true
          },
          markupFile: FAVICON_DATA_FILE
        }, function () {
          // Move the ico file to the root of the theme
          gulp.src(config.favicon.srcico)
            .pipe(gulp.dest('./'));
          done();
        });
      }
      else {
        // The dimensions are incorrect
        notify().write('The dimensions of the source image are incorrect. (must be square and bigger than 249px)');
      }
    }
    else {
      notify().write('Please add the favicon.png to source folder');
    }
  });

  copyManifest();

  // done();

});



/**
 * Exports
 */

exports.favicon = 'favicon';
