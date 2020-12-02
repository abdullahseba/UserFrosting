<?php

/*
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/UserFrosting
 * @copyright Copyright (c) 2019 Alexander Weissman
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Sprinkle\Frontend\ServicesProvider;


use Psr\Container\ContainerInterface;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Twig\Extension\DebugExtension;
use UserFrosting\Assets\AssetBundles\GulpBundleAssetsCompiledBundles as CompiledAssetBundles;
use UserFrosting\Assets\AssetLoader;
use UserFrosting\Assets\Assets;
use UserFrosting\Sprinkle\Frontend\Alert\CacheAlertStream;
use UserFrosting\Sprinkle\Frontend\Alert\SessionAlertStream;
use UserFrosting\Sprinkle\Frontend\Csrf\SlimCsrfProvider;
use UserFrosting\Sprinkle\Frontend\Error\Handler\NotFoundExceptionHandler;
use UserFrosting\Sprinkle\Frontend\Twig\AccountExtension;

use UserFrosting\Sprinkle\Core\Router;
use UserFrosting\Sprinkle\Frontend\Twig\CoreExtension;
use UserFrosting\Sprinkle\Core\Util\RawAssetBundles;
use UserFrosting\Support\Exception\NotFoundException;


/**
 * UserFrosting core services provider.
 *
 * Registers core services for UserFrosting, such as config, database, asset manager, translator, etc.
 *
 * @author Alex Weissman (https://alexanderweissman.com)
 */
