<?php

/**
 * @file
 * Contains varbase_security_update_9###(s) hook updates.
 */

use Vardot\Installer\ModuleInstallerFactory;

/**
 * Add Flood Control module for login attempt limiting.
 *
 * And remove the Flood Unblock module as it will be
 * discontinued by its maintainers.
 */
function varbase_security_update_9001() {

  /** @var \Drupal\update_helper\Updater $updateHelper */
  $updateHelper = \Drupal::service('update_helper.updater');

  // Execute configuration update definitions with logging of success.
  $updateHelper->executeUpdate('varbase_core', 'varbase_security_update_9001');

  // Output logged messages to related channel of update execution.
  return $updateHelper->logger()->output();
}

/**
 * Reinforce spam protection and enable "Honeypot" spam detection.
 *
 * Enabling protection on a wider forms and enabling time limit of 2 seconds.
 */
function varbase_security_update_9002() {
  /** @var \Drupal\update_helper\Updater $updateHelper */
  $updateHelper = \Drupal::service('update_helper.updater');

  // Execute configuration update definitions with logging of success.
  $updateHelper->executeUpdate('varbase_core', 'varbase_security_update_9002');

  // Output logged messages to related channel of update execution.
  return $updateHelper->logger()->output();
}

/**
 * Issue #3252064: Add Antibot module with config to Varbase Security.
 *
 * Reinforce spam protection via the "Antibot" spam detection mechanism.
 */
function varbase_security_update_9003() {
  /** @var \Drupal\update_helper\Updater $updateHelper */
  $updateHelper = \Drupal::service('update_helper.updater');

  // Execute configuration update definitions with logging of success.
  $updateHelper->executeUpdate('varbase_core', 'varbase_security_update_9003');

  // Output logged messages to related channel of update execution.
  return $updateHelper->logger()->output();
}

/**
 * Issue #3265975: Fix errors on validating Security Review.
 *
 * Config synchronization when the module is not enabled yet.
 */
function varbase_security_update_9004() {
  if (!\Drupal::moduleHandler()->moduleExists('security_review')) {
    // Managed configs for the Security Review module.
    $managed_configs = [
      'security_review.settings',
      'security_review.check.security_review-admin_permissions',
      'security_review.check.security_review-error_reporting',
      'security_review.check.security_review-executable_php',
      'security_review.check.security_review-failed_logins',
      'security_review.check.security_review-field',
      'security_review.check.security_review-file_perms',
      'security_review.check.security_review-input_formats',
      'security_review.check.security_review-private_files',
      'security_review.check.security_review-query_errors',
      'security_review.check.security_review-temporary_files',
      'security_review.check.security_review-trusted_hosts',
      'security_review.check.security_review-upload_extensions',
      'security_review.check.security_review-views_access',
    ];

    // Delete all Security Review configs if the module is not enable yet.
    // Which they will be imported again after installing the module.
    foreach ($managed_configs as $config_name) {
      \Drupal::configFactory()->getEditable($config_name)->delete();
    }
  }
}

/**
 * Issue #3256029: Fix Revise password policy and password suggestions message.
 *
 * Change Varbase Security weight and make it after all modules installed by it.
 */
function varbase_security_update_90004() {
  // Set the weight of the module after installation of list of modules.
  // To make sure that any hook or event subscriber worker
  // after all used modules.
  ModuleInstallerFactory::setModuleWeightAfterInstallation('varbase_security');

  /** @var \Drupal\update_helper\Updater $updateHelper */
  $updateHelper = \Drupal::service('update_helper.updater');

  // Execute configuration update definitions with logging of success.
  $updateHelper->executeUpdate('varbase_core', 'varbase_security_update_90004');

  // Output logged messages to related channel of update execution.
  return $updateHelper->logger()->output();
}

/**
 * Issue #3332656: Remove the hook implementation.
 *
 * That unset password_policy_status key, and
 * Add show_policy_table: false to the configuration of the default policy.
 */
function varbase_security_update_90005() {

  // Given that the Password Policy was enabled.
  if (\Drupal::moduleHandler()->moduleExists('password_policy')) {
    $password_policy_default_policy = \Drupal::service('config.factory')->getEditable('password_policy.password_policy.default_policy');
    $password_policy_default_policy->set('show_policy_table', FALSE)->save(TRUE);
  }

}
