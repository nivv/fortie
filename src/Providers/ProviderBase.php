<?php namespace Nivv\Fortie\Providers;

/*

   Copyright 2015 Andreas Göransson

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.

*/

use Nivv\Fortie\MissingRequiredAttributeException;


/**
 * Base provider for the all Fortnox providers, each provider includes a
 * path (the URL extension for the provider, for example "accounts") and
 * a set of attributes (both writeable and required).
 *
 * Before a request is sent to Fortnox the supplied parameter array will
 * be sanitized according to the rules in Fortnox defined by the online
 * documentation (http://developer.fortnox.se/documentation/). When the
 * data has been verified the data is sent to the Guzzle client.
 *
 * The response (either XML or JSON) is then turned into an array and
 * retured to the caller.
 */
class ProviderBase
{

  /**
   * A reference to the client in Fortie.php
   */
  protected $client = null;


  /**
   * The base path for the Provider.
   */
  protected $path = null;


  /**
   * List of readable attributes.
   */
  protected $attributes = [
  ];


  /**
   * The writeable attributes.
   */
  protected $writeable = [
  ];


  /**
   * The minimum required attributes for a write request.
   */
  protected $required = [
  ];


  /**
   * Create a new provider instance, pass the Guzzle client
   * reference.
   *
   * @return void
   */
  public function __construct(&$client)
  {
    $this->client = $client;
  }


  /**
   * Handle the response, whether it's JSON or XML.
   */
  protected function handleResponse (\GuzzleHttp\Psr7\Response $response)
  {
    $content_type = $response->getHeader('Content-Type');

    if (in_array('application/json', $content_type)) {
      return json_decode($response->getBody());
    }

    else if (in_array('application/xml', $content_type)) {
      $reader = new \Sabre\Xml\Reader();
      $reader->xml($response->getBody());
      return $reader->parse();
    }
  }

  /**
   * Send a HTTP request to Fortnox.
   */
  public function sendRequest ($method = 'GET', $paths = null, $bodyWrapper = null, $data = null, $params = null, $filePath = null)
  {
    // Start building the URL
    $URL = 'https://api.fortnox.se/3/' . $this->path . '/';
    // Add the extra paths, if there are any
    if (!is_null($paths)) {
      // If array, add all paths
      if (is_array($paths)) {
        foreach ($paths as $path) {
          $URL .= $path . '/';
        }
      }
      // Otherwise, add just the first
      else {
        $URL .= $paths . '/';
      }
    }

    // Apply the URL parameters, this must be an associative array
    if (!is_null($params) && is_array($params)) {
      $i = 0;
      foreach ($params as $key => $param) {
        // ?
        if ($i == 0) {
          $URL .= '?' . $key . '=' . $param;
        }
        // &
        else {
          $URL .= '&' . $key . '=' . $param;
        }
        $i++;
      }
    }

    $response = null;

    try {
      switch ($method) {
        case 'delete':
        case 'DELETE':
          $response = $this->client->delete($URL);
          break;

        case 'get':
        case 'GET':
          $response = $this->client->get($URL);
          break;
        
        case 'post':
        case 'POST':
          $body = $this->handleData($bodyWrapper, $data);
          if (is_null($body)) {
            // Upload file instead of data
            $fileData = Guzzle\Http\EntityBody::factory(fopen($filePath, 'r+'));
            $response = $this->client->post($URL, $fileData);
          }
          else if (!is_null($body) && is_array($body)) {
            $response = $this->client->post($URL, ['json' => $body]);
          }
          break;

        case 'put':
        case 'PUT':
          $body = $this->handleData($bodyWrapper, $data);
          if (is_null($body)) {
            // Upload file instead of data
            $fileData = Guzzle\Http\EntityBody::factory(fopen($filePath, 'r+'));
            $response = $this->client->put($URL, $fileData);
          }
          else if (!is_null($body) && is_array($body)) {
            $response = $this->client->put($URL, ['json' => $body]);
          }
      }

      return $this->handleResponse($response);
    }
    catch (\GuzzleHttp\Exception\ClientException $e) {
      $response = $e->getResponse();
      $responseBodyAsString = $response->getBody()->getContents();
      //echo $responseBodyAsString;
    }
  }


  /**
   * This will perform filtering on the supplied data, used when 
   * uploading data to Fortnox.
   */
  protected function handleData ($bodyWrapper, $data, $sanitize = true)
  {
    // Filter invalid data
    $filtered = array_intersect_key($data, array_flip($this->attributes));;

    // Filter non-writeable data
    $writeable = array_intersect_key($filtered, array_flip($this->writeable));

    // Make sure all required data are set
    if (! count(array_intersect_key(array_flip($this->required), $writeable)) === count($this->required)) {
      throw new MissingRequiredAttributeException;
    }

    // Sanitize input 
    // See: http://guzzle3.readthedocs.org/http-client/request.html#post-requests
    if ($sanitize) {
      foreach ($writeable as $key => $value) {
        $value = str_replace('@', '', $value);
      }
    }

    // Wrap the body as required by Fortnox
    $body = [
      $bodyWrapper => $writeable
    ];

    print_r($body);
    return $body;
  }

}
