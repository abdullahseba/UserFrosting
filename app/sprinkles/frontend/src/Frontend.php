<?php

/*
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/UserFrosting
 * @copyright Copyright (c) 2019 Alexander Weissman
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Sprinkle\Frontend;

use RocketTheme\Toolbox\Event\Event;
use UserFrosting\Sprinkle\Frontend\Csrf\SlimCsrfProvider;
use UserFrosting\System\Sprinkle\Sprinkle;
use UserFrosting\Sprinkle\Frontend\I18n\LocaleServicesProvider;
use UserFrosting\Sprinkle\Frontend\I18n\TranslatorServicesProvider;

/**
 * Bootstrapper class for the core sprinkle.
 *
 * @author Alex Weissman (https://alexanderweissman.com)
 */
class Frontend extends Sprinkle
{

    /**
     * @var string[] List of services provider to register
     */
    protected $servicesproviders = [
        LocaleServicesProvider::class,
        TranslatorServicesProvider::class,
    ];
    /**
     * Defines which events in the UF lifecycle our Sprinkle should hook into.
     */
    public static function getSubscribedEvents()
    {
        return [
            'onAddGlobalMiddleware'       => ['onAddGlobalMiddleware', 0],
        ];
    }

    /**
     * Add CSRF middleware.
     *
     * @param Event $event
     */
    public function onAddGlobalMiddleware(Event $event)
    {
        // Don't register CSRF if CLI
        if (!$this->ci->cli) {
            SlimCsrfProvider::registerMiddleware($event->getApp(), $this->ci->request, $this->ci->csrf);
        }
    }
}
