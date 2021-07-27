<?php

declare(strict_types=1);

namespace Workshop\Main;

use GuzzleHttp\Psr7\Request;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;

use function is_numeric;
use function json_decode;

class SomeClass
{
    private ClientInterface $httpClient;

    private LoggerInterface $logger;

    private const IP_API_URL = 'https://api.ipify.org?format=json';

    public function __construct(ClientInterface $httpClient, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->logger     = $logger;
    }

    /** @throws SomeException */
    public function getIpTestable(): string
    {
        $request = new Request('GET', self::IP_API_URL);
        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            $this->logger->error('Cannot make http call', [$e->getMessage()]);
            throw new SomeException('Cannot make HTTP call');
        }

        $responseBody = $response->getBody()->getContents();

        try {
            $decodedResponse = json_decode($responseBody, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->logger->error('Cannot decode response', ['response' => $responseBody]);
            throw new SomeException('Cannot decode API response');
        }
        return $decodedResponse['ip'];
    }

    /** @throws SomeException */
    public function getIpNotTestable(): string
    {
        $curlHandle = curl_init();

        curl_setopt($curlHandle, CURLOPT_URL, self::IP_API_URL);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curlHandle);
        if (curl_errno($curlHandle)) {
            $this->logger->error('Cannot make http call', [curl_error($curlHandle)]);
            throw new SomeException('Cannot make HTTP call');
        }
        curl_close($curlHandle);

        try {
            $decodedResponse = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->logger->error('Cannot decode response', ['response' => $response]);
            throw new SomeException('Cannot decode API response');
        }
        return $decodedResponse['ip'];
    }

    public function doSomething(mixed $param1, mixed $param2): string|int
    {
        if (is_numeric($param1) && is_numeric($param2)) {
            return (int)$param1 + (int)$param2;
        }

        return $param1 . $param2;
    }
}
