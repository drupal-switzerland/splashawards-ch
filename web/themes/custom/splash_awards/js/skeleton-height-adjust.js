(function ($, Drupal) {

  Drupal.behaviors.componentHeightAdjust = {
    attach(context) {
      const stickyContainer = document.getElementById('skeleton-utils-sticky-container');
      const skeletonUtils = document.getElementById('skeleton-utils');
      const skeletonContent = document.getElementById('skeleton-content');

      if (!stickyContainer || !skeletonUtils || !skeletonContent) {
        return;
      }

      const adjustHeightAndSticky = () => {
        if (skeletonContent.offsetHeight <= skeletonUtils.offsetHeight) {
          skeletonContent.style.minHeight = skeletonUtils.offsetHeight + 'px';
          stickyContainer.classList.remove('sticky');
        } else {
          if (window.innerWidth >= 640) {
            stickyContainer.classList.add('sticky');
          } else {
            stickyContainer.classList.remove('sticky');
          }
        }
      };

      // Run initially.
      adjustHeightAndSticky();

      // Bind resize listener once per page load.
      if (!context.__skeletonHeightAdjustResizeBound) {
        window.addEventListener('resize', adjustHeightAndSticky);
        context.__skeletonHeightAdjustResizeBound = true;
      }
    }
  };

})(jQuery, Drupal);
