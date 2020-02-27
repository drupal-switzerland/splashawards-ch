<?php

namespace Drupal\Tests\easy_breadcrumb\Kernel;

use Drupal\Core\Routing\RequestContext;
use Drupal\Core\Routing\RouteMatch;
use Drupal\Core\Url;
use Drupal\easy_breadcrumb\EasyBreadcrumbBuilder;
use Drupal\KernelTests\KernelTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

/**
 * Tests the easy breadcrumb builder.
 *
 * @group easy_breadcrumb
 */
class EasyBreadcrumbBuilderTest extends KernelTestBase {

  public static $modules = ['easy_breadcrumb', 'system', 'easy_breadcrumb_test'];

  /**
   * Tests the front page with an invalid path.
   */
  public function testFrontpageWithInvalidPaths() {
    \Drupal::configFactory()->getEditable('easy_breadcrumb.settings')
      ->set('include_invalid_paths', TRUE)
      ->set('include_title_segment', TRUE)
      ->save();
    \Drupal::configFactory()->getEditable('system.site')
      ->set('page.front', '/path')
      ->save();

    $request_context = new RequestContext();

    $breadcrumb_builder = new EasyBreadcrumbBuilder($request_context,
      \Drupal::service('access_manager'),
      \Drupal::service('router'),
      \Drupal::service('request_stack'),
      \Drupal::service('path_processor_manager'),
      \Drupal::service('config.factory'),
      \Drupal::service('title_resolver'),
      \Drupal::service('current_user'),
      \Drupal::service('path.current'),
      \Drupal::service('plugin.manager.menu.link'),
      \Drupal::service('language_manager'),
      \Drupal::service('entity_type.manager'),
      \Drupal::service('entity.repository'),
      \Drupal::service('logger.factory'),
      \Drupal::service('messenger')
    );

    $route_match = new RouteMatch('test_front', new Route('/front'));
    $result = $breadcrumb_builder->build($route_match);
    $this->assertCount(0, $result->getLinks());
  }

  /**
   * Provides data for the get title string test.
   */
  public function providerTestGetTitleString() {
    return [
      'string' => [
        'route' => 'easy_breadcrumb_test.title_string',
        'expected' => 'Easy Breadcrumb',
      ],
      'formattable_markup' => [
        'route' => 'easy_breadcrumb_test.title_formattable_markup',
        'expected' => 'Type: <em class="placeholder">Drupal\Component\Render\FormattableMarkup</em>',
      ],
      'formattable_markup_safe_html' => [
        'route' => 'easy_breadcrumb_test.title_formattable_markup_safe_html',
        'expected' => 'Hello World & "aliens"',
      ],
      'formattable_markup_unsafe_html' => [
        'route' => 'easy_breadcrumb_test.title_formattable_markup_unsafe_html',
        'expected' => 'Script: &lt;script&gt;alert(String.fromCharCode(88,83,83)&lt;/script&gt;',
      ],
      'translatable_markup' => [
        'route' => 'easy_breadcrumb_test.title_translatable_markup',
        'expected' => 'TranslatableMarkup',
      ],
      'render_array' => [
        'route' => 'easy_breadcrumb_test.title_render_array',
        'expected' => 'this is a string',
      ],
      'render_array_unsafe_html' => [
        'route' => 'easy_breadcrumb_test.title_render_array_unsafe_html',
        'expected' => 'this is a string with html: alert("clean me!")',
      ],
      'render_array_safe_html' => [
        'route' => 'easy_breadcrumb_test.title_render_array_safe_html',
        'expected' => 'this is a string with html: <em>leave me</em>',
      ],
    ];
  }

  /**
   * Tests getting title string from the various ways route titles can be set.
   *
   * @param string $route_name
   *   The route to test.
   * @param string $expected_title
   *   The expected title.
   *
   * @dataProvider providerTestGetTitleString
   */
  public function testGetTitleString($route_name, $expected_title) {
    $url = Url::fromRoute($route_name);
    $request_context = new RequestContext();

    $breadcrumb_builder = new EasyBreadcrumbBuilder($request_context,
      \Drupal::service('access_manager'),
      \Drupal::service('router'),
      \Drupal::service('request_stack'),
      \Drupal::service('path_processor_manager'),
      \Drupal::service('config.factory'),
      \Drupal::service('title_resolver'),
      \Drupal::service('current_user'),
      \Drupal::service('path.current'),
      \Drupal::service('plugin.manager.menu.link'),
      \Drupal::service('language_manager'),
      \Drupal::service('entity_type.manager'),
      \Drupal::service('entity.repository'),
      \Drupal::service('logger.factory'),
      \Drupal::service('messenger')
    );

    $request = Request::create($url->getInternalPath());
    $router = \Drupal::service('router.no_access_checks');
    $route_match = new RouteMatch($route_name, $router->match($url->getInternalPath())['_route_object']);
    $title = $breadcrumb_builder->getTitleString($request, $route_match, []);
    $this->assertEquals($expected_title, $title);
  }

}
