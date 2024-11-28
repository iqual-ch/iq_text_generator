<?php

namespace Drupal\iq_text_generator\Service;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Http\ClientFactory;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Google\Auth\ApplicationDefaultCredentials;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Provides a text generator class.
 */
class TextGenerator implements TextGeneratorInterface {

  use StringTranslationTrait;

  /**
   * The api configuration.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  private $config;

  /**
   * A Guzzle HTTP Client.
   *
   * @var \Guzzle\Client
   */
  protected $httpClient;

  /**
   * The token.
   *
   * @var string
   */
  protected $token = FALSE;

  /**
   * The logger.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * Constructs a TextGenerator object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Http\ClientFactory $httpClientFactory
   *   The http client factory.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger factory.
   */
  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
    protected ClientFactory $httpClientFactory,
    ConfigFactoryInterface $configFactory,
    LoggerChannelFactoryInterface $logger_factory,
  ) {
    $this->config = $configFactory->get('iq_text_generator.settings');
    $this->logger = $logger_factory->get('text_generator');
  }

  /**
   * {@inheritdoc}
   */
  public function generateText(array $inputs) {
    $this->establishConnection();
    $response = $this->sendRequest('POST', $this->config->get('generate_endpoint'), [
      'json' => $inputs,
    ]);

    if ($response) {
      return $response->getBody()->getContents();
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function establishConnection() {
    $this->httpClient = $this->httpClientFactory->fromOptions([
      'base_uri' => $this->config->get('base_url'),
      'headers' => [
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $this->getIdToken(),
      ],
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getIdToken() {
    if (!$this->token) {
      $credentials = ApplicationDefaultCredentials::getCredentials([$this->config->get('base_url')]);
      try {
        $authToken = $credentials->fetchAuthToken();
        $this->token = $authToken['id_token'];
      }
      catch (\Exception $error) {
        $this->logger->error(
          'Remote API Connection',
          [],
          $this->t('An error occurred while trying to fetch the auth token. The reported error was @error', ['@error' => $error->getMessage()]));
      }
    }

    return $this->token;
  }

  /**
   * Helper function to encapsulate send request and catch error.
   *
   * @param string $method
   *   The method GET|POST|PATCH.
   * @param string $url
   *   The endpoint url.
   * @param array $args
   *   Custom headers, form_params, data.
   */
  private function sendRequest($method, $url, array $args = []) {
    try {
      $response = $this->httpClient->request($method, $url, $args);
      return $response;
    }
    catch (GuzzleException $error) {
      $message = new FormattableMarkup(
        'API connection error. Error details are as follows:<pre>@response</pre>',
        ['@response' => $error->getMessage()]
          );
      $this->logger->error($message);
    }
    catch (\Exception $error) {
      $this->logger->error(
        $this->t('An unknown error occurred while trying to connect to the remote API. This is not a Guzzle error, nor an error in the remote API, rather a generic local error ocurred. The reported error was @error', ['@error' => $error->getMessage()])
      );
    }
    return FALSE;
  }

}