<?php

/**
 * A simple way to call guzzle and cache data from APIs
 *
 * @author     Leo Leoncio
 * @see        https://github.com/leowebguy
 * @copyright  Copyright (c) 2026, leowebguy
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
     * @param array<string, mixed> $client
     * @param string $method
     * @param string $destination
     * @param array<string, mixed> $options
     * @param int|null $duration
     * @return array<mixed>|string
     */
    public function guzzle(array $client, string $method, string $destination, array $options = [], ?int $duration = 0): array|string
    {
        $plugin = Plugin::getInstance();
        if ($plugin === null) {
            return [
                'error' => true,
                'reason' => 'Plugin instance not found',
            ];
        }

        $cache = Craft::$app->getCache();
        if ($duration !== null && $duration > 0 && $cache !== null) {
            $cacheKey = md5(StringHelper::slugify($method . '-' . Json::encode($client) . '-' . $destination . '-' . Json::encode($options)));

            /** @var array<mixed>|string */
            return $cache->getOrSet($cacheKey, function() use ($plugin, $client, $method, $destination, $options): array|string {
                return $plugin->guzzleService->callGuzzle($client, $method, $destination, $options);
            }, $duration);
        }

        return $plugin->guzzleService->callGuzzle($client, $method, $destination, $options);
    }
}
