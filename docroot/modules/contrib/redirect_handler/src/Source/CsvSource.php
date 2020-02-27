<?php

namespace Drupal\redirect_handler\Source;

use Drupal\file\Entity\File;

/**
 * Class CsvSource.
 *
 * @package Drupal\redirect_handler\Source
 */
class CsvSource {

  /**
   * The file object to work on.
   *
   * @var \Drupal\file\Entity\File
   */
  protected $file;

  /**
   * The file data.
   *
   * @var array
   */
  protected $data = [];

  /**
   * The CSV delimiter.
   *
   * @var string
   */
  protected $delimiter = ';';

  /**
   * Whether or not the file has a header row.
   *
   * @var bool
   */
  protected $headers = TRUE;

  /**
   * What header column names.
   *
   * @var array
   */
  protected $headerData = ['from', 'to', 'language', 'type'];

  /**
   * CsvSource constructor.
   *
   * @param \Drupal\file\Entity\File $file
   *   The CSV file.
   */
  public function __construct(File $file) {
    $this->file = $file;

  }

  /**
   * Reads a redirect csv file and returns the result.
   *
   * @return array
   *   The data from the file.
   */
  public function readFile() {
    $row = [];
    if (($handle = fopen($this->file->getFileUri(), 'r')) !== FALSE) {
      $i = 0;
      $columns = 4;
      while (($data = fgetcsv($handle, 1000, $this->delimiter)) !== FALSE) {
        if ($this->headers && !$i) {
          $i++;
          continue;
        }
        else {
          $row = [];
          for ($c = 0; $c < $columns; $c++) {
            // Don't add empty rows.
            if ($data[$c] != '') {
              $row[$this->headerData[$c]] = $data[$c];
            }
            else {
              continue;
            }
          }
          if ($row) {
            $this->data[] = $row;
          }
        }
        $i++;
      }
    }
    return $this->data;
  }

}
