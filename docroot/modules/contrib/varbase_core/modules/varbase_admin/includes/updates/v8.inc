<?php

/**
 * @file
 * Contains varbase_admin_update_8###(s) hook updates.
 */

/**
 * Enable Length Indicator module.
 */
function varbase_admin_update_8602() {

  /** @var \Drupal\update_helper\Updater $updateHelper */
  $updateHelper = \Drupal::service('update_helper.updater');

  // Execute configuration update definitions with logging of success.
  $updateHelper->executeUpdate('varbase_core', 'varbase_admin_update_8602');

  // Output logged messages to related channel of update execution.
  return $updateHelper->logger()->output();
}

/**
 * Enable Admin Toolbar Links Access Filter module.
 */
function varbase_admin_update_8603() {
  if (!\Drupal::moduleHandler()->moduleExists('admin_toolbar_links_access_filter')) {
    \Drupal::service('module_installer')->install(['admin_toolbar_links_access_filter'], FALSE);
  }
}

/**
 * Enable [Events log track] module.
 */
function varbase_admin_update_8604() {
  if (!\Drupal::moduleHandler()->moduleExists('event_log_track')) {
    \Drupal::service('module_installer')->install(['event_log_track'], FALSE);
  }

  if (!\Drupal::moduleHandler()->moduleExists('event_log_track_file')) {
    \Drupal::service('module_installer')->install(['event_log_track_file'], FALSE);
  }

  if (!\Drupal::moduleHandler()->moduleExists('event_log_track_media')) {
    \Drupal::service('module_installer')->install(['event_log_track_media'], FALSE);
  }

  if (!\Drupal::moduleHandler()->moduleExists('event_log_track_user')) {
    \Drupal::service('module_installer')->install(['event_log_track_user'], FALSE);
  }

  if (!\Drupal::moduleHandler()->moduleExists('event_log_track_taxonomy')) {
    \Drupal::service('module_installer')->install(['event_log_track_taxonomy'], FALSE);
  }

  if (!\Drupal::moduleHandler()->moduleExists('event_log_track_node')) {
    \Drupal::service('module_installer')->install(['event_log_track_node'], FALSE);
  }

  if (!\Drupal::moduleHandler()->moduleExists('event_log_track_menu')) {
    \Drupal::service('module_installer')->install(['event_log_track_menu'], FALSE);
  }

  if (!\Drupal::moduleHandler()->moduleExists('event_log_track_auth')) {
    \Drupal::service('module_installer')->install(['event_log_track_auth'], FALSE);
  }

}

/**
 * Issue #3152698: Switch from [Event Log Track] to [Admin Audit Trail] module.
 */
