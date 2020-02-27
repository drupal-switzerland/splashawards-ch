// Global javascript (loaded on all pages in Pattern Lab and Drupal)
// Should be used sparingly because javascript files can be used in components
// See https://github.com/fourkitchens/dropsolid_fix_base_8/wiki/Drupal-Components#javascript-in-drupal for more details on using component javascript in Drupal.

// JavaScript should be made compatible with libraries other than jQuery by
// wrapping it with an "anonymous closure". See:
// - https://drupal.org/node/1446420
// - http://www.adequatelygood.com/2010/3/JavaScript-Module-Pattern-In-Depth

/**
 * Cooldrops UI JS
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

  Drupal.behaviors.dropsolidUITables = {
    attach: function (context, settings) {

      var tables = $('table', context);

      // Make responsive tables
      // in the Sass: choose between 2 types: 'reformatted' or 'scroll'
      if (tables.length) self.setResponsiveTables(tables);

    }
  };

  ///////////////////////////////////////////////////////////////////////
  // Behavior for Tabs: functions
  ///////////////////////////////////////////////////////////////////////

  /**
   * Make tables responsive
   *
   */

  self.setResponsiveTables = function(tables) {

    // Add responsive functionality to tables added via WYSIWYG
    // or otherwise inserted in the page (eg. from Commerce)

    // if coming from CKE
    var ckeClass = '.cke_show_borders, .text-long';

    tables.once('js-once-set-responsive-tables').each(function() {

      var table = $(this);

      if(table.closest('.page').length ) {

        // if already has a responsive class, define the type
        // based on if it's coming from CKE or not

        // if table in CKE, we need to take into account
        // the style that the user has set on it:
        if (table.closest(ckeClass).length) {

          // user has set to be reformatted
          // so wrap in a div with the right classes
          // and fire function
          if (table.hasClass('table--reformatted')) {
            if(!table.closest('div').hasClass('table-responsive')) {
              table.wrap('<div class="table-responsive is-reformatted"></div>');
            }
            self.responsiveTables(table);
          // no class, or scroll class, set that wrapper
          } else {
            if(!table.closest('div').hasClass('table-responsive')) {
              table.wrap('<div class="table-responsive has-scroll"></div>');
            }
          }

        // not coming from CKE,
        // we can't give the user a choice and just do the default:
        // check for a wrapper and add one if there isn't one
        // set class to reformat on mobile
        } else {

          // // if no responsive wrapper, and if in .page,
          // // make a wrapper and set a type
          // //
          // if(!table.closest('div').hasClass('table-responsive')) {
          //   table.wrap('<div class="table-responsive is-reformatted"></div>');
          //   self.responsiveTables(table);
          // }
        }
      }

    });
  };

  self.responsiveTables = function(table) {

    // make data-attributes for CSS to use as headings
    // if th's in thead
    if(table.find('thead').length) {
      var headings = [];
      table.find('th').each(function(){
        headings.push($(this).text());
      });
      var count = 0;
      table.find('tr').each(function(){
        // table.find('td').attr('data-title', headings[count-1]);
        // ++count;
        count = 0;
        $(this).find('td').each(function() {
          $(this).attr('data-title', headings[count]);
          ++count;
        });
      });
    } else {
      // if th's in tbody
      if(table.find('th').length) {
        table.find('tr').each(function(){
          var heading = $(this).find('th').text();
          table.find('td').each(function(){
            table.attr('data-title', heading);
          });
        });
      // if no th's at all, don't need certain styling on mobile
      } else {
        table.addClass('no-th');
      }
    }
  };


})(jQuery, Drupal, window, document);

