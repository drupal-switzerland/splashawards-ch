<?php

namespace Drupal\splash_awards_base\Entity;

/**
 * Interface to implement of entities that are pages.
 */
interface PageInterface {

  /**
   * Returns the data to be displayed in the hero section.
   *
   * @return array
   *   An array of data to be displayed in the hero section.
   */
  public function getHeroData(): array;

}
