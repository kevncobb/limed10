<?php

namespace Drupal\varbase_core\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides form for managing module settings.
 */
class VarbaseGeneralSettingsForm extends ConfigFormBase {

  /**
   * Get the from ID.
   */
  public function getFormId() {
    return 'varbase_core_general_settings';
  }

  /**
   * Get the editable config names.
   */
  protected function getEditableConfigNames() {
    return ['varbase_core.general_settings'];
  }

  /**
   * Build the form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    global $base_url;
    $front_page_with_welcome = $base_url . '/?welcome';

    $config = $this->config('varbase_core.general_settings');
    $form['settings']['welcome_status'] = [
      '#type' => 'checkbox',
      '#default_value' => $config->get('welcome_status') ?? 1,
      '#title' => $this->t('Allow site to show welcome message'),
      '#description' => $this->t('This option will allow to display Varbase\'s welcome message on the homepage by adding <code>?welcome</code> to the URL. This option is automatically disabled after closing the welcome message. Check this then navigate to <a href="@front_page_with_welcome">@front_page_with_welcome</a> to see the welcome message again.', ['@front_page_with_welcome' => $front_page_with_welcome]),
    ];

    $form['settings']['allow_custom_account_name'] = [
      '#type' => 'checkbox',
      '#default_value' => $config->get('allow_custom_account_name') ?? 1,
      '#title' => $this->t('Allow custom account name'),
      '#description' => $this->t('This option enables users to create custom usernames and provides the ability to change the names of existing user accounts.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Submit Form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $config = $this->config('varbase_core.general_settings');
    $config->set('welcome_status', $form_state->getValue('welcome_status'));
    $config->set('allow_custom_account_name', $form_state->getValue('allow_custom_account_name'));

    $config->save();

    parent::submitForm($form, $form_state);
  }

}
