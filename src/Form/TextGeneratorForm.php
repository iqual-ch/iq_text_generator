<?php

namespace Drupal\iq_text_generator\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\iq_text_generator\textGeneratorPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the text generator settings form.
 */
class TextGeneratorForm extends EntityForm {

  use StringTranslationTrait;

  /**
   * The messenger.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * The plugin manager.
   *
   * @var \Drupal\iq_text_generator\TextGeneratorPluginManager
   */
  protected $pluginManager;

  /**
   * Constructs a ProfileForm object.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config
   *   The config factory.
   * @param \Drupal\iq_text_generator\TextGeneratorPluginManager $pluginManager
   *   The plugin manager.
   */
  public function __construct(MessengerInterface $messenger, ConfigFactoryInterface $config, TextGeneratorPluginManager $pluginManager) {
    $this->messenger = $messenger;
    $this->config = $config;
    $this->pluginManager = $pluginManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger'),
      $container->get('config.factory'),
      $container->get('plugin.manager.iq_text_generator.text_generator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    /** @var \Drupal\iq_text_generator\Entity\TextGenerator $textGenerator */
    $textGenerator = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#default_value' => $textGenerator->label(),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $textGenerator->id(),
      '#required' => TRUE,
      '#machine_name' => [
        'exists' => [$this, 'textGeneratorExists'],
        'replace_pattern' => '[^a-z0-9_.]+',
      ],
    ];

    $options = [];
    foreach ($this->pluginManager->getDefinitions() as $pluginId => $textGeneratorPlugin) {
      $options[$pluginId] = $textGeneratorPlugin['label'];
    }

    $form['plugin_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Text Generator Type'),
      '#default_value' => $textGenerator->plugin_id,
      '#required' => TRUE,
      '#options' => $options,
    ];

    if ($textGenerator->plugin_id) {
      $form['plugin_id']['#disabled'] = TRUE;

      $textGeneratorPlugin = $this->pluginManager->createInstance($textGenerator->plugin_id);

      $form['settings'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Settings'),
      ] + $textGeneratorPlugin->buildConfigurationForm($textGenerator->settings);
    }

    if ($textGenerator->id()) {
      $form['id']['#disabled'] = TRUE;
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {

    $this->entity->set('label', trim((string) $form_state->getValue('label')));
    $this->entity->set('id', $form_state->getValue('id'));
    $this->entity->set('plugin_id', $form_state->getValue('plugin_id'));

    if ($this->entity->get('plugin_id')) {
      $textGeneratorPlugin = $this->pluginManager->createInstance($this->entity->get('plugin_id'));
      $settings = $textGeneratorPlugin->processConfigurationForm($form, $form_state, $this->entity);
      $this->entity->set('settings', $settings);
    }

    $status = $this->entity->save();

    // Tell the user we've updated/added the data source.
    $action = $status == SAVED_UPDATED ? 'updated' : 'added';
    $this->messenger()->addStatus($this->t(
      'Text Generator %label has been %action.',
      ['%label' => $this->entity->label(), '%action' => $action]
    ));
    $this->logger('text_generator')
      ->notice(
        'Text Generator %label has been %action.',
        ['%label' => $this->entity->label(), '%action' => $action]
      );

    // Redirect back to the list view if data source has been updated.
    if ($status == SAVED_UPDATED) {
      $form_state->setRedirect('entity.text_generator.collection');
    }
    else {
      $form_state->setRedirect('entity.text_generator.edit_form', [
        'text_generator' => $this->entity->get('id'),
      ]);
    }

  }

  /**
   * Checks if a text generator name is taken.
   *
   * @param string $value
   *   The machine name.
   * @param array $element
   *   An array containing the structure of the 'id' element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return bool
   *   Whether or not the text generator name is taken.
   */
  public function textGeneratorExists($value, array $element, FormStateInterface $form_state) {
    /** @var \Drupal\Core\Config\Entity\ConfigEntityInterface $profile */
    $textGenerator = $form_state->getFormObject()->getEntity();
    return (bool) $this->entityTypeManager->getStorage($textGenerator->getEntityTypeId())
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition($textGenerator->getEntityType()->getKey('id'), $value)
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    $textGeneratorPlugin = $this->pluginManager->createInstance($this->entity->plugin_id);
    $textGeneratorPlugin->validateConfigurationForm($form, $form_state);
  }

}

