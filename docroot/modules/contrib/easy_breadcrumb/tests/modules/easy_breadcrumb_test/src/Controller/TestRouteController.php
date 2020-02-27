<?php

namespace Drupal\easy_breadcrumb_test\Controller;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Controller\ControllerBase;

/**
 * Provides block routines for search server-specific routes.
 */
class TestRouteController extends ControllerBase {

  /**
   * Displays page for testing purposes.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function page() {
    return [
      '#markup' => 'Test Page',
    ];
  }

  /**
   * Returns the page title as FormattableMarkup.
   *
   * Among other places,
   *  used in Drupal\search_api\Controller\IndexController.php.
   *
   * @return \Drupal\Component\Render\FormattableMarkup
   *   The page title.
   */
  public function pageTitleFormattableMarkup() {
    return new FormattableMarkup('Type: %type', ['%type' => FormattableMarkup::class]);
  }

  /**
   * Returns the page title as FormattableMarkup with safe HTML.
   *
   * @return \Drupal\Component\Render\FormattableMarkup
   *   The page title.
   */
  public function pageTitleFormattableMarkupSafeHtml() {
    return new FormattableMarkup('Hello World & "@aliens"', ['@aliens' => 'aliens']);
  }

  /**
   * Returns the page title as FormattableMarkup with unsafe HTML.
   *
   * @return \Drupal\Component\Render\FormattableMarkup
   *   The page title.
   */
  public function pageTitleFormattableMarkupUnsafeHtml() {
    return new FormattableMarkup('Script: @script', ['@script' => '<script>alert(String.fromCharCode(88,83,83)</script>']);
  }

  /**
   * Returns the page title as TranslatableMarkup.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The page title.
   */
  public function pageTitleTranslatableMarkup() {
    return $this->t('TranslatableMarkup');
  }

  /**
   * Returns the page title as FormattableMarkup.
   *
   * @return array
   *   The page title.
   */
  public function pageTitleRender() {
    return [
      '#markup' => 'this is a string',
    ];
  }

  /**
   * Returns the page title as a render array with unsafe HTML.
   *
   * @return array
   *   The page title.
   */
  public function pageTitleRenderUnsafeHtml() {
    return [
      '#markup' => 'this is a string with html: <script>alert("clean me!")</script>',
    ];
  }

  /**
   * Returns the page title as a render array with safe HTML.
   *
   * @return array
   *   The page title.
   */
  public function pageTitleRenderSafeHtml() {
    return [
      '#markup' => 'this is a string with html: <em>leave me</em>',
    ];
  }

}
