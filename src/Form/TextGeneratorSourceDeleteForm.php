<?php

namespace Drupal\iq_text_generator\Form;

use Drupal\Core\Entity\EntityDeleteForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Builds the form to delete an Text Generator Source.
 */
class TextGeneratorSourceDeleteForm extends EntityDeleteForm {

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->entity->delete();
    $this->messenger()->addMessage($this->t('Text Generator %label has been deleted.', ['%label' => $this->entity->label()]));
    $form_state->setRedirect('entity.text_generator_source.collection');
  }

}
