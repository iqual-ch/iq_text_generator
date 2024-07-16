<?php

namespace Drupal\iq_text_generator\Service;

interface TextGeneratorInterface {
  
  /**
   * Generate text from given inputs.
   *
   * @param string $sourceId
   *   The source plugin id.
   * @param array $inputs
   *   An array of inputs
   *
   * @return string
   *   The generated text.
   */
  public function generateText($sourceId, $inputs);

  /**
   * Set the source plugin and config.
   *
   * @param string $sourceId
   *   The source plugin id.
   */
  public function setSource($sourceId);

}