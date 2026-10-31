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
use GuzzleHttp\Exception\GuzzleException;

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
        $guzzleClient = Craft::createGuzzleClient($client);

        try {
            $result = $guzzleClient->requestAsync($method, $uri, $options)->wait();
        } catch (GuzzleException $e) {
            Craft::error($e->getMessage(), __METHOD__);
            return [
                'error' => true,
                'reason' => $e->getMessage(),
            ];
        }

        $body = (string)$result->getBody();

        if ($result->getStatusCode() >= 200 && $result->getStatusCode() < 300 && Json::isJsonObject($body)) {
            /** @var array<mixed> */
            return Json::decode($body);
        }

        return $body;
    }
}
