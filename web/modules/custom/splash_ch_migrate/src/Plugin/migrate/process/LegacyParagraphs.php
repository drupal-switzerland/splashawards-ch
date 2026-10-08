<?php

namespace Drupal\splash_ch_migrate\Plugin\migrate\process;

use Drupal\Component\Utility\Html;
use Drupal\Core\Database\Database;
use Drupal\media\Entity\Media;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\splash_ch_migrate\Plugin\migrate\source\LegacyEntity;

/**
 * Rebuilds a legacy Rocketship paragraph list as new splash_awards paragraphs.
 *
 * Input: the legacy field_paragraphs items. Output: target_id /
 * target_revision_id pairs of freshly created paragraphs. Rare types are
 * flattened to text, forms are dropped; both are logged as migrate messages.
 *
 * ponytail: paragraphs are created on every import; re-running with --update
 * leaves the previous ones as orphans for ERR's orphan purger to delete.
 *
 * @MigrateProcessPlugin(
 *   id = "splash_ch_paragraphs",
 *   handle_multiples = TRUE
 * )
 */
class LegacyParagraphs extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $out = [];
    $jury = NULL;
    foreach ((array) $value as $item) {
      $legacy = $this->legacyParagraph($item['target_id']);
      if (!$legacy) {
        continue;
      }
      // Consecutive jury paragraphs share one jury_grid.
      if ($legacy['type'] !== 'jury_paragraph') {
        $jury = NULL;
      }
      foreach ($this->build($legacy, $migrate_executable, $jury) as $paragraph) {
        $paragraph->save();
        $out[] = ['target_id' => $paragraph->id(), 'target_revision_id' => $paragraph->getRevisionId()];
      }
    }
    return $out;
  }

  /**
   * Maps one legacy paragraph to zero or more new (unsaved) paragraphs.
   */
  protected function build(array $p, MigrateExecutableInterface $executable, ?Paragraph &$jury): array {
    $f = fn(string $field, string $column = 'value') => $this->value($p['id'], $field, $column);
    $log = fn(string $msg) => $executable->saveMessage("Legacy paragraph {$p['id']} ({$p['type']}): $msg", MigrationInterface::MESSAGE_INFORMATIONAL);

    switch ($p['type']) {
      case 'p_003':
        return [$this->text($this->heading($f('field_p_title')) . $this->html($f('field_p_teaser')) . $f('field_p_text') . $this->button($p['id']))];

      case 'p_001':
        $paragraphs = [$this->text($this->heading($f('field_p_title')) . $this->html($f('field_p_subtitle')) . $f('field_p_text') . $this->button($p['id']))];
        if ($media = $this->media($p['id'], 'field_p_image')) {
          $paragraphs[] = Paragraph::create(['type' => 'images', 'field_images' => $media, 'field_layout' => 'center']);
        }
        $log('story flattened to text + images');
        return $paragraphs;

      case 'p_002':
      case 'p_009':
        $media = array_merge($this->media($p['id'], 'field_p_image'), $this->media($p['id'], 'field_p_images_unlimited'));
        $paragraphs = [];
        if ($heading = $this->heading($f('field_p_title'))) {
          $paragraphs[] = $this->text($heading . $this->html($f('field_p_teaser')));
        }
        if ($media) {
          $paragraphs[] = Paragraph::create(['type' => 'images', 'field_images' => $media, 'field_layout' => 'center']);
        }
        return $paragraphs;

      case 'p_006':
        $url = $f('field_p_video');
        $log('video flattened to a text link');
        return [$this->text($this->heading($f('field_title')) . $this->html($f('field_p_subtitle')) . ($url ? '<p><a href="' . Html::escape($url) . '">' . Html::escape($url) . '</a></p>' : ''))];

      case 'p_007':
        $html = $this->heading($f('field_p_title')) . $this->html($f('field_p_teaser'));
        foreach ($this->children($p['id'], 'field_p_007_children') as $child) {
          $html .= $this->heading($this->value($child, 'field_p_title'), 'h3') . $this->value($child, 'field_p_text');
        }
        $log('USP flattened to text');
        return [$this->text($html . $this->button($p['id']))];

      case 'p_008':
        $button = LegacyEntity::fieldValues($this->db(), 'paragraph', $p['id'], 'field_p_button')[0] ?? NULL;
        return [Paragraph::create([
          'type' => 'cta',
          'field_cta_title' => ['value' => $f('field_p_title'), 'format' => 'plain_text'],
          'field_cta_text' => ['value' => $f('field_p_teaser'), 'format' => 'plain_text'],
          'field_cta_link' => $button ? ['uri' => $this->uri($button['uri']), 'title' => $button['title']] : [],
          'field_type' => 'default',
        ])];

      case 'overview':
        $view = $f('field_overview');
        if (str_starts_with((string) $view, 'news_overview')) {
          return [Paragraph::create(['type' => 'news', 'field_teaser_mode' => $view === 'news_overview_front'])];
        }
        $log("overview '$view' dropped (award landing pages list cases now)");
        return [];

      case 'jury_paragraph':
        $member = Paragraph::create([
          'type' => 'jury_member',
          'field_jury_name' => ['value' => trim(strip_tags((string) $f('field_name'))), 'format' => 'plain_text'],
          'field_jury_description' => ['value' => trim(strip_tags((string) $f('field_company'))), 'format' => 'plain_text'],
          'field_portrait' => $this->media($p['id'], 'field_image')[0] ?? NULL,
        ]);
        $member->save();
        $ref = ['target_id' => $member->id(), 'target_revision_id' => $member->getRevisionId()];
        if ($jury) {
          // Already returned earlier in this list; saved again by the caller.
          $jury->get('field_jury_member')->appendItem($ref);
          $jury->save();
          return [];
        }
        $jury = Paragraph::create(['type' => 'jury_grid', 'field_jury_member' => [$ref]]);
        return [$jury];

      case 'p_010':
        $sponsors = [];
        foreach ($this->children($p['id'], 'field_p_010_children') as $child) {
          $link = LegacyEntity::fieldValues($this->db(), 'paragraph', $child, 'field_p_link')[0] ?? NULL;
          $sponsor = Paragraph::create([
            'type' => 'sponsor',
            'field_sponsor_logo' => $this->media($child, 'field_p_image')[0] ?? NULL,
            'field_sponsor_url' => $link ? ['uri' => $this->uri($link['uri']), 'title' => $link['title']] : [],
          ]);
          $sponsor->save();
          $sponsors[] = ['target_id' => $sponsor->id(), 'target_revision_id' => $sponsor->getRevisionId()];
        }
        return [Paragraph::create([
          'type' => 'sponsor_grid',
          'field_sponsors_title' => ['value' => (string) $f('field_p_title'), 'format' => 'plain_text'],
          'field_sponsors_text' => ['value' => (string) $f('field_p_teaser'), 'format' => 'plain_text'],
          'field_sponsors' => $sponsors,
        ])];

      case 'p_011':
        $log('form dropped (webforms are not migrated)');
        return [];
    }
    $log('unknown type dropped');
    return [];
  }

  /**
   * Media entities (get-or-create) for a legacy paragraph image field.
   */
  protected function media($paragraph_id, string $field): array {
    $media = [];
    foreach (LegacyEntity::fieldValues($this->db(), 'paragraph', $paragraph_id, $field) as $image) {
      if ($id = static::mediaForFile((int) $image['target_id'], (string) ($image['alt'] ?? ''))) {
        $media[] = ['target_id' => $id];
      }
    }
    return $media;
  }

  /**
   * Returns the image media id wrapping a (migrated, same-fid) file.
   */
  public static function mediaForFile(int $fid, string $alt = ''): ?int {
    $storage = \Drupal::entityTypeManager()->getStorage('media');
    $ids = $storage->getQuery()->accessCheck(FALSE)->condition('field_media_image.target_id', $fid)->range(0, 1)->execute();
    if ($ids) {
      return (int) reset($ids);
    }
    $file = \Drupal::entityTypeManager()->getStorage('file')->load($fid);
    if (!$file) {
      return NULL;
    }
    $media = Media::create([
      'bundle' => 'image',
      'uid' => 1,
      'name' => $file->getFilename(),
      'field_media_image' => ['target_id' => $fid, 'alt' => $alt ?: pathinfo($file->getFilename(), PATHINFO_FILENAME)],
    ]);
    $media->save();
    return (int) $media->id();
  }

  /**
   * Legacy paragraph base row, or NULL.
   */
  protected function legacyParagraph($id): ?array {
    $row = $this->db()->select('paragraphs_item_field_data', 'p')->fields('p', ['id', 'type'])->condition('id', $id)->execute()->fetchAssoc();
    return $row ?: NULL;
  }

  /**
   * Child paragraph ids of an entity_reference_revisions field.
   */
  protected function children($id, string $field): array {
    return array_column(LegacyEntity::fieldValues($this->db(), 'paragraph', $id, $field), 'target_id');
  }

  /**
   * First value of a column of a legacy paragraph field.
   */
  protected function value($id, string $field, string $column = 'value') {
    return LegacyEntity::fieldValues($this->db(), 'paragraph', $id, $field)[0][$column] ?? NULL;
  }

  /**
   * Button link of a legacy paragraph as HTML.
   */
  protected function button($id): string {
    $button = LegacyEntity::fieldValues($this->db(), 'paragraph', $id, 'field_p_button')[0] ?? NULL;
    if (!$button || !$button['uri']) {
      return '';
    }
    $href = \Drupal\Core\Url::fromUri($this->uri($button['uri']))->toString();
    return '<p><a href="' . Html::escape($href) . '">' . Html::escape($button['title'] ?: $href) . '</a></p>';
  }

  /**
   * Legacy link uri, made safe for the new site (nids are preserved).
   */
  protected function uri(string $uri): string {
    return preg_replace('#^entity:node/#', 'internal:/node/', $uri);
  }

  protected function text(string $html): Paragraph {
    return Paragraph::create(['type' => 'text', 'field_text' => ['value' => $html, 'format' => 'basic_html']]);
  }

  protected function heading($text, string $tag = 'h2'): string {
    return trim((string) $text) === '' ? '' : "<$tag>" . Html::escape(trim($text)) . "</$tag>";
  }

  protected function html($text): string {
    return trim((string) $text) === '' ? '' : '<p>' . nl2br(Html::escape(trim($text))) . '</p>';
  }

  protected function db() {
    return Database::getConnection('default', 'migrate');
  }

}
