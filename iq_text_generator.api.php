<?php

/**
 * @file
 * Hooks provided by the IQ Text Generator module.
 */

use Drupal\Core\Form\FormStateInterface;
use Drupal\iq_text_generator\Entity\TextGeneratorSource;

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Alter the inputs for the text generator.
 *
 * @param array &$inputs
 *   The inputs array.
 * @param array $element
 *   The element array.
 * @param \Drupal\Core\Form\FormStateInterface $form_state
 *   The form state.
 */
function hook_iq_text_generator_inputs_alter(
  array &$inputs,
  array $element,
  FormStateInterface $form_state,
) {
  $inputs['Languages'] = [
    'English',
    'Spanish',
  ];
}

/**
 * @} End of "addtogroup hooks".
 */
