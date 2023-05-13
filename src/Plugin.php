<?php
/**
 * A simple way to call guzzle and cache data from APIs
 *
 * @author     Leo Leoncio
 * @see        https://github.com/leowebguy
 * @copyright  Copyright (c) 2023, leowebguy
 * @license    MIT
 */

namespace leowebguy\simpleguzzle;

use Craft;
use craft\base\Plugin as BasePlugin;
use leowebguy\simpleguzzle\services\FetchService;
use leowebguy\simpleguzzle\twigextensions\FetchTwigExtension;

class Plugin extends BasePlugin
{
    // Properties
    // =========================================================================

    public static mixed $plugin;

    public bool $hasCpSection = false;

    public bool $hasCpSettings = false;

    // Public Methods
    // =========================================================================

    /**
     * @return void
     */
    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        if (!$this->isInstalled) {
            return;
        }

        $this->setComponents([
            'guzzleService' => FetchService::class
        ]);

        Craft::$app->view->registerTwigExtension(new FetchTwigExtension());

        Craft::info(
            'Simple Guzzle plugin loaded',
            __METHOD__
        );
    }
}
