<?php

namespace Drupal\dropsolid_blocks\Plugin\Block;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Provides a 'PoweredByDropsolid' block.
 *
 * @Block(
 *  id = "powered_by_dropsolid",
 *  admin_label = @Translation("Powered by Dropsolid"),
 * )
 */
class PoweredByDropsolid extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    $build['#cache']['contexts'] = [
      'languages',
    ];

    $build['powered_by_dropsolid']['#markup'] = $this->t('Powered by Dropsolid');

    return $build;
  }

}
