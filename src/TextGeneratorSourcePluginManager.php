<?php

namespace Drupal\iq_text_generator;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Provides the Text Generator Source manager.
 */
class TextGeneratorSourcePluginManager extends DefaultPluginManager {

  /**
   * Seetings for plugin manager.
   *
   * @var array
   */
  protected $settings;

  /**
   * Constructor for TextGeneratorPluginManager objects.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/TextGenerator/TextGeneratorSource',
      $namespaces,
      $module_handler,
      'Drupal\iq_text_generator\TextGeneratorSourcePluginInterface',
      'Drupal\iq_text_generator\Annotation\TextGeneratorSourcerPlugin'
    );
    $this->alterInfo('iq_text_generator_plugin_info');
    $this->setCacheBackend($cache_backend, 'iq_text_generator_plugins');
  }

}
