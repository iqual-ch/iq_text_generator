<?php

namespace Drupal\iq_text_generator_ftp\Plugin\TextGenerator\TextGenerator;

use Drupal\Core\Form\FormStateInterface;
use Drupal\iq_text_generator\TextGeneratorPluginBase;
use Drupal\iq_text_generator\Entity\TextGenerator;

/**
 * Provids FTP import functionality.
 *
 * @TextGeneratorPlugin(
 *   id = "gemini_text_generator",
 *   label = @Translation("Gemini Text Generator"),
 * )
 */
class GeminiTextGenerator extends TextGeneratorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm($values = []) {

    $form = [];
    $form['host'] = [
      '#type' => 'textfield',
      '#default_value' => $values['host'] ?? '',
      '#title' => $this->t('Host'),
      '#description' => $this->t('Exclude protocol.'),
      '#required' => TRUE,
    ];

    $form['user'] = [
      '#type' => 'textfield',
      '#default_value' => $values['user'] ?? '',
      '#title' => $this->t('User'),
      '#required' => TRUE,
    ];

    $form['service_account_file_path'] = [
      '#type' => 'textfield',
      '#default_value' => $values['service_account_file_path'] ?? '',
      '#title' => $this->t('Service Account File Path'),
      '#required' => TRUE,
    ];

    $form['generate_endpoint'] = [
      '#type' => 'textfield',
      '#default_value' => $values['generate_endpoint'] ?? '',
      '#title' => $this->t('Endpoint to generate text'),
    ];

    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    $input = $form_state->getValue('host');
    if ($input) {
      if (strpos($input, ':') !== FALSE || strpos($input, '//') !== FALSE) {
        $form_state->setErrorByName('host', $this->t('The host must not contain the protocol (e.g. ftp://).'));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function processConfigurationForm(array &$form, FormStateInterface $form_state, TextGenerator $entity) {
    $settings = [];
    $settingKeys = array_keys($this->buildConfigurationForm());
    foreach ($settingKeys as $key) {
      $settings[$key] = $form_state->getValue($key);
    }

    return $settings;
  }

}

