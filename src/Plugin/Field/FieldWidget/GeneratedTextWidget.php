<?php

namespace Drupal\iq_text_generator\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\StringTextareaWidget;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'generated_text_widget' widget.
 *
 * @FieldWidget(
 *   id = "generated_text_widget",
 *   label = @Translation("Generated Text Widget"),
 *   field_types = {
 *     "string_long"
 *   }
 * )
 */
class GeneratedTextWidget extends StringTextareaWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $element['#attached']['library'][] = 'iq_text_generator/generated-text';
    $element['#attributes']['class'][] = 'generated-text-widget';
    $element['#theme'] = 'generated_text';

    return $element;
  }

}