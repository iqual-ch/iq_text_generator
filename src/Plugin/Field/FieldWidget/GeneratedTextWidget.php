<?php

namespace Drupal\iq_text_generator\Plugin\Field\FieldWidget;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\StringTextareaWidget;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\iq_text_generator\TextGeneratorSourcePluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'generated_text_widget' widget.
 *
 * @FieldWidget(
 *   id = "generated_text_widget",
 *   label = @Translation("Generated Text Widget"),
 *   field_types = {
 *     "string_long"
 *   }
 * )
 */
class GeneratedTextWidget extends StringTextareaWidget {

  /**
   * Constructs a InlineEntityFormComplex object.
   *
   * @param string $plugin_id
   *   The plugin_id for the widget.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the widget is associated.
   * @param array $settings
   *   The widget settings.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   Module handler service.
   * @param \Drupal\iq_text_generator\TextGeneratorSourcePluginManager $textGeneratorSourcePluginManager
   *   The text generator source plugin manager.
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    array $third_party_settings,
    protected ModuleHandlerInterface $moduleHandler,
    protected TextGeneratorSourcePluginManager $textGeneratorSourcePluginManager,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
  ) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('module_handler'),
      $container->get('plugin.manager.iq_text_generator.text_generator_source')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $defaults = parent::defaultSettings();
    $defaults += [
      'source_id' => NULL,
      'output_type' => NULL,
    ];

    return $defaults;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);

    // Get the definitions of all plugins.
    $definitions = $this->textGeneratorSourcePluginManager->getDefinitions();

    // Prepare an options array.
    $options = [];
    foreach ($definitions as $id => $definition) {
      $options[$id] = $definition['label'];
    }

    $element['source_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Text Generator Source'),
      '#default_value' => $this->getSetting('source_id'),
      '#options' => $options,
      '#required' => TRUE,
    ];

    $element['output_type'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Output type'),
      '#default_value' => $this->getSetting('output_type') ?? 'blog',
      '#required' => TRUE,
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
    $definitions = $this->textGeneratorSourcePluginManager->getDefinitions();

    if (isset($definitions[$this->getSetting('source_id')])) {
      $label = $definitions[$this->getSetting('source_id')]['label'];
      $summary[] = $this->t('Text Generator Source: @source', ['@source' => $label]);
    }

    $summary[] = $this->t('Output type: @type', ['@type' => $this->getSetting('output_type')]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $element['#attached']['library'][] = 'iq_text_generator/generated-text';
    $element['#attributes']['class'][] = 'generated-text-widget';
    $element['#theme'] = 'generated_text';
    $element['#attached']['drupalSettings']['iq_text_generator'] = $this->setDrupalSettings($element);

    return $element;
  }

  /**
   * Set the Drupal settings for the widget.
   *
   * @param array $element
   *   The element array.
   *
   * @return array
   *   The Drupal settings array.
   */
  protected function setDrupalSettings(array $element) {
    $inputs = [
      'parameters' => [
        'location' => 'Berlin, Germany',
        'keywords' => 'Nightlife, shopping',
        'themes' => 'City trips',
        'language' => 'English',
        'llm_model_name' => 'gemini-pro',
      ],
      'persona' => 'HotelPlan',
      'languages' => [
        'English'
      ],
      'output_type' => 'blog',
    ];
    $inputs = $this->getInputs($element);
    $url = Url::fromRoute('iq_text_generator.generate_text')->toString();
    return [
      'source' => $this->getSetting('source_id'),
      'inputs' => $inputs,
      'url' => $url,
    ];
  }

  /**
   * Get the inputs for the text generator.
   *
   * @param array $element
   *   The element array.
   *
   * @return array
   *   The inputs array.
   */
  protected function getInputs(array $element) {
    $inputs = [];
    $inputs['output_type'] = $this->getSetting('output_type');
    $this->moduleHandler->alter('iq_text_generator_inputs', $inputs, $element);
    return $inputs;
  }

}
