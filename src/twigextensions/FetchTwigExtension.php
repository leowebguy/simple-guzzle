<?php
/**
 * A simple way to call guzzle and cache data from APIs
 *
 * @author     Leo Leoncio
 * @see        https://github.com/leowebguy
 * @copyright  Copyright (c) 2023, leowebguy
 * @license    MIT
 */

namespace leowebguy\simpleguzzle\twigextensions;

use Craft;
use craft\helpers\Json;
use craft\helpers\StringHelper;
use leowebguy\simpleguzzle\Plugin;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FetchTwigExtension extends AbstractExtension
{
    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('guzzle', [$this, 'guzzle']),
        ];
    }

    /**
     * @param array $client
     * @param string $method
     * @param string $destination
     * @param array $options
     * @param int|null $duration
     * @return array|string
     */
    public function guzzle(array $client, string $method, string $destination, array $options = [], int|null $duration = 0): array|string
    {
        $cacheKey = md5(StringHelper::slugify($method . '-' . Json::encode($client) . '-' . $destination . '-' . Json::encode($options)));

        return Craft::$app->cache->getOrSet($cacheKey, function() use ($client, $method, $destination, $options) {
            return Plugin::$plugin->guzzleService->callGuzzle($client, $method, $destination, $options);
        }, $duration);
    }
}
