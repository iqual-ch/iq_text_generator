<?php

namespace Drupal\iq_text_generator;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\iq_text_generator\Entity\TextGeneratorSource;

/**
 * Defines an interface for Text Generator Source plugins.
 */
interface TextGeneratorSourcePluginInterface extends PluginInspectionInterface {

  /**
   * Return the plugins label.
   *
   * @return string
   *   Returns the label as a string.
   */
  public function getLabel();

  /**
   * Create speficic configuration for data source.
   *
   * @return array
   *   Render array for configuration form.
   */
  public function buildConfigurationForm(array $values = []);

  /**
   * Custom validation for configuration form.
   *
   * @param array $form
   *   Render array of configuratoin form.
   * @param Drupal\Core\Form\FormStateInterface $form_state
   *   Form state, containing submitted form data.
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state);

  /**
   * Processes submitted form data into an array of setting.
   *
   * @param array $form
   *   Render array of configuratoin form.
   * @param Drupal\Core\Form\FormStateInterface $form_state
   *   Form state, containing submitted form data.
   * @param Drupal\iq_text_generator\Entity\TextGeneratorSource $entity
   *   The text generator source being processed.
   *
   * @return array
   *   Array containing submitted & processed form values.
   */
  public function processConfigurationForm(array &$form, FormStateInterface $form_state, TextGeneratorSource $entity);

  /**
   * Generate text based on configuration and inputs.
   *
   * @param array $config
   *   Configuration array.
   * @param array $inputs
   *   Array of inputs.
   *
   * @return string
   *   Generated text.
   */
  public function generateText(array $config, array $inputs);

}

