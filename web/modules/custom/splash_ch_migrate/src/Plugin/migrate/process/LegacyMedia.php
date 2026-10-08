<?php

namespace Drupal\splash_ch_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Turns a (migrated, same-fid) file id into an image media id.
 *
 * @MigrateProcessPlugin(
 *   id = "splash_ch_media"
 * )
 */
class LegacyMedia extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    return $value ? LegacyParagraphs::mediaForFile((int) $value) : NULL;
  }

}
