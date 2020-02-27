<?php

namespace Drupal\redirect_handler;

/**
 * Interface RedirectHandlerInterface.
 *
 * @package Drupal\redirect_handler
 */
interface RedirectHandlerInterface {

  /**
   * Delete all redirects in the site.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function delete();

  /**
   * Perform a csv import.
   *
   * Import all rows as a new redirect if not exist.
   *
   * @return mixed
   *   The amount of rows that were imported.
   */
  public function performImport();

  /**
   * Returns the file.
   *
   * @return \Drupal\Core\Entity\EntityInterface|\Drupal\file\Entity\File|null
   *   The file in question or null.
   */
  public function getFile();

}
