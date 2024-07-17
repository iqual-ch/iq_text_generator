<?php

namespace Drupal\iq_text_generator\Plugin\Field\FieldWidget;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\StringTextareaWidget;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
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
   * The configuration.
   *
   * @var \Drupal\Core\Config\ConfigInterface
   */
  protected $config;

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
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory service.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   Module handler service.
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    array $third_party_settings,
    ConfigFactoryInterface $configFactory,
    protected ModuleHandlerInterface $moduleHandler,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->config = $configFactory->get('iq_text_generator.settings');
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
      $container->get('config.factory'),
      $container->get('module_handler'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $defaults = parent::defaultSettings();
    $defaults += [
      'output_type' => NULL,
      'themes' => NULL,
    ];

    return $defaults;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);

    $element['output_type'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Output type'),
      '#default_value' => $this->getSetting('output_type') ?? 'blog',
      '#required' => TRUE,
    ];

    $element['themes'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Themes'),
      '#default_value' => $this->getSetting('themes'),
      '#required' => TRUE,
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();

    $summary[] = $this->t('Output type: @type', ['@type' => $this->getSetting('output_type')]);

    $summary[] = $this->t('Themes: @themes', ['@themes' => $this->getSetting('themes')]);

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
    $element['#attached']['drupalSettings']['iq_text_generator'] = $this->setDrupalSettings($element, $form_state);

    return $element;
  }

  /**
   * Set the Drupal settings for the widget.
   *
   * @param array $element
   *   The element array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array
   *   The Drupal settings array.
   */
  protected function setDrupalSettings(array $element, FormStateInterface $form_state) {
    $inputs = $this->getInputs($element, $form_state);
    $url = Url::fromRoute('iq_text_generator.generate_text')->toString();
    return [
      'inputs' => $inputs,
      'url' => $url,
    ];
  }

  /**
   * Get the inputs for the text generator.
   *
   * @param array $element
   *   The element array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array
   *   The inputs array.
   */
  protected function getInputs(array $element, FormStateInterface $form_state) {
    $inputs = [
      'persona' => $this->config->get('persona'),
      'parameters' => [
        'location' => '',
        'keywords' => '',
        'themes' => $this->getSetting('themes'),
        'language' => '',
        'llm_model_name' => $this->config->get('llm_model_name'),
      ],
      'languages' => [],
      'output_type' => $this->getSetting('output_type'),
    ];
    $this->moduleHandler->alter('iq_text_generator_inputs', $inputs, $element, $form_state);
    return $inputs;
  }

}
