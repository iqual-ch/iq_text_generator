<?php

namespace Drupal\iq_text_generator\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the text generator entity.
 *
 * @ConfigEntityType(
 *   id = "text_generator",
 *   label = @Translation("Text Generator for iQual Text Generator"),
 *   handlers = {
 *     "storage" = "Drupal\Core\Config\Entity\ConfigEntityStorage",
 *     "form" = {
 *       "default" = "Drupal\iq_text_generator\Form\TextGeneratorForm",
 *       "delete" = "Drupal\iq_text_generator\Form\TextGeneratorDeleteForm"
 *     },
 *     "list_builder" = "Drupal\iq_text_generator\TextGeneratorListBuilder"
 *   },
 *   admin_permission = "administer text generator",
 *   config_prefix = "text_generator",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "settings" = "settings",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "settings",
 *     "plugin_id",
 *   },
 *   links = {
 *     "add-form" = "/admin/config/services/text-generator/add",
 *     "edit-form" = "/admin/config/services/text-generator/{text_generator}/edit",
 *     "delete-form" = "/admin/config/services/text-generator/{text_generator}/delete",
 *     "import" = "/admin/config/services/text-generator/{text_generator}/import",
 *   }
 * )
 */
class TextGenerator extends ConfigEntityBase {

  /**
   * Profile machine name.
   *
   * @var string
   */
  public $id;

  /**
   * Profile human readable name.
   *
   * @var string
   */
  public $label;

  /**
   * ID of the text generator plugin.
   *
   * @var string
   */
  public $plugin_id;

  /**
   * The settings for this Text Generator.
   *
   * @var array
   */
  public $settings;

}
