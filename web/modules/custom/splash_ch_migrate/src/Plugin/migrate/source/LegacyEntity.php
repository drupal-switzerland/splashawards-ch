<?php

namespace Drupal\splash_ch_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Reads one entity type/bundle from the legacy D8 database, latest revision.
 *
 * Configuration:
 * - entity_type: node, taxonomy_term, user or menu_link_content.
 * - bundle: (optional) bundle/vocabulary to restrict to.
 * - fields: field names whose values are attached to the row, as a list of
 *   items keyed by column without the field-name prefix (value, target_id…).
 * - ids: (optional) only these entity ids.
 * - exclude_ids: (optional) skip these entity ids.
 *
 * @MigrateSource(
 *   id = "splash_ch_legacy_entity",
 *   source_module = "splash_ch_migrate"
 * )
 */
class LegacyEntity extends SqlBase {

  const TYPES = [
    'node' => ['node_field_data', 'nid', 'type'],
    'taxonomy_term' => ['taxonomy_term_field_data', 'tid', 'vid'],
    'user' => ['users_field_data', 'uid', NULL],
    'menu_link_content' => ['menu_link_content_data', 'id', NULL],
  ];

  /**
   * {@inheritdoc}
   */
  public function query() {
    [$table, $id, $bundle_key] = self::TYPES[$this->configuration['entity_type']];
    $query = $this->select($table, 'e')->fields('e');
    if ($bundle_key && !empty($this->configuration['bundle'])) {
      $query->condition("e.$bundle_key", $this->configuration['bundle']);
    }
    if (!empty($this->configuration['ids'])) {
      $query->condition("e.$id", $this->configuration['ids'], 'IN');
    }
    if (!empty($this->configuration['exclude_ids'])) {
      $query->condition("e.$id", $this->configuration['exclude_ids'], 'NOT IN');
    }
    return $query->orderBy("e.$id");
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $type = $this->configuration['entity_type'];
    $id = $row->getSourceProperty(self::TYPES[$type][1]);
    foreach ($this->configuration['fields'] ?? [] as $field) {
      $row->setSourceProperty($field, static::fieldValues($this->getDatabase(), $type, $id, $field));
    }
    if ($type === 'node') {
      $alias = $this->select('url_alias', 'a')
        ->fields('a', ['alias'])
        ->condition('source', "/node/$id")
        ->orderBy('pid', 'DESC')
        ->range(0, 1)
        ->execute()->fetchField();
      $row->setSourceProperty('legacy_alias', $alias ?: NULL);
    }
    return parent::prepareRow($row);
  }

  /**
   * Returns a field's items for one legacy entity, prefix stripped from keys.
   */
  public static function fieldValues($database, string $entity_type, $id, string $field): array {
    $table = "{$entity_type}__{$field}";
    if (!$database->schema()->tableExists($table)) {
      return [];
    }
    $items = [];
    $result = $database->select($table, 'f')->fields('f')
      ->condition('entity_id', $id)
      ->condition('deleted', 0)
      ->orderBy('delta')
      ->execute();
    foreach ($result as $record) {
      $item = [];
      foreach ((array) $record as $column => $value) {
        if (str_starts_with($column, "{$field}_")) {
          $item[substr($column, strlen($field) + 1)] = $value;
        }
      }
      $items[] = $item;
    }
    return $items;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return array_combine($this->configuration['fields'] ?? [], $this->configuration['fields'] ?? []);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [self::TYPES[$this->configuration['entity_type']][1] => ['type' => 'integer']];
  }

}
