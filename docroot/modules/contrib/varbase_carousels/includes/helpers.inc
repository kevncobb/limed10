<?php

/**
 * @file
 * Contains List of all varbase_carousels helpers.
 *
 * Add custom needed helper functions.
 */

/**
 * Implements hook_requirements().
 */
function varbase_carousels_requirements($phase) {

  $requirements = [];

  $path = DRUPAL_ROOT . '/libraries/slick/slick/slick.min.js';

  // Is the library found in the root libraries path.
  $library_found = file_exists($path);

  // If library is not found, then look in the current profile libraries path.
  if (!$library_found) {
    $profile_path = drupal_get_path('profile', \Drupal::installProfile());
    $profile_path .= '/libraries/slick/slick/slick.min.js';
    // Is the library found in the current profile libraries path.
    $library_found = file_exists($profile_path);
  }

  if (!$library_found) {
    $requirements['slick_library'] = [
      'title' => t('Slick library missing'),
      'description' => t('Varbase carousels requires the slick.min.js library.
        Download it (https://github.com/kenwheeler/slick) and place it in the
        libraries folder (/libraries)'),
      'severity' => REQUIREMENT_ERROR,
    ];
  }

  return $requirements;
}
