<?php

/**
 * A simple way to call guzzle and cache data from APIs
 *
 * @author     Leo Leoncio
 * @see        https://github.com/leowebguy
 * @copyright  Copyright (c) 2026, leowebguy
 */

namespace leowebguy\simpleguzzle\services;

use Craft;
use craft\base\Component;
use craft\helpers\Json;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class FetchService extends Component
{
    /**
     * @param array<string, mixed> $client
     * @param string $method
     * @param string $uri
     * @param array<string, mixed> $options
     * @return array<mixed>|string
     */
    public function callGuzzle(array $client, string $method, string $uri, array $options = []): array|string
    {
        try {
            $guzzleClient = Craft::createGuzzleClient($client);
            $result = $guzzleClient->requestAsync($method, $uri, $options)->wait();
        } catch (Throwable $e) {
            Craft::error($e->getMessage(), __METHOD__);
            return [
                'error' => true,
                'reason' => $e->getMessage(),
            ];
        }

        if (!$result instanceof ResponseInterface) {
            Craft::error('Invalid response received from Guzzle', __METHOD__);
            return [
                'error' => true,
                'reason' => 'Invalid response received from Guzzle',
            ];
        }

        $body = (string)$result->getBody();

        if ($result->getStatusCode() >= 200 && $result->getStatusCode() < 300 && Json::isJsonObject($body)) {
            try {
                /** @var array<mixed> */
                return Json::decode($body);
            } catch (Throwable) {
                return $body;
            }
        }

        return $body;
    }
}
