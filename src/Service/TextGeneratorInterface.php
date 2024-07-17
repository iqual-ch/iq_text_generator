<?php

namespace Drupal\iq_text_generator\Service;

/**
 * Interface for the Text Generator service.
 */
interface TextGeneratorInterface {

  /**
   * Generate text from given inputs.
   *
   * @param array $inputs
   *   An array of inputs.
   *
   * @return string
   *   The generated text.
   */
  public function generateText(array $inputs);

  /**
   * Establish connection to the remote API.
   */
  public function establishConnection();

  /**
   * Get the ID token from the service account.
   *
   * @return string
   *   The ID token.
   */
  public function getIdToken();

}