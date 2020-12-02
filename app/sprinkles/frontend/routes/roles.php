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
 * Routes for administrative role management.
 */

$app->group('/roles', function () {
    $this->get('', 'UserFrosting\Sprinkle\Frontend\Controller\RoleController:pageList')
        ->setName('uri_roles');

    $this->get('/r/{slug}', 'UserFrosting\Sprinkle\Frontend\Controller\RoleController:pageInfo');
})->add('authGuard')->add(new NoCache());

$app->group('/api/roles', function () {
    $this->delete('/r/{slug}', 'UserFrosting\Sprinkle\Frontend\Controller\RoleController:delete');

    $this->get('', 'UserFrosting\Sprinkle\Frontend\Controller\RoleController:getList');

    $this->get('/r/{slug}', 'UserFrosting\Sprinkle\Frontend\Controller\RoleController:getInfo');

    $this->get('/r/{slug}/permissions', 'UserFrosting\Sprinkle\Frontend\Controller\RoleController:getPermissions');

    $this->get('/r/{slug}/users', 'UserFrosting\Sprinkle\Frontend\Controller\RoleController:getUsers');

    $this->post('', 'UserFrosting\Sprinkle\Frontend\Controller\RoleController:create');

    $this->put('/r/{slug}', 'UserFrosting\Sprinkle\Frontend\Controller\RoleController:updateInfo');

    $this->put('/r/{slug}/{field}', 'UserFrosting\Sprinkle\Frontend\Controller\RoleController:updateField');
})->add('authGuard')->add(new NoCache());

$app->group('/modals/roles', function () {
    $this->get('/confirm-delete', 'UserFrosting\Sprinkle\Frontend\Controller\RoleController:getModalConfirmDelete');

    $this->get('/create', 'UserFrosting\Sprinkle\Frontend\Controller\RoleController:getModalCreate');

    $this->get('/edit', 'UserFrosting\Sprinkle\Frontend\Controller\RoleController:getModalEdit');

    $this->get('/permissions', 'UserFrosting\Sprinkle\Frontend\Controller\RoleController:getModalEditPermissions');
})->add('authGuard')->add(new NoCache());
