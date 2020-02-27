// Global javascript (loaded on all pages in Pattern Lab and Drupal)
// Should be used sparingly because javascript files can be used in components
// See https://github.com/fourkitchens/dropsolid_fix_base_8/wiki/Drupal-Components#javascript-in-drupal for more details on using component javascript in Drupal.

// JavaScript should be made compatible with libraries other than jQuery by
// wrapping it with an "anonymous closure". See:
// - https://drupal.org/node/1446420
// - http://www.adequatelygood.com/2010/3/JavaScript-Module-Pattern-In-Depth

/**
 * Dropsolid UI JS
 *
 * contains: triggers for functions
 * Functions themselves are split off and grouped below each behavior
 *
 * Drupal behaviors:
 *
 * Means the JS is loaded when page is first loaded
 * + during AJAX requests (for newly added content)
 * use jQuery's "once" to avoid processing the same element multiple times
 * http: *api.jquery.com/one/
 * use the "context" param to limit scope, by default this will return document
 * use the "settings" param to get stuff set via the theme hooks and such.
 *
 *
 * Avoid multiple triggers by using jQuery Once
 *
 * EXAMPLE 1:
 *
 * $('.some-link', context).once('js-once-my-behavior').click(function () {
 *   // Code here will only be applied once
 * });
 *
 * EXAMPLE 2:
 *
 * $('.some-element', context).once('js-once-my-behavior').each(function () {
 *   // The following click-binding will only be applied once
 * * });
 */

(function ($, Drupal, window, document) {

  "use strict";

  // set namespace for frontend UI javascript
  if (typeof window.dropsolidUI == 'undefined') { window.dropsolidUI = {}; }

  var self = window.dropsolidUI;

  ///////////////////////////////////////////////////////////////////////
  // Cache variables available across the namespace
  ///////////////////////////////////////////////////////////////////////


  ///////////////////////////////////////////////////////////////////////
  // Behavior for Tabs: triggers
  ///////////////////////////////////////////////////////////////////////

  Drupal.behaviors.dropsolidUICountdown = {
    attach: function (context, settings) {

      var timer = $('.block--type-countdown');

      if (timer.length) {

        self.countdown(timer);
      }

    }
  };

  ///////////////////////////////////////////////////////////////////////
  // Behavior for Tabs: functions
  ///////////////////////////////////////////////////////////////////////

  /**
   * Countdown to specific date and time
   * by comparing current datetime with a date set in the future
   * uses a timezone and date field
   */
  self.countdown = function(timer) {

    timer.once('js-countdown-timer').each(function() {

      var timer = $(this);

      var fieldDays = timer.find('.field--name-field-days .countdown__number');
      var fieldHours = timer.find('.field--name-field-hours .countdown__number');
      var fieldMinutes = timer.find('.field--name-field-minutes .countdown__number');
      var endDate = timer.find('.field--name-field-end-date .countdown__number').text();
      var timezone = timer.find('.field--name-field-timezone .countdown__number').text();

      // we need the Amsterdam timezone in order to be correct
      var endDateMs = moment(endDate).tz(timezone);

      // show time left in front-end
      self.timeLeft(endDateMs, timezone, fieldDays, fieldHours, fieldMinutes);

      // update timer in frontend every minute
      self.runCountdown(endDateMs, timezone, fieldDays, fieldHours, fieldMinutes);
    });

  };

  self.timeLeft = function(endDateMs, timezone, fieldDays, fieldHours, fieldMinutes) {

    var currentDateMs = moment().tz(timezone);

    var daysDivider = (1000 * 60 * 60 * 24);
    var hoursDivider = (1000 * 60 * 60);
    var minutesDivider = (1000 * 60);

    var countdownDiff = (endDateMs - currentDateMs);

    if (countdownDiff > 0) {

      // days
      var countdownDays = Math.floor(countdownDiff / daysDivider);
      var daysRest = countdownDiff - (countdownDays * daysDivider);

      var countdownHours =  Math.floor(daysRest  / hoursDivider);
      var hoursRest = daysRest - (countdownHours * hoursDivider);

      var countdownMinutes =  Math.floor(hoursRest  / minutesDivider);
      var minutesRest = hoursRest - (countdownMinutes * hoursDivider);

      fieldDays.text(countdownDays);
      fieldHours.text(countdownHours);
      fieldMinutes.text(countdownMinutes);

    } else {

      fieldDays.text(0);
      fieldHours.text(0);
      fieldMinutes.text(0);

    }

  };

  self.runCountdown = function(endDateMs, timezone, fieldDays, fieldHours, fieldMinutes) {

    var timer = setInterval(function () {

      clearInterval(timer);

      // calc and update
      self.timeLeft(endDateMs, timezone, fieldDays, fieldHours, fieldMinutes);

      var currentDateMs = moment().tz(timezone);
      var countdownDiff = (endDateMs - currentDateMs);

      if (countdownDiff > 0) {
        // loop again
        self.runCountdown(endDateMs, timezone, fieldDays, fieldHours, fieldMinutes);
      }

    }, 60000);

  };

})(jQuery, Drupal, window, document);
