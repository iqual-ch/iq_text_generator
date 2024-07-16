<?php

namespace Drupal\iq_text_generator\Controller;

use Drupal\Component\Serialization\Json;
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
    $cleaned_output = FALSE;
    $data = Json::decode($request->getContent());
    $text = $this->textGenerator->generateText($data['source'], $data['inputs']);
    if ($text) {
      $array = Json::decode($text);
      if (is_array($array) && isset($array[0]['output'])) {
        $output = $array[0]['output'];
        // @todo once rules are clear
        // $cleaned_output = $this->getCleanOutput($output);
        $cleaned_output = $output;
      }
    }

    return new JsonResponse(['text' => $cleaned_output]);
  }

  /**
   * Get cleaned output.
   *
   * @param string $output
   *   The output.
   *
   * @return string
   *   The cleaned output.
   */
  protected function getCleanOutput($output) {
    $cleaned_output = '';
    // Replace one # or more followed by a space with an empty string.
    $cleaned_output = preg_replace('/#+\s/', '', $output);
    $cleaned_output = trim($cleaned_output);
    return $cleaned_output;
  }

}