class ServicesProvider
{
    /**
     * Register UserFrosting's core services.
     *
     * @param ContainerInterface $container A DI container implementing ArrayAccess and psr-container.
     */
    public function register(ContainerInterface $container)
    {
        /*
         * Flash messaging service.
         *
         * Persists error/success messages between requests in the session.
         *
         * @throws \Exception                                    If alert storage handler is not supported
         * @return \UserFrosting\Sprinkle\Frontend\Alert\AlertStream
         */
        $container['alerts'] = function ($c) {
            $config = $c->config;

            if ($config['alert.storage'] == 'cache') {
                return new CacheAlertStream($config['alert.key'], $c->translator, $c->cache, $c->session->getId());
            } elseif ($config['alert.storage'] == 'session') {
                return new SessionAlertStream($config['alert.key'], $c->translator, $c->session);
            } else {
                throw new \Exception("Bad alert storage handler type '{$config['alert.storage']}' specified in configuration file.");
            }
        };

        /*
         * Asset loader service
         *
         * Loads assets from a specified relative location.
         * Assets are Javascript, CSS, image, and other files used by your site.
         * This implementation is a temporary hack until Assets can be refactored.
         *
         * @return \UserFrosting\Assets\AssetLoader
         */
        $container['assetLoader'] = function ($c) {
            return new AssetLoader($c->assets);
        };

        /*
         * Asset manager service.
         *
         * Loads raw or compiled asset information from your bundle.config.json schema file.
         * Assets are Javascript, CSS, image, and other files used by your site.
         *
         * @return \UserFrosting\Assets\Assets
         */
        $container['assets'] = function ($c) {
            $config = $c->config;
            $locator = $c->locator;

            // Load asset schema
            if ($config['assets.use_raw']) {

                // Register sprinkle assets stream, plus vendor assets in shared streams
                $locator->registerStream('assets', 'vendor', \UserFrosting\NPM_ASSET_DIR, true);
                $locator->registerStream('assets', 'vendor', \UserFrosting\BROWSERIFIED_ASSET_DIR, true);
                $locator->registerStream('assets', 'vendor', \UserFrosting\BOWER_ASSET_DIR, true);
                $locator->registerStream('assets', '', \UserFrosting\ASSET_DIR_NAME);

                $baseUrl = $config['site.uri.public'] . '/' . $config['assets.raw.path'];

                $assets = new Assets($locator, 'assets', $baseUrl);

                // Load raw asset bundles for each Sprinkle.

                // Retrieve locations of raw asset bundle schemas that exist.
                $bundleSchemas = array_reverse($locator->findResources('sprinkles://' . $config['assets.raw.schema']));

                // Load asset bundle schemas that exist.
                if (array_key_exists(0, $bundleSchemas)) {
                    $bundles = new RawAssetBundles(array_shift($bundleSchemas));

                    foreach ($bundleSchemas as $bundleSchema) {
                        $bundles->extend($bundleSchema);
                    }

                    // Add bundles to asset manager.
                    $assets->addAssetBundles($bundles);
                }
            } else {

                // Register compiled assets stream in public folder + alias for vendor ones + build stream for CompiledAssetBundles
                $locator->registerStream('assets', '', \UserFrosting\PUBLIC_DIR_NAME . '/' . \UserFrosting\ASSET_DIR_NAME, true);
                $locator->registerStream('assets', 'vendor', \UserFrosting\PUBLIC_DIR_NAME . '/' . \UserFrosting\ASSET_DIR_NAME, true);
                $locator->registerStream('build', '', \UserFrosting\BUILD_DIR_NAME, true);

                $baseUrl = $config['site.uri.public'] . '/' . $config['assets.compiled.path'];
                $assets = new Assets($locator, 'assets', $baseUrl);

                // Load compiled asset bundle.
                $path = $locator->findResource('build://' . $config['assets.compiled.schema'], true, true);
                $bundles = new CompiledAssetBundles($path);
                $assets->addAssetBundles($bundles);
            }

            // Force load the current user to add it's theme assets ressources
            $currentUser = $c->currentUser;


            return $assets;
        };

        /*
         * Initialize CSRF guard middleware.
         *
         * @see https://github.com/slimphp/Slim-Csrf
         * @throws \UserFrosting\Support\Exception\BadRequestException
         * @return \Slim\Csrf\Guard
         */
        $container['csrf'] = function ($c) {
            return SlimCsrfProvider::setupService($c);
        };

        /*
         * Extends the 'errorHandler' service with custom exception handlers.
         *
         * Custom handlers added: ForbiddenExceptionHandler
         *
         * @return \UserFrosting\Sprinkle\Frontend\Error\ExceptionHandlerManager
         */
        $container->extend('errorHandler', function ($handler, $c) {
            // Register the ForbiddenExceptionHandler.
            $handler->registerHandler('\UserFrosting\Support\Exception\ForbiddenException', '\UserFrosting\Sprinkle\Frontend\Error\Handler\ForbiddenExceptionHandler');
            // Register the AuthExpiredExceptionHandler
            $handler->registerHandler('\UserFrosting\Sprinkle\Account\Authenticate\Exception\AuthExpiredException', '\UserFrosting\Sprinkle\Frontend\Error\Handler\AuthExpiredExceptionHandler');
            // Register the AuthCompromisedExceptionHandler.
            $handler->registerHandler('\UserFrosting\Sprinkle\Account\Authenticate\Exception\AuthCompromisedException', '\UserFrosting\Sprinkle\Frontend\Error\Handler\AuthCompromisedExceptionHandler');

            return $handler;
        });

        /*
         * Error-handler for 404 errors.  Notice that we manually create a UserFrosting NotFoundException,
         * and a NotFoundExceptionHandler.  This lets us pass through to the UF error handling system.
         *
         * @return callable
         */
        $container['notFoundHandler'] = function ($c) {
            return function ($request, $response) use ($c) {
                $exception = new NotFoundException();
                $handler = new NotFoundExceptionHandler($c, $request, $response, $exception, $c->settings['displayErrorDetails']);

                return $handler->handle();
            };
        };

        /*
         * Override Slim's default router with the UF router.
         *
         * @return \UserFrosting\Sprinkle\Core\Router
         */
        $container['router'] = function ($c) {
            $routerCacheFile = false;
            if (isset($c->config['settings.routerCacheFile'])) {
                $filename = $c->config['settings.routerCacheFile'];
                $routerCacheFile = $c->locator->findResource("cache://$filename", true, true);
            }

            return (new Router())->setCacheFile($routerCacheFile);
        };

        /*
         * Set up Twig as the view, adding template paths for all sprinkles and the Slim Twig extension.
         *
         * Also adds the UserFrosting core Twig extension, which provides additional functions, filters, global variables, etc.
         *
         * @return \Slim\Views\Twig
         */
        $container['view'] = function ($c) {

            /** @var \UserFrosting\UniformResourceLocator\ResourceLocator $locator */
            $locator = $c->locator;

            $templatePaths = $locator->getResources('templates://');
            $view = new Twig(array_map('strval', $templatePaths));
            $loader = $view->getLoader();

            // Add Sprinkles' templates namespaces
            foreach (array_reverse($templatePaths) as $templateResource) {
                $loader->addPath($templateResource->getAbsolutePath(), $templateResource->getLocation()->getName());
            }

            $twig = $view->getEnvironment();

            if ($c->config['cache.twig']) {
                $twig->setCache($c->locator->findResource('cache://twig', true, true));
            }

            if ($c->config['debug.twig']) {
                $twig->enableDebug();
                $view->addExtension(new DebugExtension());
            }

            // Register the Slim extension with Twig
            $slimExtension = new TwigExtension(
                $c->router,
                $c->request->getUri()
            );
            $view->addExtension($slimExtension);

            // Register the core UF extension with Twig
            $coreExtension = new CoreExtension($c);
            $view->addExtension($coreExtension);

            $accountExtension = new AccountExtension($c);
            $twig->addExtension($accountExtension);

            // Add paths for user theme, if a user is logged in
            // We catch any authorization-related exceptions, so that error pages can be rendered.
            try {
                /** @var \UserFrosting\Sprinkle\Account\Authenticate\Authenticator $authenticator */
                $authenticator = $c->authenticator;
                $currentUser = $c->currentUser;
            } catch (\Exception $e) {
                return $view;
            }

            // Register user theme template with Twig Loader
            if ($authenticator->check()) {
                $themePath = $c->locator->findResource('templates://', true, false);
                if ($themePath) {
                    $loader = $twig->getLoader();
                    $loader->prependPath($themePath);
                    // Add namespaced path as well
                    $loader->addPath($themePath, $currentUser->theme);
                }
            }

            return $view;
        };
    }
}
