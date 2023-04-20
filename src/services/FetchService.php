<?php
/**
 * A simple way to call guzzle and cache data from APIs
 *
 * @author     Leo Leoncio
 * @see        https://github.com/leowebguy
 * @copyright  Copyright (c) 2023, leowebguy
 * @license    MIT
 */

namespace leowebguy\simpleguzzle\services;

use Craft;
use GuzzleHttp\Exception\GuzzleException;
use craft\base\Component;
use craft\helpers\Json;

class FetchService extends Component
{
    // Public Methods
    // =========================================================================

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
