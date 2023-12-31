<?php

namespace Drupal\tour_ui\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Builds the form to delete a tour tip.
 */
class TourTipDeleteForm extends ConfirmFormBase {

  /**
   * Stores the tour entity being deleted.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected $entity;

  /**
   * Stores the tour tip candidate for deletion.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected $tip;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The Messenger Service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs a new TourTipDeleteForm object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity manager.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The Messenger Service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, MessengerInterface $messenger) {
    $this->entityTypeManager = $entity_type_manager;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'tour_ui_tip_confirm_delete';
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return Url::fromRoute('entity.tour.edit_form', ['tour' => $this->entity->id()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the %tour tour %tip tip?', [
      '%tour' => $this->entity->label(),
      '%tip' => $this->tip->get('label'),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $tour = '', $tip = '') {
    $this->entity = $this->entityTypeManager->getStorage('tour')->load($tour);
    $tour = $this->entity;
    $this->tip = $tour->getTip($tip);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Rebuild the tips and remove the irrelevant one.
    $candidate = $this->tip->get('id');
    $tips = [];
    foreach ($this->entity->getTips() as $tip) {
      $tip_id = $tip->get('id');
      if ($tip_id == $candidate) {
        continue;
      }
      $tips[$tip_id] = $tip->getConfiguration();
    }
    $this->entity->set('tips', $tips);
    $this->entity->save();

    $form_state->setRedirect('entity.tour.edit_form', ['tour' => $this->entity->id()]);
    $this->messenger->addMessage($this->t('Deleted the %tour tour %tip tip.', [
      '%tour' => $this->entity->label(),
      '%tip' => $this->tip->get('label'),
    ]));
  }

}
