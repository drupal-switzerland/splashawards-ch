<?php

namespace Drupal\dropsolid_office_advanced\Plugin\DsField;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\ds\Plugin\DsField\DsFieldBase;

/**
 * Outputs a link back to the migrated node, or another one if set.
 *
 * @DsField(
 *   id = "office_more_offices",
 *   title = @Translation("More offices link"),
 *   entity_type = "node",
 *   ui_limit = {"office|*"},
 *   provider = "dropsolid_office_advanced"
 * )
 */
class MoreOfficesLink extends DsFieldBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'uuid' => '69352ecf-1e01-49fb-a7b6-a8490803a9da',
      'link_text' => 'More offices',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary($settings) {
    $config = $this->getConfiguration();

    $summary = [];
    $summary[] = 'UUID: ' . $config['uuid'];
    $summary[] = 'Link text: ' . $config['link_text'];

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm($form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();

    $form['uuid'] = [
      '#title' => t('Node UUID'),
      '#description' => t('Enter the UUID of the node to link to. Defaults to the migrated node.'),
      '#type' => 'textfield',
      '#default_value' => $config['uuid'],
      '#required' => TRUE,
    ];
    $form['link_text'] = [
      '#title' => t('Link text'),
      '#description' => t('Text for the link to the node.'),
      '#type' => 'textfield',
      '#default_value' => $config['link_text'],
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $uuid = $config['uuid'];

    $nodes = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadByProperties(['uuid' => $uuid]);
    if (!$nodes) {
      return [];
    }
    /** @var \Drupal\node\NodeInterface $node */
    $node = reset($nodes);
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
    if ($node->hasTranslation($language)) {
      $node = $node->getTranslation($language);
    }
    $url = $node->toUrl();
    $text = $this->t($config['link_text']);

    return Link::fromTextAndUrl($text, $url)->toRenderable();
  }

}
