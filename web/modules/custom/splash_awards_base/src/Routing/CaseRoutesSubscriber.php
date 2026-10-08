<?php

namespace Drupal\splash_awards_base\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events for case management to restrict access.
 *
 * This makes sure that only users with the 'administrator' or 'content_editor'
 * roles can access the "Add case" and "Edit case" routes intended for the
 * backend/administration of cases.
 */
class CaseRoutesSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection): void {
    // Restrict the "Add case" route for non-admin users.
    if ($route = $collection->get('node.add')) {
      $route->setRequirement('_custom_access', 'splash_awards_base.access_check::access');
    }

    // Restrict the "Edit case" route for non-admin users.
    if ($route = $collection->get('entity.node.edit_form')) {
      $route->setRequirement('_custom_access', 'splash_awards_base.access_check::access');
    }
  }

}
