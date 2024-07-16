<?php

namespace Drupal\iq_text_generator\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\iq_text_generator\Service\TextGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for the text generator.
 */
class TextGeneratorController extends ControllerBase {

  /**
   * Constructs a TextGeneratorController object.
   *
   * @param \Drupal\iq_text_generator\Service\TextGeneratorInterface $textGenerator
   *   The text generator.
   */
  public function __construct(
    protected TextGeneratorInterface $textGenerator
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('iq_text_generator.text_generator')
    );
  }

  /**
   * Generate text.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The response object.
   */
  public function generateText(Request $request) {
    $data = json_decode($request->getContent(), TRUE);
    $text = $this->textGenerator->generateText($data['source'], $data['inputs']);

    return new JsonResponse(['text' => $text]);
  }

}
