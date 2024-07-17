<?php

namespace Drupal\iq_text_generator\Form;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provide the settings form for the Hotelplan Asktom.
 */
class TextGeneratorSettingsForm extends ConfigFormBase implements ContainerInjectionInterface {

  /**
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return ['iq_text_generator.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'iq_text_generator_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $settings = $this->config('iq_text_generator.settings');

    $form['base_url'] = [
      '#type' => 'textfield',
      '#default_value' => $values['base_url'] ?? '',
      '#title' => $this->t('Base URL'),
      '#description' => $this->t('Full Url of the app.'),
      '#required' => TRUE,
    ];

    $form['persona'] = [
      '#type' => 'textfield',
      '#default_value' => $values['persona'] ?? '',
      '#title' => $this->t('Gemini App User'),
      '#required' => TRUE,
    ];

    $form['llm_model_name'] = [
      '#type' => 'textfield',
      '#default_value' => $values['llm_model_name'] ?? '',
      '#title' => $this->t('LLM Model Name'),
      '#required' => TRUE,
    ];

    $form['generate_endpoint'] = [
      '#type' => 'textfield',
      '#default_value' => $values['generate_endpoint'] ?? '',
      '#title' => $this->t('Endpoint to generate text'),
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->cleanValues();

    $config = $this->config('iq_text_generator.settings');
    $config->set('base_url', $form_state->getValue('base_url'));
    $config->set('persona', $form_state->getValue('persona'));
    $config->set('llm_model_name', $form_state->getValue('llm_model_name'));
    $config->set('generate_endpoint', $form_state->getValue('generate_endpoint'));
    $config->save();

    parent::submitForm($form, $form_state);
  }

}
