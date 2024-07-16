<?php

namespace Drupal\iq_text_generator;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Defines a listing of text generator source entities.
 *
 * @see \Drupal\iq_text_generator\Entity\TextGeneratorSource
 */
class TextGeneratorSourceListBuilder extends ConfigEntityListBuilder {
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header = [];
    $header['label'] = $this->t('Label');
    $header['id'] = $this->t('Machine name');
    $header['plugin_id'] = $this->t('Text Generator Type');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row = [];
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['plugin_id'] = $entity->get('plugin_id');
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);
    if ($entity->hasLinkTemplate('edit-form')) {
      $operations['edit'] = [
        'title' => $this->t('Edit text generator'),
        'weight' => 20,
        'url' => $entity->toUrl('edit-form'),
      ];
    }

    return $operations;
  }

}
