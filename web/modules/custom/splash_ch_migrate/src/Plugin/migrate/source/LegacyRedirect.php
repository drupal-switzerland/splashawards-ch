<?php

namespace Drupal\splash_ch_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Old URLs that need a 301 on the new site.
 *
 * Combines the legacy node aliases (pathauto regenerated them, e.g.
 * /nominees/x -> /case/x) with the legacy redirect table. Rows whose old URL
 * is still the live alias, or whose target node was not migrated, are skipped.
 *
 * @MigrateSource(
 *   id = "splash_ch_legacy_redirect",
 *   source_module = "splash_ch_migrate"
 * )
 */
class LegacyRedirect extends SqlBase {

  /**
   * Legacy listing pages that were replaced by award landing pages.
   */
  const REPLACED = [193 => '/nominees/2023', 266 => '/nominees/2025'];

  /**
   * {@inheritdoc}
   */
  public function query() {
    $aliases = $this->select('url_alias', 'a');
    $aliases->addExpression('TRIM(LEADING :slash FROM a.alias)', 'source_path', [':slash' => '/']);
    $aliases->addExpression("CONCAT('internal:', a.source)", 'target');
    $aliases->condition('a.source', '/node/%', 'LIKE');

    $redirects = $this->select('redirect', 'r');
    $redirects->addField('r', 'redirect_source__path', 'source_path');
    $redirects->addField('r', 'redirect_redirect__uri', 'target');

    return $this->select($aliases->union($redirects), 'u')->fields('u', ['source_path', 'target']);
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $target = (string) $row->getSourceProperty('target');
    if (preg_match('#^(?:internal:/node/|entity:node/)(\d+)$#', $target, $m)) {
      $nid = (int) $m[1];
      if (isset(self::REPLACED[$nid])) {
        $target = 'internal:' . self::REPLACED[$nid];
      }
      elseif (!\Drupal::entityTypeManager()->getStorage('node')->load($nid)) {
        return FALSE;
      }
      else {
        $target = "internal:/node/$nid";
        $alias = \Drupal::service('path_alias.manager')->getAliasByPath("/node/$nid");
        if (ltrim($alias, '/') === $row->getSourceProperty('source_path')) {
          return FALSE;
        }
      }
    }
    $row->setSourceProperty('target', $target);
    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return ['source_path' => 'Old path without leading slash', 'target' => 'Redirect target uri'];
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return ['source_path' => ['type' => 'string']];
  }

}
