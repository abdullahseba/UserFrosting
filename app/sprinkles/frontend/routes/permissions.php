<?php

/*
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/UserFrosting
 * @copyright Copyright (c) 2019 Alexander Weissman
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/LICENSE.md (MIT License)
 */

use UserFrosting\Sprinkle\Core\Util\NoCache;

/*
 * Routes for administrative permission management.
 */

$app->group('/permissions', function () {
    $this->get('', 'UserFrosting\Sprinkle\Frontend\Controller\PermissionController:pageList')
        ->setName('uri_permissions');

    $this->get('/p/{id}', 'UserFrosting\Sprinkle\Frontend\Controller\PermissionController:pageInfo');
})->add('authGuard')->add(new NoCache());

$app->group('/api/permissions', function () {
    $this->get('', 'UserFrosting\Sprinkle\Frontend\Controller\PermissionController:getList');

    $this->get('/p/{id}', 'UserFrosting\Sprinkle\Frontend\Controller\PermissionController:getInfo');

    $this->get('/p/{id}/users', 'UserFrosting\Sprinkle\Frontend\Controller\PermissionController:getUsers');
})->add('authGuard')->add(new NoCache());
