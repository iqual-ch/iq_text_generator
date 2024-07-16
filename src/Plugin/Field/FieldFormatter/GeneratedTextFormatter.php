<?php

namespace Drupal\iq_text_generator\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\BasicStringFormatter;

/**
 * Plugin implementation of the 'generated text' formatter.
 *
 * @FieldFormatter(
 *   id = "generated_text_formatter",
 *   label = @Translation("Generated Text"),
 *   description = @Translation("Text plugin with AI generated text feature."),
 *   field_types = {
 *     "string_long"
 *   }
 * )
 */
class GeneratedTextFormatter extends BasicStringFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);
    $values = $items->getValue();

    foreach ($elements as $delta => $entity) {

    }

    return $elements;
  }

}
