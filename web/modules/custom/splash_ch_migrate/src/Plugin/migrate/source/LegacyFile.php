<?php

namespace Drupal\splash_ch_migrate\Plugin\migrate\source;

use Drupal\Core\Site\Settings;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Legacy public files referenced by nodes, paragraphs or terms.
 *
 * Unreferenced files (webform uploads, theme leftovers) stay in the archive.
 *
 * @MigrateSource(
 *   id = "splash_ch_legacy_file",
 *   source_module = "splash_ch_migrate"
 * )
 */
class LegacyFile extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $used = $this->select('file_usage', 'u')
      ->fields('u', ['fid'])
      ->condition('u.type', ['node', 'paragraph', 'taxonomy_term'], 'IN');
    return $this->select('file_managed', 'f')
      ->fields('f')
      ->condition('f.fid', $used, 'IN')
      ->condition('f.uri', 'public://%', 'LIKE')
      ->orderBy('f.fid');
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $base = Settings::get('splash_ch_migrate_files');
    $row->setSourceProperty('source_path', $base . '/' . substr($row->getSourceProperty('uri'), strlen('public://')));
    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return ['fid' => 'fid', 'uri' => 'uri', 'filename' => 'filename', 'source_path' => 'Local path of the legacy file'];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return ['fid' => ['type' => 'integer']];
  }

}
