<?php

namespace AlgoDocs;

use \GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * Class AlgoDocsApiRequest
 *
 * provides a first layer of abstraction over Guzzle
 * transform responses from JSON and catches Exceptions
 * @internal
 * @package AlgoDocs
 */
class AlgoDocsApiRequest
{

    /**
     * @var Client
     */
    private $guzzle;

    private $apiKey;

    private $host;

    /**
     * constructor.
     * @param $host
     * @param $apiKey
     */
    public function __construct($host, $apiKey)
    {
        $this->host = $host;
        $this->apiKey = $apiKey;

        $this->guzzle = new Client([
            'base_uri' => $host,
            'timeout' => 10,
            'headers' => [
                'x-api-key' => $apiKey
            ],
            'verify' => false // remove this in production...
        ]);

        return $this;
    }

    /**
     * @param $endpoint
     * @param array $payload
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface|string
     */
    public function makeGetRequest($endpoint, $payload = [])
    {
        try {
            $response = $this->guzzle->get($endpoint, [
                'query' => $payload,
            ]);
        } catch (ClientException $e) {
            AlgoDocsErrorHandler::throw_exception($e);
            return false;
        }

        $response = (string)$response->getBody();
        if (self::isJson($response)) {
            $response = json_decode($response, true);
        }

        return $response;
    }

    /**
     * @param $endpoint
     * @param $payload
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface|string
     */
    public function makePostRequest($endpoint, $payload)
    {
        try {
            $response = $this->guzzle->post($endpoint, [
                'form_params' => $payload,
            ]);
        } catch (ClientException $e) {
            AlgoDocsErrorHandler::throw_exception($e);
            return false;
        }

        $response = (string)$response->getBody();
        if (self::isJson($response)) {
            $response = json_decode($response, true);
        }

        return $response;
    }

    /**
     * @param $endpoint
     * @param $fileContent
     * @param null $filename
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface|string
     */
    public function uploadDocument($endpoint, $fileContent, $filename)
    {
        try {
            $request = [
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => $fileContent,
                        'filename' => $filename
                    ]
                ]
            ];

            $response = $this->guzzle->request('POST', $endpoint, $request);
        } catch (ClientException $e) {
            AlgoDocsErrorHandler::throw_exception($e);
            return false;
        }

        $response = (string)$response->getBody();
        if (self::isJson($response)) {
            $response = json_decode($response, true);
        }

        return $response;
    }

    public static function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
