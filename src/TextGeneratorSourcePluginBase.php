<?php

namespace Drupal\iq_text_generator;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\iq_text_generator\Entity\TextGeneratorSource;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for Text Generator plugins.
 *
 * @see \Drupal\iq_text_generator\Annotation\TextGeneratorSourcePlugin
 * @see \Drupal\iq_text_generator\TextGeneratorSourcePluginInterface
 */
abstract class TextGeneratorSourcePluginBase extends PluginBase implements TextGeneratorSourcePluginInterface, ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
    );
  }

  /**
   * Return the plugins label.
   *
   * @return string
   *   returns the label as a string.
   */
  public function getLabel() {
    return $this->pluginDefinition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    // Validation is optional.
  }

  /**
   * {@inheritdoc}
   */
  public function processConfigurationForm(array &$form, FormStateInterface $form_state, TextGeneratorSource $entity) {
    $settings = [];
    $settingKeys = array_keys($this->buildConfigurationForm());
    foreach ($settingKeys as $key) {
      $settings[$key] = $form_state->getValue($key);
    }
    return $settings;
  }

}
