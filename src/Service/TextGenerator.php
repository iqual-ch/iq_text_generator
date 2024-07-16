<?php

namespace Drupal\iq_text_generator\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\iq_text_generator\TextGeneratorSourcePluginManager;

/**
 * Provides a text generator class.
 */
class TextGenerator implements TextGeneratorInterface {

  /**
   * The source configuration.
   *
   * @var array
   */
  private $config;

  /**
   * The source plugin.
   *
   * @var \Drupal\iq_text_generator\TextGeneratorSourcePluginInterface
   */
  private $plugin;

  /**
   * Constructs a TextGenerator object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\iq_text_generator\TextGeneratorSourcePluginManager $pluginManager
   *   The plugin manager.
   */
  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
    protected TextGeneratorSourcePluginManager $pluginManager
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public function generateText($sourceId, $inputs) {
    $this->setSource($sourceId);
    return $this->plugin->generateText($this->config, $inputs);
  }

  /**
   * {@inheritdoc}
   */
  public function setSource($sourceId) {
    $source = $this->entityTypeManager->getStorage('text_generator_source')->load($sourceId);
    if (!$source) {
      throw new \Exception('Invalid source');
    }
    $this->config = $source->settings;
    $this->plugin = $this->pluginManager->createInstance($source->plugin_id);
  }

}