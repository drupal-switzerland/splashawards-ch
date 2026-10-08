<?php

namespace Drupal\splash_awards_base\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultAllowed;
use Drupal\Core\Access\AccessResultForbidden;
use Drupal\Core\Routing\Access\AccessInterface;
use Symfony\Component\Routing\Route;
use Drupal\Core\Session\AccountInterface;

/**
 * Class RoleAccessCheck.
 *
 * Provides access control based on user roles for specific routes.
 *
 * This class checks if the user has one of the allowed roles
 * to access certain routes, such as adding or editing cases.
 */
class RoleAccessCheck implements AccessInterface {

  /**
   * Checks access based on the account's roles.
   */
  public function access(Route $route, AccountInterface $account): AccessResultForbidden|AccessResultAllowed {
    $allowed_roles = ['administrator', 'content_editor'];
    foreach ($account->getRoles() as $role) {
      if (in_array($role, $allowed_roles, TRUE)) {
        return AccessResult::allowed();
      }
    }
    return AccessResult::forbidden();
  }

}
