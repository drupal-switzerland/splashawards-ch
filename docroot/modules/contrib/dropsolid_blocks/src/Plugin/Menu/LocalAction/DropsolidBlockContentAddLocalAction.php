<?php

namespace Drupal\dropsolid_blocks\Plugin\Menu\LocalAction;

use Drupal\block_content\Plugin\Menu\LocalAction\BlockContentAddLocalAction;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Routing\UrlGeneratorTrait;

/**
 * Modifies the 'Add custom block' local action.
 */
class DropsolidBlockContentAddLocalAction extends BlockContentAddLocalAction {

  use UrlGeneratorTrait;

  /**
   * {@inheritdoc}
   */
  public function getOptions(RouteMatchInterface $route_match) {
    $options = parent::getOptions($route_match);
    // Adds a destination on our own custom block listing under admin/content.
    // Else the redirect goes to instance creation.
    if ($route_match->getRouteName() == 'view.dropsolid_content_block_library.page_1') {
      $options['query']['destination'] = $this->url('<current>');
    }
    return $options;
  }

}
