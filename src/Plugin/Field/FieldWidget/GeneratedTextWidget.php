<?php

namespace Drupal\iq_text_generator\Plugin\Field\FieldWidget;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\StringTextareaWidget;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
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
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   *   Language manager service.
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    array $third_party_settings,
    protected ModuleHandlerInterface $moduleHandler,
    protected LanguageManagerInterface $languageManager,
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
      $container->get('language_manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $defaults = parent::defaultSettings();
    $defaults += [
      'persona' => 'Neutral',
      'output_type' => 'blog',
    ];

    return $defaults;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);

    // Hide the placeholder setting and set a default value.
    $element['placeholder']['#access'] = FALSE;

    $element['persona'] = [
      '#type' => 'select',
      '#title' => $this->t('Persona'),
      '#default_value' => $this->getSetting('persona'),
      '#options' => [
        'Hotelplan' => $this->t('HotelPlan'),
        'travelhouse' => $this->t('Travelhouse'),
        'tpt' => $this->t('TPT'),
        'Migros Ferien' => $this->t('Migros Ferien'),
        'Neutral' => $this->t('Neutral'),
      ],
      '#required' => TRUE,
    ];

    $element['output_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Output type'),
      '#default_value' => $this->getSetting('output_type'),
      '#options' => [
        'blog' => $this->t('Blog'),
        'hotel' => $this->t('Hotel'),
        'profile' => $this->t('Profile'),
        'destination' => $this->t('Destination'),
        'golf_course' => $this->t('Golf course'),
        'itinerary' => $this->t('Itinerary'),
      ],
      '#required' => TRUE,
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
    $summary[] = $this->t('Persona: @persona', ['@persona' => $this->getSetting('persona')]);
    $summary[] = $this->t('Output type: @type', ['@type' => $this->getSetting('output_type')]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    $class = 'generated-text-widget';
    if (!empty($element['value']['#default_value'])) {
      $class .= ' has-content';
    }
    $element['#id'] = 'generated-text-widget-' . $items->getName();
    $element['#field_name'] = $items->getName();
    $element['#attached']['library'][] = 'iq_text_generator/generated-text';
    $element['#prefix'] = '<div class="' . $class . '" data-field-name="' . $items->getName() . '">';
    $element['#suffix'] = '</div>';
    $element['#theme'] = 'generated_text';
    $element['#attached']['drupalSettings']['iq_text_generator'][$items->getName()] = $this->setDrupalSettings($element, $form_state);
    $element['#language'] = $this->getSetting('language');
    $element['value']['#placeholder'] = $this->t('No text generated yet.');
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
      'persona' => $this->getSetting('persona'),
      'parameters' => [],
      'languages' => [$this->getLanguage()['name']],
      'output_type' => $this->getSetting('output_type'),
    ];
    $this->moduleHandler->alter('iq_text_generator_inputs', $inputs, $element, $form_state);
    return $inputs;
  }

  /**
   * Get the current language.
   *
   * Default to English if the current language is not available.
   *
   * @return array
   *   The language array.
   */
  protected function getLanguage() {
    $available_languages = [
      'en' => [
        'name' => 'English',
        'label' => $this->t('English'),
      ],
      'de' => [
        'name' => 'German',
        'label' => $this->t('German'),
      ],
      'fr' => [
        'name' => 'French',
        'label' => $this->t('French'),
      ],
      'it' => [
        'name' => 'Italian',
        'label' => $this->t('Italian'),
      ],
    ];
    $current_language = $this->languageManager->getCurrentLanguage()->getId();
    return $available_languages[$current_language] ?? $available_languages['en'];
  }

}
