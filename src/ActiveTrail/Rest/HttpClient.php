<?php

namespace ActiveTrail\Rest;
use ActiveTrail\Exception\NoApiTokenException;
use GuzzleHttp\Client;

/**
 * Class HttpClient
 * @package ActiveTrail\Rest
 */
class HttpClient {

  const CONNECTION_TIMEOUT = 5;
  const REQUEST_TIMEOUT = 10;
  private $apiToken;

  /**
   * HttpClient constructor.
   * @param $apiToken
   */
  public function __construct($apiToken) {
    $this->apiToken = $apiToken;
  }


  /**
   * General method for making API calls to ActiveTrail via GuzzleHttp.
   * @param $endpoint
   * @param $method
   * @param $payload
   * @param null $endpoint_params
   * @return \GuzzleHttp\Psr7\Response
   * @throws \Exception
   */
  public function MakeActiveTrailApiCall($endpoint, $method, $payload = null, $endpoint_params = null, $extra_headers = []){

    // First, make sure we have an authorization token
    if (empty($this->apiToken)) {
      throw new NoApiTokenException('You must provide an Api Token.');
    }

    // Process any endpoint params
    if (!empty($endpoint_params)) {
      foreach ($endpoint_params as $param_name => $param_value) {
        $endpoint = str_replace(':' . $param_name, $param_value, $endpoint);
      }
    }

    $client = new Client([ 'base_uri' => EndPoints::$API_BASE['uri'] ]);

    $request_options = [
      'connect_timeout' => self::CONNECTION_TIMEOUT,
      'timeout' => self::REQUEST_TIMEOUT,
      'headers' => []
    ];

    // Add additional headers if any were passed.
    if (!empty($extra_headers)) {
      $request_options['headers'] = $extra_headers;
    }

    // Add authorization header to top
    $request_options['headers'] = array_merge(['Authorization' => 'Basic ' . $this->apiToken], $request_options['headers']);

    // Add payload if one was provided.
    if (!empty($payload)) {
      $request_options['json'] = $payload;
    }

    return $client->request($method, $endpoint, $request_options);

  }

}

