<?php

namespace Drupal\redirect_handler;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Database\Connection;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Language\Language;
use Drupal\file\Entity\File;
use Drupal\redirect\Entity\Redirect;
use Drupal\redirect_handler\Source\CsvSource;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Entity\EntityStorageException;

/**
 * Class RedirectHandler.
 *
 * @package Drupal\redirect_handler
 */
class RedirectHandler implements RedirectHandlerInterface, ContainerInjectionInterface {

  /**
   * The file location.
   *
   * @var string
   */
  protected $fileLocation = 'public://redirect/';

  /**
   * The file name.
   *
   * @var string
   */
  protected $fileName = 'sources.csv';

  /**
   * The Connection service.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The logging service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $logger;

  /**
   * RedirectHandler constructor.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database service.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger
   *   The logging service.
   */
  public function __construct(Connection $database,
                              LoggerChannelFactoryInterface $logger) {
    $this->database = $database;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('logger.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function delete() {
    /** @var \Drupal\redirect\Entity\Redirect[] $redirects */
    $redirects = Redirect::loadMultiple();
    foreach ($redirects as $redirect) {
      $redirect->delete();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function performImport() {
    $count = 0;
    $file = $this->getFile();
    $source = new CsvSource($file);
    $data = $source->readFile();
    // Loop all data and try to add redirects to the system.
    foreach ($data as $row) {
      if (isset($row['from']) && isset($row['to'])) {
        // Parse the source url so query and path are split.
        $parsed_from = UrlHelper::parse(trim($row['from']));
        // Read path if it was provided.
        $source_path = isset($parsed_from['path']) ? $parsed_from['path'] : NULL;
        // Checkout if we have query parameters.
        $source_query = isset($parsed_from['query']) ? $parsed_from['query'] : NULL;
        // A redirect can only be added if we have a source path at this point.
        // We then try to strip off the domain name because Drupal can't
        // handle this
        // domain name url.
        if ($source_path) {
          $source_path = $this->stripDomain($source_path);
          // Only add new redirects to the system.
          $language = (isset($row['language']) && $row['language'] != '') ? $row['language'] : Language::LANGCODE_NOT_SPECIFIED;
          $existingRedirect = redirect_repository()->findMatchingRedirect(ltrim($source_path, '/'), $source_query, $language);
          if ($existingRedirect) {
            continue;
          }
          else {
            $type = (isset($row['type']) && $row['type'] != '') ? $row['type'] : 301;
            $to_url = urldecode($row['to']);
            $redirect = Redirect::create();
            if ($this->isExternal($to_url)) {
              $redirect->redirect_redirect->set(0, $to_url, []);
            }
            else {
              $redirect->setRedirect($to_url);
            }
            $redirect->setSource($source_path, $source_query);
            $redirect->setLanguage(strtolower($language));
            $redirect->setStatusCode($type);

            try {
              $redirect->save();
              $count++;
            }
            catch (EntityStorageException $e) {
              $this->logger->get('redirect_handler')->error($e->getMessage());
            }
          }
        }
      }
    }

    return $count;
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    $query = $this->database->select('file_managed', 'fm')
      ->fields('fm', ['fid'])
      ->condition('uri', $this->fileLocation . $this->fileName);
    $result = $query->execute()->fetchObject();
    if ($result) {
      $file = File::load($result->fid);
      return $file;
    }
    return NULL;
  }

  /**
   * Strip off the domain name of an url if this exist.
   *
   * @param string $source_path
   *   The source path.
   *
   * @return string
   *   Fixed source path.
   */
  protected function stripDomain($source_path) {
    if (strpos($source_path, '://') !== FALSE) {
      $parts = parse_url($source_path);
      if (isset($parts['path'])) {
        return $parts['path'];
      }
    }
    return $source_path;
  }

  /**
   * Checks if a URL string is exernal or internal.
   *
   * @param string $url
   *   The url to check.
   *
   * @return bool
   *   If the url is external or not.
   */
  protected function isExternal($url) {
    if (strpos($url, '://') !== FALSE) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

}
