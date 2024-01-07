<?php

namespace Drupal\editoria11y\Exception;

/**
 * A simple exception class to mark errors thrown by the editoria11y module.
 */
class Editoria11yApiException extends \Exception {

  /**
   * Constructs an Editoria11yApiException.
   *
   * @param string $class
   *   The entity parent class.
   */
  public function __construct($class) {
    $message = sprintf('%s', $class);
    parent::__construct($message);
    \Drupal::logger('warning')->warning('Warning' . $message . ' in Editoria11y API');
  }

}
