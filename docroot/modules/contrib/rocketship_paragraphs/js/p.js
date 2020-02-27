/**
 * Rocketship UI JS
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
  if (typeof window.rocketshipUI === 'undefined') { window.rocketshipUI = {}; }

  var self = window.rocketshipUI;

  ///////////////////////////////////////////////////////////////////////
  // Cache variables available across the namespace
  ///////////////////////////////////////////////////////////////////////


  ///////////////////////////////////////////////////////////////////////
  // Behavior for Tabs: triggers
  ///////////////////////////////////////////////////////////////////////

  Drupal.behaviors.rocketshipUI_p = {
    attach: function (context, settings) {

      var groupedParagraphs = $('.group--paragraphs__item', context);

      // add class for paragraphs that border each other and have same bg
      if (groupedParagraphs.length) {
        self.group(groupedParagraphs);
      }

    }
  };

  ///////////////////////////////////////////////////////////////////////
  // Behavior for Tabs: functions
  ///////////////////////////////////////////////////////////////////////

  /**
   * Make sure that paragraphs with the same bg-color
   * don't have too much space, by adding a class we can use for styling overrides/exceptions
   *
   */
  self.group = function(paragraphItems) {

    paragraphItems.once('js-once-paragraphsGroup').each(function() {

      var paragraphItem = $(this);

      paragraphItem.once('js-once-groupedParagraph').each(function() {

        var pIt = $(this),
          p = pIt.find('.paragraph').first(),
          pItNext = pIt.next(),
          pNext = pItNext.find('.paragraph').first();

        var paragraphsHandler = function() {

          // when 2 paragraphs following each other have the same BG color,
          // add classes to reset paddings to avoid 'double padding' perception
          // under specific circumstances
          //
          if (pNext.length && typeof pNext[0] !== 'undefined' && typeof pNext[0].classList !== 'undefined' && pNext[0].classList.length) {

            var pMarginTop = parseInt(p.css('marginTop').replace('px', ''), 10);

            // ONLY IF:
            // - both have same BG color
            // - && neither has a BG image
            // - && both paragraphs do or do not have bg images
            // - && first p has no stretched image/video (this behaves a bit like BG image)
            // - && first p has no bottom margin
            if (!p.hasClass('p--layout--image_stretched') && !p.hasClass('p--layout--video_stretched') && (!p.hasClass('has-bg-image') && !pNext.hasClass('has-bg-image')) && (p.hasClass('has-bg') && pNext.hasClass('has-bg') || !p.hasClass('has-bg') && !pNext.hasClass('has-bg')) && (pMarginTop < 1)) {

              // get classes of both
              var pClassList = p[0].className.split(/\s+/);
              var pNextClassList = pNext[0].className.split(/\s+/);

              // no bg class, so both transparent
              if (!p.hasClass('has-bg') && !pNext.hasClass('has-bg')) {

                pIt.addClass('has-matching-bg').addClass('has-matching-bg-first');
                pItNext.addClass('has-matching-bg').addClass('has-matching-bg-last');

                // if both have a bg color, check that it's the same bg color
              } else {

                // look for any item that contains 'bg--' and is same in both paragraphs
                for (var i = 0; i < pClassList.length; ++i) {

                  for (var j = 0; j < pNextClassList.length; ++j) {

                    if (pClassList[i] === pNextClassList[j]) {

                      if (pClassList[i].indexOf('bg--') !== -1) {
                        pIt.addClass('has-matching-bg').addClass('has-matching-bg-first');
                        pItNext.addClass('has-matching-bg').addClass('has-matching-bg-last');
                      }
                    }

                  }

                }
              }

            }
          }
        };

        paragraphsHandler();

        rocketshipUI.optimizedResize().add(function() {

          paragraphsHandler();

        });

      });

    });

  };


  /**
   * Since resize events can fire at a high rate,
   * the event handler shouldn't execute computationally expensive operations
   * such as DOM modifications.
   * Instead, it is recommended to throttle the event using requestAnimationFrame,
   * setTimeout or customEvent
   *
   * Src: https://developer.mozilla.org/en-US/docs/Web/Events/resize
   *
   * Example:
   *
   * window.dropsolidUI.optimizedResize().add(function() {
   *   console.log('Resource conscious resize callback!')
   * });
   */
  self.optimizedResize = function() {

    var callbacks = [],
      running = false;

    // Fired on resize event
    function resize() {
      if (!running) {
        running = true;
        if (window.requestAnimationFrame) {
          window.requestAnimationFrame(runCallbacks);
        }
        else {
          setTimeout(runCallbacks, 250);
        }
      }
    }

    // Run the actual callbacks
    function runCallbacks() {
      callbacks.forEach(function(callback) {
        callback();
      });
      running = false;
    }

    // Adds callback to loop
    function addCallback(callback) {
      if (callback) {
        callbacks.push(callback);
      }
    }
    return {
      // Public method to add additional callback
      add: function(callback) {
        if (!callbacks.length) {
          window.addEventListener('resize', resize);
        }
        addCallback(callback);
      }
    };
  };


  /**
   * Detect if all the images withing your object are loaded
   *
   * No longer needs imagesLoaded plugin to work
   */
  self.imgLoaded = function (el, callback)
  {
    var img = el.find('img'),
      iLength = img.length,
      iCount = 0;

    if (iLength) {

      img.each(function() {

        var img = $(this);

        // fires after images are loaded (if not cached)
        img.on('load', function(){

          iCount = iCount + 1;

          if (iCount == iLength) {
            // all images loaded so proceed
            callback();
          }

        }).each(function() {
          // in case images are cached
          // re-enter the load function in order to get to the callback
          if (this.complete) {

            var url = img.attr('src');

            $(this).load(url);

            iCount = iCount + 1;

            if (iCount == iLength) {
              // all images loaded so proceed
              callback();
            }

          }
        });

      });

    } else {
      // no images, so we can proceed
      return callback();
    }
  };

})(jQuery, Drupal, window, document);