function varbase_admin_update_8605() {

  // ---------------------------------------------------------------------------
  // Install all Admin Audit Trail modules.
  if (!\Drupal::moduleHandler()->moduleExists('admin_audit_trail')
    && \Drupal::moduleHandler()->moduleExists('event_log_track')) {
    \Drupal::service('module_installer')->install(['admin_audit_trail'], FALSE);
  }

  if (!\Drupal::moduleHandler()->moduleExists('admin_audit_trail_file')
    && \Drupal::moduleHandler()->moduleExists('event_log_track_file')) {
    \Drupal::service('module_installer')->install(['admin_audit_trail_file'], FALSE);
  }

  if (!\Drupal::moduleHandler()->moduleExists('admin_audit_trail_media')
    && \Drupal::moduleHandler()->moduleExists('event_log_track_media')) {
    \Drupal::service('module_installer')->install(['admin_audit_trail_media'], FALSE);
  }

  if (!\Drupal::moduleHandler()->moduleExists('admin_audit_trail_user')
    && \Drupal::moduleHandler()->moduleExists('event_log_track_user')) {
    \Drupal::service('module_installer')->install(['admin_audit_trail_user'], FALSE);
  }

  if (!\Drupal::moduleHandler()->moduleExists('admin_audit_trail_taxonomy')
    && \Drupal::moduleHandler()->moduleExists('event_log_track_taxonomy')) {
    \Drupal::service('module_installer')->install(['admin_audit_trail_taxonomy'], FALSE);
  }

  if (!\Drupal::moduleHandler()->moduleExists('admin_audit_trail_node')
    && \Drupal::moduleHandler()->moduleExists('event_log_track_node')) {
    \Drupal::service('module_installer')->install(['admin_audit_trail_node'], FALSE);
  }

  if (!\Drupal::moduleHandler()->moduleExists('admin_audit_trail_menu')
    && \Drupal::moduleHandler()->moduleExists('event_log_track_menu')) {
    \Drupal::service('module_installer')->install(['admin_audit_trail_menu'], FALSE);
  }

  if (!\Drupal::moduleHandler()->moduleExists('admin_audit_trail_auth')
    && \Drupal::moduleHandler()->moduleExists('event_log_track_auth')) {
    \Drupal::service('module_installer')->install(['admin_audit_trail_auth'], FALSE);
  }

  if (!\Drupal::moduleHandler()->moduleExists('admin_audit_trail_workflows')
    && \Drupal::moduleHandler()->moduleExists('event_log_track_workflows')) {
    \Drupal::service('module_installer')->install(['admin_audit_trail_workflows'], FALSE);
  }

  // Migrate logs from the event_log_track table to admin_audit_trail table.
  if (\Drupal::moduleHandler()->moduleExists('event_log_track')) {
    $fields = [
      'lid',
      'type',
      'operation',
      'path',
      'ref_numeric',
      'ref_char',
      'description',
      'uid',
      'ip',
      'created',
    ];

    $event_log_track_query = \Drupal::database()->select('event_log_track', 'event_log_track');
    $event_log_track_query->fields('event_log_track', $fields);
    $event_log_track_result = $event_log_track_query->execute();
    while ($event_log_track_row_values = $event_log_track_result->fetchAssoc()) {
      $result = \Drupal::database()->insert('admin_audit_trail')
        ->fields($fields)
        ->values($event_log_track_row_values)
        ->execute();
      unset($result);
    }
  }

  // Uninstall all Event Log Track modules.
  if (\Drupal::moduleHandler()->moduleExists('event_log_track_file')) {
    \Drupal::service('module_installer')->uninstall(['event_log_track_file'], FALSE);
  }

  if (\Drupal::moduleHandler()->moduleExists('event_log_track_media')) {
    \Drupal::service('module_installer')->uninstall(['event_log_track_media'], FALSE);
  }

  if (\Drupal::moduleHandler()->moduleExists('event_log_track_user')) {
    \Drupal::service('module_installer')->uninstall(['event_log_track_user'], FALSE);
  }

  if (\Drupal::moduleHandler()->moduleExists('event_log_track_taxonomy')) {
    \Drupal::service('module_installer')->uninstall(['event_log_track_taxonomy'], FALSE);
  }

  if (\Drupal::moduleHandler()->moduleExists('event_log_track_node')) {
    \Drupal::service('module_installer')->uninstall(['event_log_track_node'], FALSE);
  }

  if (\Drupal::moduleHandler()->moduleExists('event_log_track_menu')) {
    \Drupal::service('module_installer')->uninstall(['event_log_track_menu'], FALSE);
  }

  if (\Drupal::moduleHandler()->moduleExists('event_log_track_auth')) {
    \Drupal::service('module_installer')->uninstall(['event_log_track_auth'], FALSE);
  }

  if (\Drupal::moduleHandler()->moduleExists('event_log_track_workflows')) {
    \Drupal::service('module_installer')->uninstall(['event_log_track_workflows'], FALSE);
  }

  if (\Drupal::moduleHandler()->moduleExists('event_log_track')) {
    \Drupal::service('module_installer')->uninstall(['event_log_track'], FALSE);
  }

}
