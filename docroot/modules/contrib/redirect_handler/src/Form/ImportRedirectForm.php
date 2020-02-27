<?php

namespace Drupal\redirect_handler\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\redirect_handler\RedirectHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ImportRedirectForm.
 *
 * @package Drupal\redirect_handler\Form
 */
class ImportRedirectForm extends FormBase {

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
   * The redirect handler.
   *
   * @var \Drupal\redirect_handler\RedirectHandlerInterface
   */
  protected $redirectHandlerManager;

  /**
   * ImportRedirectForm constructor.
   *
   * @param \Drupal\redirect_handler\RedirectHandlerInterface $redirectHandler
   *   The redirect handler service.
   */
  public function __construct(RedirectHandlerInterface $redirectHandler) {
    $this->redirectHandlerManager = $redirectHandler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('redirect_handler.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'import_redirect_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $path = NULL;

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => t('Upload'),
      ],
    ];
    $file = NULL;
    if ($file = $this->redirectHandlerManager->getFile()) {
      $form['actions'][] = [
        '#type' => 'submit',
        '#submit' => ['::performImport'],
        '#value' => t('Import'),
      ];
    }
    $validators = [
      'file_validate_extensions' => [
        'csv',
      ],
    ];
    $form['source'] = [
      '#type' => 'managed_file',
      '#title' => t('Source file'),
      '#default_value' => ($file) ? [$file->id()] : '',
      '#upload_location' => $this->fileLocation,
      '#upload_validators' => $validators,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $fid = $form_state->getValue('source')[0];

    $file = File::load($fid);
    if ($file) {
      $new_file = clone $file;
      $new_file->setFilename($this->fileName);
      $new_file->setPermanent();
      $new_file->save();

      if (!file_prepare_directory($this->fileLocation, FILE_CREATE_DIRECTORY | FILE_MODIFY_PERMISSIONS)) {
        \Drupal::logger('redirect_handler')
          ->error('Failed to create the redirect directory.');
      }

      file_move($new_file, $this->fileLocation . $this->fileName, FILE_EXISTS_REPLACE);
    }
  }

  /**
   * Run the import of the current uploaded sources.csv.
   */
  public function performImport() {
    $count = $this->redirectHandlerManager->performImport();
    if ($count > 0) {
      \Drupal::messenger()
        ->addMessage(t('Successfully created @count redirects', ['@count' => $count]));
    }
    else {
      \Drupal::messenger()
        ->addMessage(t('No new redirects created'), 'warning');
    }
  }

}
