<?php

namespace Drupal\iq_text_generator\Plugin\Field\FieldWidget;

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
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    array $third_party_settings,
    protected ModuleHandlerInterface $moduleHandler,
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
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $defaults = parent::defaultSettings();
    $defaults += [
      'persona' => 'HotelPlan',
      'output_type' => 'Blog',
      'themes' => NULL,
      'language' => 'English',
      'llm_model_name' => 'gemini-pro',
    ];

    return $defaults;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);

    $element['persona'] = [
      '#type' => 'select',
      '#title' => $this->t('Persona'),
      '#default_value' => $this->getSetting('persona'),
      '#options' => [
        'HotelPlan' => $this->t('HotelPlan'),
        'Travelhouse' => $this->t('Travelhouse'),
        'Migros Ferien' => $this->t('Migros Ferien'),
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
        'destination' => $this->t('Destination'),
      ],
      '#required' => TRUE,
    ];

    $element['language'] = [
      '#type' => 'select',
      '#title' => $this->t('Language'),
      '#default_value' => $this->getSetting('language'),
      '#options' => [
        'English' => $this->t('English'),
        'French' => $this->t('French'),
        'German' => $this->t('German'),
        'Italian' => $this->t('Italian'),
      ],
      '#required' => TRUE,
    ];

    $element['themes'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Themes'),
      '#default_value' => $this->getSetting('themes'),
    ];

    $element['llm_model_name'] = [
      '#type' => 'select',
      '#title' => $this->t('Language model'),
      '#default_value' => $this->getSetting('llm_model_name'),
      '#options' => [
        'gemini-pro' => $this->t('gemini-pro'),
        'text-bison@001' => $this->t('text-bison@001'),
        'text-bison@002' => $this->t('text-bison@002'),
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
    $summary[] = $this->t('Language: @language', ['@language' => $this->getSetting('language')]);
    $summary[] = $this->t('Themes: @themes', ['@themes' => $this->getSetting('themes')]);
    $summary[] = $this->t('Language model: @model', ['@model' => $this->getSetting('llm_model_name')]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $element['#attached']['library'][] = 'iq_text_generator/generated-text';
    $element['#prefix'] = '<div class="generated-text-widget" data-field-name="' . $items->getName() . '">';
    $element['#suffix'] = '</div>';
    $element['#theme'] = 'generated_text';
    $element['#attached']['drupalSettings']['iq_text_generator'][$items->getName()] = $this->setDrupalSettings($element, $form_state);

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
      'parameters' => [
        'location' => '',
        'keywords' => '',
        'themes' => $this->getSetting('themes'),
        'language' => $this->getSetting('language'),
        'llm_model_name' => $this->getSetting('llm_model_name'),
      ],
      'languages' => [$this->getSetting('language')],
      'output_type' => $this->getSetting('output_type'),
    ];
    $this->moduleHandler->alter('iq_text_generator_inputs', $inputs, $element, $form_state);
    return $inputs;
  }

}
