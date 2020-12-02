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
 * Routes for administrative user management.
 */

$app->group('/users', function () {
    $this->get('', 'UserFrosting\Sprinkle\Frontend\Controller\UserController:pageList')
        ->setName('uri_users');

    $this->get('/u/{user_name}', 'UserFrosting\Sprinkle\Frontend\Controller\UserController:pageInfo');
})->add('authGuard')->add(new NoCache());

$app->group('/api/users', function () {
    $this->delete('/u/{user_name}', 'UserFrosting\Sprinkle\Frontend\Controller\UserController:delete');

    $this->get('', 'UserFrosting\Sprinkle\Frontend\Controller\UserController:getList');

    $this->get('/u/{user_name}', 'UserFrosting\Sprinkle\Frontend\Controller\UserController:getInfo');

    $this->get('/u/{user_name}/activities', 'UserFrosting\Sprinkle\Frontend\Controller\UserController:getActivities');

    $this->get('/u/{user_name}/roles', 'UserFrosting\Sprinkle\Frontend\Controller\UserController:getRoles');

    $this->get('/u/{user_name}/permissions', 'UserFrosting\Sprinkle\Frontend\Controller\UserController:getPermissions');

    $this->post('', 'UserFrosting\Sprinkle\Frontend\Controller\UserController:create');

    $this->post('/u/{user_name}/password-reset', 'UserFrosting\Sprinkle\Frontend\Controller\UserController:createPasswordReset');

    $this->put('/u/{user_name}', 'UserFrosting\Sprinkle\Frontend\Controller\UserController:updateInfo');

    $this->put('/u/{user_name}/{field}', 'UserFrosting\Sprinkle\Frontend\Controller\UserController:updateField');
})->add('authGuard')->add(new NoCache());

$app->group('/modals/users', function () {
    $this->get('/confirm-delete', 'UserFrosting\Sprinkle\Frontend\Controller\UserController:getModalConfirmDelete');

    $this->get('/create', 'UserFrosting\Sprinkle\Frontend\Controller\UserController:getModalCreate');

    $this->get('/edit', 'UserFrosting\Sprinkle\Frontend\Controller\UserController:getModalEdit');

    $this->get('/password', 'UserFrosting\Sprinkle\Frontend\Controller\UserController:getModalEditPassword');

    $this->get('/roles', 'UserFrosting\Sprinkle\Frontend\Controller\UserController:getModalEditRoles');
})->add('authGuard')->add(new NoCache());
