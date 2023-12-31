<?php

namespace Drupal\reroute_email\Form;

use Drupal\Component\Utility\EmailValidatorInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\reroute_email\Constants\RerouteEmailConstants;
use Drupal\user\RoleStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Implements a settings form for Reroute Email configuration.
 */
class SettingsForm extends ConfigFormBase implements TrustedCallbackInterface {

  /**
   * An editable config.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $rerouteConfig;

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The role storage.
   *
   * @var \Drupal\user\RoleStorageInterface
   */
  protected $roleStorage;

  /**
   * The email validator.
   *
   * @var \Drupal\Component\Utility\EmailValidatorInterface
   */
  protected $emailValidator;

  /**
   * The module extension list.
   *
   * @var \Drupal\Core\Extension\ModuleExtensionList
   */
  protected $moduleExtensionList;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'reroute_email_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['reroute_email.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks() {
    return ['textareaRowsValue'];
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('module_handler'),
      $container->get('entity_type.manager')->getStorage('user_role'),
      $container->get('email.validator'),
      $container->get('extension.list.module')
    );
  }

  /**
   * Constructs a new object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler service.
   * @param \Drupal\user\RoleStorageInterface $role_storage
   *   The role storage.
   * @param \Drupal\Component\Utility\EmailValidatorInterface $email_validator
   *   The email validator.
   * @param \Drupal\Core\Extension\ModuleExtensionList $extension_list_module
   *   The module extension list.
   */
  public function __construct(ConfigFactoryInterface $config_factory,
                              ModuleHandlerInterface $module_handler,
                              RoleStorageInterface $role_storage,
                              EmailValidatorInterface $email_validator,
                              ModuleExtensionList $extension_list_module) {
    parent::__construct($config_factory);
    $this->rerouteConfig = $this->config('reroute_email.settings');
    $this->moduleHandler = $module_handler;
    $this->roleStorage = $role_storage;
    $this->emailValidator = $email_validator;
    $this->moduleExtensionList = $extension_list_module;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form[RerouteEmailConstants::REROUTE_EMAIL_ENABLE] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable rerouting'),
      '#default_value' => $this->rerouteConfig->get(RerouteEmailConstants::REROUTE_EMAIL_ENABLE),
      '#description' => $this->t('Check this box if you want to enable email rerouting. Uncheck to disable rerouting.'),
      '#config' => [
        'key' => 'reroute_email.settings:' . RerouteEmailConstants::REROUTE_EMAIL_ENABLE,
      ],
    ];

    $states = [
      'visible' => [':input[name=' . RerouteEmailConstants::REROUTE_EMAIL_ENABLE . ']' => ['checked' => TRUE]],
    ];

    $default_address = $this->rerouteConfig->get(RerouteEmailConstants::REROUTE_EMAIL_ADDRESS);
    if (NULL === $default_address) {
      $default_address = $this->config('system.site')->get('mail');
    }

    $form[RerouteEmailConstants::REROUTE_EMAIL_ADDRESS] = [
      '#type' => 'textarea',
      '#rows' => 2,
      '#title' => $this->t('Rerouting email addresses'),
      '#default_value' => $default_address,
      '#description' => $this->t('Provide a comma-delimited list of email addresses. Every destination email address which is not fit with "Skip email rerouting for" lists will be rerouted to these addresses.<br/>If this field is empty and no value is provided, all outgoing emails would be aborted and the email would be recorded in the recent log entries (if enabled).'),
      '#element_validate' => [
        [$this, 'validateMultipleEmails'],
        [$this, 'validateMultipleUnique'],
      ],
      '#reroute_config_delimiter' => ',',
      '#pre_render' => [[$this, 'textareaRowsValue']],
      '#states' => $states,
      '#config' => [
        'key' => 'reroute_email.settings:' . RerouteEmailConstants::REROUTE_EMAIL_ADDRESS,
      ],
    ];

    $form[RerouteEmailConstants::REROUTE_EMAIL_ALLOWLIST] = [
      '#type' => 'textarea',
      '#rows' => 2,
      '#title' => $this->t('Skip email rerouting for email addresses:'),
      '#default_value' => $this->rerouteConfig->get(RerouteEmailConstants::REROUTE_EMAIL_ALLOWLIST),
      '#description' => $this->t('Provide a line-delimited list of email addresses to pass through. All emails to addresses from this list will not be rerouted.<br/>A patterns like "*@example.com" and "myname+*@example.com" can be used to add all emails by its domain or the pattern.'),
      '#element_validate' => [
        [$this, 'validateMultipleEmails'],
        [$this, 'validateMultipleUnique'],
      ],
      '#pre_render' => [[$this, 'textareaRowsValue']],
      '#states' => $states,
      '#config' => [
        'key' => 'reroute_email.settings:' . RerouteEmailConstants::REROUTE_EMAIL_ALLOWLIST,
      ],
    ];

    $roles = [];
    foreach ($this->roleStorage->loadMultiple() as $role) {
      /** @var \Drupal\user\RoleInterface $role */
      if ($role->id() !== 'anonymous') {
        $roles[$role->id()] = $role->get('label');
      }
    }
    $form[RerouteEmailConstants::REROUTE_EMAIL_ROLES] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Skip email rerouting for roles:'),
      '#description' => $this->t("Emails that belong to users with selected roles won't be rerouted."),
      '#options' => $roles,
      '#default_value' => (array) $this->rerouteConfig->get(RerouteEmailConstants::REROUTE_EMAIL_ROLES),
      '#states' => $states,
      '#config' => [
        'key' => 'reroute_email.settings:' . RerouteEmailConstants::REROUTE_EMAIL_ROLES,
      ],
    ];

    $form[RerouteEmailConstants::REROUTE_EMAIL_DESCRIPTION] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show rerouting description in mail body'),
      '#default_value' => $this->rerouteConfig->get(RerouteEmailConstants::REROUTE_EMAIL_DESCRIPTION),
      '#description' => $this->t('Check this box if you want a message to be inserted into the email body when the mail is being rerouted. Otherwise, SMTP headers will be used to describe the rerouting. If sending rich-text email, leave this unchecked so that the body of the email will not be disturbed.'),
      '#states' => $states,
      '#config' => [
        'key' => 'reroute_email.settings:' . RerouteEmailConstants::REROUTE_EMAIL_DESCRIPTION,
      ],
    ];

    $form[RerouteEmailConstants::REROUTE_EMAIL_MESSAGE] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display a Drupal status message after rerouting'),
      '#default_value' => $this->rerouteConfig->get(RerouteEmailConstants::REROUTE_EMAIL_MESSAGE),
      '#description' => $this->t('Check this box if you would like a Drupal status message to be displayed to users after submitting an email to let them know it was aborted to send or rerouted to a different email address.'),
      '#states' => $states,
      '#config' => [
        'key' => 'reroute_email.settings:' . RerouteEmailConstants::REROUTE_EMAIL_MESSAGE,
      ],
    ];

    $form['mailkeys'] = [
      '#type' => 'details',
      '#title' => $this->t('Mail keys settings'),
      '#states' => $states,
      '#open' => (!empty($this->rerouteConfig->get(RerouteEmailConstants::REROUTE_EMAIL_MAILKEYS)) || !empty($this->rerouteConfig->get(RerouteEmailConstants::REROUTE_EMAIL_MAILKEYS_SKIP))),
    ];

    // Format a list of modules that implement hook_mail.
    $mail_modules = [];
    $this->moduleHandler->invokeAllWith('mail', function (callable $hook, string $module) use (&$mail_modules) {
      $mail_modules[] = $this->t("%module's module possible mail keys are `@machine_name`, `@machine_name_%`;", [
        '%module' => $this->moduleExtensionList->getName($module) ?? $module,
        '@machine_name' => $module,
      ]);
    });
    $form['mailkeys']['modules'] = [
      [
        '#type' => 'item',
        '#plain_text' => $this->t('Here is a list of modules that send emails (`%` is one of a specific mail key provided by the module):'),
      ],
      [
        '#theme' => 'item_list',
        '#items' => $mail_modules,
      ],
      [
        '#type' => 'item',
        '#description' => $this->t('Provide a line-delimited list of message keys to be rerouted in the text areas below.<br/>Either module machine name or specific mail key can be used for that. If left empty (as it is by default), all emails will be selected for rerouting.'),
      ],
    ];

    $form['mailkeys'][RerouteEmailConstants::REROUTE_EMAIL_MAILKEYS] = [
      '#title' => $this->t('Filter emails FOR rerouting by their mail keys:'),
      '#type' => 'textarea',
      '#rows' => 2,
      '#element_validate' => [[$this, 'validateMultipleUnique']],
      '#pre_render' => [[$this, 'textareaRowsValue']],
      '#default_value' => $this->rerouteConfig->get(RerouteEmailConstants::REROUTE_EMAIL_MAILKEYS),
      '#description' => $this->t('Use case: we need to reroute only a few specific mail keys (specified mail keys will be rerouted, all other emails will NOT be rerouted).'),
      '#config' => [
        'key' => 'reroute_email.settings:' . RerouteEmailConstants::REROUTE_EMAIL_MAILKEYS,
      ],
    ];

    $form['mailkeys'][RerouteEmailConstants::REROUTE_EMAIL_MAILKEYS_SKIP] = [
      '#title' => $this->t('Filter emails FROM rerouting by their mail keys:'),
      '#type' => 'textarea',
      '#rows' => 2,
      '#element_validate' => [[$this, 'validateMultipleUnique']],
      '#pre_render' => [[$this, 'textareaRowsValue']],
      '#default_value' => $this->rerouteConfig->get(RerouteEmailConstants::REROUTE_EMAIL_MAILKEYS_SKIP),
      '#description' => $this->t('Use case: we need to reroute all outgoing emails except a few mail keys (specified mail keys will NOT be rerouted, all other emails will be rerouted).'),
      '#config' => [
        'key' => 'reroute_email.settings:' . RerouteEmailConstants::REROUTE_EMAIL_MAILKEYS_SKIP,
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Adjust rows value according to the content size.
   *
   * @param array $element
   *   The render array to add the access denied message to.
   *
   * @return array
   *   The updated render array.
   */
  public static function textareaRowsValue(array $element): array {
    $size = mb_substr_count($element['#default_value'] ?? '', PHP_EOL) + 1;
    if ($size > $element['#rows']) {
      $element['#rows'] = min($size, 10);
    }
    return $element;
  }

  /**
   * Validate multiple email addresses field.
   *
   * @param array $element
   *   A field array to validate.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function validateMultipleEmails(array $element, FormStateInterface $form_state): void {
    // Allow only valid email addresses.
    $addresses = reroute_email_split_string($form_state->getValue($element['#name']));
    foreach ($addresses as $address) {
      if (!$this->emailValidator->isValid($address)) {
        $form_state->setErrorByName($element['#name'], $this->t('@address is not a valid email address.', ['@address' => $address]));
      }
    }
  }

  /**
   * Validate multiple email addresses field.
   *
   * @param array $element
   *   A field array to validate.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function validateMultipleUnique(array $element, FormStateInterface $form_state): void {
    // String "email@example.com; ;; , ,," save just as "email@example.com".
    // This will be ignored if any validation errors occur.
    $form_state->setValue($element['#name'], implode($element['#reroute_config_delimiter'] ?? PHP_EOL, reroute_email_split_string($form_state->getValue($element['#name']))));
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->rerouteConfig
      ->set(RerouteEmailConstants::REROUTE_EMAIL_ENABLE, $form_state->getValue(RerouteEmailConstants::REROUTE_EMAIL_ENABLE))
      ->set(RerouteEmailConstants::REROUTE_EMAIL_ADDRESS, $form_state->getValue(RerouteEmailConstants::REROUTE_EMAIL_ADDRESS))
      ->set(RerouteEmailConstants::REROUTE_EMAIL_ALLOWLIST, $form_state->getValue(RerouteEmailConstants::REROUTE_EMAIL_ALLOWLIST))
      ->set(RerouteEmailConstants::REROUTE_EMAIL_ROLES, array_values(array_filter($form_state->getValue(RerouteEmailConstants::REROUTE_EMAIL_ROLES))))
      ->set(RerouteEmailConstants::REROUTE_EMAIL_DESCRIPTION, $form_state->getValue(RerouteEmailConstants::REROUTE_EMAIL_DESCRIPTION))
      ->set(RerouteEmailConstants::REROUTE_EMAIL_MESSAGE, $form_state->getValue(RerouteEmailConstants::REROUTE_EMAIL_MESSAGE))
      ->set(RerouteEmailConstants::REROUTE_EMAIL_MAILKEYS, $form_state->getValue(RerouteEmailConstants::REROUTE_EMAIL_MAILKEYS))
      ->set(RerouteEmailConstants::REROUTE_EMAIL_MAILKEYS_SKIP, $form_state->getValue(RerouteEmailConstants::REROUTE_EMAIL_MAILKEYS_SKIP))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
