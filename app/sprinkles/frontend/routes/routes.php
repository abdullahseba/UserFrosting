<?php

/*
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/UserFrosting
 * @copyright Copyright (c) 2019 Alexander Weissman
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/LICENSE.md (MIT License)
 */

use UserFrosting\Sprinkle\Core\Util\NoCache;

global $app;
$config = $app->getContainer()->get('config');

$app->get('/', 'UserFrosting\Sprinkle\Frontend\Controller\FrontendController:pageIndex')
    ->add('checkEnvironment')
    ->setName('index');


$app->get('/alerts', 'UserFrosting\Sprinkle\Frontend\Controller\FrontendController:jsonAlerts')
    ->add(new NoCache());


$app->get('/' . $config['assets.raw.path'] . '/{url:.+}', 'UserFrosting\Sprinkle\Frontend\Controller\FrontendController:getAsset');
