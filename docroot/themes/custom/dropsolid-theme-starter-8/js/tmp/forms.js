/**
 * Cooldrops UI JS: Forms
 *
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
  // Behavior for Forms: triggers
  ///////////////////////////////////////////////////////////////////////

  Drupal.behaviors.dropsolidUIForms = {
    attach: function (context, settings) {

      var input = $('input', context),
          select = $('select', context),
          textarea = $('textarea', context);

      // handle focus on form elements
      if (input.length || textarea.length) self.focusFields(input, textarea, select);

      // wrap select tag to add custom styling and arrow
      if (select.length) self.customSelect(select);
    }
  };

  ///////////////////////////////////////////////////////////////////////
  // Behavior for Forms: functions
  ///////////////////////////////////////////////////////////////////////

  /**
   * Add focus styles to form elements
   *
   */
  self.focusFields = function (input, textarea, select) {

    // if input exists
    if (input.length) {
      // iterate all inputs
      input.once('js-once-input').each(function() {
        var input = $(this);

        var wrapper = '.form-item';

        if ( input.closest(wrapper).length < 1 ) {
          wrapper = '.form-wrapper';
        }

        // add an active class on wrapper and label if focus
        input.focus(function(e) {
          var input = $(this);
          input.closest(wrapper).addClass('is-active').find('label').addClass('is-active');

          // some forms don't have wrappers with a class,
          // just use parent instead
          if (input.attr('name') == 'search_block_form') {
            input.parent().addClass('is-active').find('label').addClass('is-active');
          }

        // remove active class on blur
        }).blur(function(e) {
          var input = $(this);

          input.closest(wrapper).removeClass('is-active').find('label').removeClass('is-active');
          if (input.attr('name') == 'search_block_form') {
            input.parent().removeClass('is-active').find('label').removeClass('is-active');
          }

          // if field has a value, add a has-value class (handy for floating labels)
          if (input.val()) {
            input.closest(wrapper).addClass('has-value').find('label').addClass('has-value');
            if (input.attr('name') == 'search_block_form') {
              input.parent().addClass('has-value').find('label').addClass('has-value');
            }
          // remove value class if empty
          } else {
            input.closest(wrapper).removeClass('has-value').find('label').removeClass('has-value');
            if (input.attr('name') == 'search_block_form') {
              input.parent().removeClass('has-value').find('label').removeClass('has-value');
            }
          }
        });

        // if field has a value, add a has-value class (handy for floating labels)
        if (input.val()) {
          input.closest(wrapper).addClass('has-value').find('label').addClass('has-value');
          if (input.attr('name') == 'search_block_form') {
            input.parent().addClass('has-value').find('label').addClass('has-value');
          }
        }
      });
    }

    if (textarea.length) {
      textarea.once('js-once-textarea').each(function() {
        var textarea = $(this);

        var wrapper = '.form-item';

        if ( textarea.closest(wrapper).length < 1 ) {
          wrapper = '.form-wrapper';
        }

        // add an active class on wrapper and label if focus
        textarea.focus(function(e) {
          var textarea = $(this);
          textarea.closest(wrapper).addClass('is-active').find('label').addClass('is-active');

        // remove active class on blur
        }).blur(function(e) {
          var textarea = $(this);
          textarea.closest(wrapper).removeClass('is-active').find('label').removeClass('is-active');

          // if textarea has a value, add a has-value class (handy for floating labels)
          if (textarea.val()) {
            textarea.closest(wrapper).addClass('has-value').find('label').addClass('has-value');
          // remove value class if empty
          } else {
            textarea.closest(wrapper).removeClass('has-value').find('label').removeClass('has-value');
          }
        });

        // if field has a value, add a has-value class (handy for floating labels)
        if (textarea.val()) {
          textarea.closest(wrapper).addClass('has-value').find('label').addClass('has-value');
        }
      });
    }

    if (select.length) {
      select.once('js-once-select').each(function() {

        var select = $(this);

        var wrapper = '.form-item';

        if ( select.closest(wrapper).length < 1 ) {
          wrapper = '.form-wrapper';
        }

        select.focus(function(e) {
          var select = $(this);
          select.parent().parent().find('label').addClass('is-active');

        }).blur(function(e) {
          var select = $(this);
          select.closest(wrapper).removeClass('is-active').find('label').removeClass('is-active');

          if (select.val()) {

            console.log(select.val());

            select.closest(wrapper).addClass('has-value').find('label').addClass('has-value');
          } else {
            select.closest(wrapper).removeClass('has-value').find('label').removeClass('has-value');
          }
        });

        if (select.val()) {

          // if value is not a string but data of some sort, check for it to be filled
          if ((typeof select.val() === 'object' && select.val().length) > 0) {

            select.closest(wrapper).addClass('has-value').find('label').addClass('has-value');
          }

          // if value as a string
          if (typeof select.val() !== 'object') {

            select.closest(wrapper).addClass('has-value').find('label').addClass('has-value');
          }

        }
      });
    }
  };

  /*
   * wrap select in order to create custom styling for arrow and such
   */
  self.customSelect = function (select) {
    select.once('js-once-select-wrap').each(function() {
      if($(this).closest('.form-item__dropdown').length < 1) {
        $(this).wrap('<div class="form-item__dropdown"></div>');
      }
    });
  };

})(jQuery, Drupal, window, document);
