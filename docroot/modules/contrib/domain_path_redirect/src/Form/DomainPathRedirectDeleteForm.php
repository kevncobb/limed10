<?php

namespace Drupal\domain_path_redirect\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides the redirect delete confirmation form.
 */
class DomainPathRedirectDeleteForm extends ContentEntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the URL redirect from %source to %redirect?', ['%source' => $this->entity->getSourceUrl(), '%redirect' => $this->entity->getRedirectUrl()->toString()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('domain_path_redirect.list');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->entity->delete();
    $this->messenger()->addStatus(t('The redirect %redirect has been deleted.', ['%redirect' => $this->entity->getRedirectUrl()->toString()]));
    $form_state->setRedirect('domain_path_redirect.list');
  }

}
