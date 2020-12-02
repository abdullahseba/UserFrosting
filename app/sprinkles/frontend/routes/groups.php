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
 * Routes for administrative group management.
 */

$app->group('/groups', function () {
    $this->get('', 'UserFrosting\Sprinkle\Frontend\Controller\GroupController:pageList')
        ->setName('uri_groups');

    $this->get('/g/{slug}', 'UserFrosting\Sprinkle\Frontend\Controller\GroupController:pageInfo');
})->add('authGuard')->add(new NoCache());

$app->group('/api/groups', function () {
    $this->delete('/g/{slug}', 'UserFrosting\Sprinkle\Frontend\Controller\GroupController:delete');

    $this->get('', 'UserFrosting\Sprinkle\Frontend\Controller\GroupController:getList');

    $this->get('/g/{slug}', 'UserFrosting\Sprinkle\Frontend\Controller\GroupController:getInfo');

    $this->get('/g/{slug}/users', 'UserFrosting\Sprinkle\Frontend\Controller\GroupController:getUsers');

    $this->post('', 'UserFrosting\Sprinkle\Frontend\Controller\GroupController:create');

    $this->put('/g/{slug}', 'UserFrosting\Sprinkle\Frontend\Controller\GroupController:updateInfo');
})->add('authGuard')->add(new NoCache());

$app->group('/modals/groups', function () {
    $this->get('/confirm-delete', 'UserFrosting\Sprinkle\Frontend\Controller\GroupController:getModalConfirmDelete');

    $this->get('/create', 'UserFrosting\Sprinkle\Frontend\Controller\GroupController:getModalCreate');

    $this->get('/edit', 'UserFrosting\Sprinkle\Frontend\Controller\GroupController:getModalEdit');
})->add('authGuard')->add(new NoCache());
