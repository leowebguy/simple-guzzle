<?php
/**
 * A simple way to call guzzle and cache data from APIs
 *
 * @author     Leo Leoncio
 * @see        https://github.com/leowebguy
 * @copyright  Copyright (c) 2024, leowebguy
 */

namespace leowebguy\simpleguzzle\services;

use Craft;
use craft\base\Component;
use craft\helpers\Json;
use GuzzleHttp\Exception\GuzzleException;

class FetchService extends Component
{
    /**
     * @param $client
     * @param $method
     * @param $uri
     * @param $options
     * @return array|string
     */
    public function callGuzzle($client, $method, $uri, $options): array|string
    {
        $client = Craft::createGuzzleClient($client);

        try {
            $result = $client->requestAsync($method, $uri, $options)->wait();
        } catch (GuzzleException $e) {
            Craft::error($e->getMessage(), __METHOD__);
            return [
                'error' => true,
                'reason' => $e->getMessage()
            ];
        }

        if ($result->getStatusCode() === 200 &&
            Json::isJsonObject($result->getBody())) {
            return Json::decode($result->getBody());
        }

        return (string)$result->getBody();
    }
}
