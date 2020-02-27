/**
  * Adds a font-size switcher
  * See the following change record for more information,
  * https://www.drupal.org/project/claro/issues/3067208
  **/
(function ($, Drupal) {
  Drupal.behaviors.sizeSwitcher = {
    attach: function attach(context) {

      var $html = $('html', context);
        $sizeSwitcher = $('.size-switcher', context);

      // add size switcher if doesn't exist yet
        if ($sizeSwitcher.length) {
        setSwitcher($sizeSwitcher);
        setSizing();
      }

      function setSwitcher($sizeSwitcher) {

        var $links = $sizeSwitcher.find('a');

        $links.once('size-switcher-links').each(function() {

          var $link = $(this);

          $link.on('click', function () {

            // add a class based on a data-attr
            var $clickedLink = $(this),
                $size = $clickedLink.data('size');

            setSizing($size);

          });

        });

      }

      function setSizing(value) {

        var $sizeSwitcher = $('.size-switcher', context),
            $links = $sizeSwitcher.find('a');

        // if no value passed to this function
        if (typeof value === 'undefined') {

          // try to get value from localStorage
          // or else leave the default
          var storedSize = localStorage.getItem('claro-size');

          if (storedSize !== null || typeof storedSize === 'undefined') {
            value = storedSize;
          } else {
            value = 'default';
          }
        }

        // set a class on the html + remove the other size-related classes
        $html.removeClass (function (index, className) {
          return (className.match (/(^|\s)claro-size--\S+/g) || []).join(' ');
        });

        // add value as a class
        $html.addClass('claro-size--' + value);

        // store it for later reference
        localStorage.setItem('claro-size', value);

        // set an active class on the switcher link that matches the value
        // but remove on the others
        var newActiveLink = $sizeSwitcher.find('a[data-size="' + value + '"]');

        newActiveLink.addClass('js-active');
        newActiveLink.closest('li').siblings().find('a').removeClass('js-active')

      }
    }
  };
})(jQuery, Drupal);
