<?php

namespace Drupal\iq_text_generator\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Text Generator for a text generator item annotation object.
 *
 * @see \Drupal\iq_text_generator\TextGeneratorSourcePluginBase
 * @see plugin_api
 *
 * @Annotation
 */
class TextGeneratorPlugin extends Plugin {

  /**
   * Style definition machine name.
   *
   * @var string
   */
  public $id;

  /**
   * Style definition label.
   *
   * @var string
   */
  public $label;

}
