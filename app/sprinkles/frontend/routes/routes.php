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

$app->group('/account', function () {
    $this->get('/captcha', 'UserFrosting\Sprinkle\Frontend\Controller\AccountController:imageCaptcha');

    $this->get('/check-username', 'UserFrosting\Sprinkle\Frontend\Controller\AccountController:checkUsername');

    $this->get('/forgot-password', 'UserFrosting\Sprinkle\Frontend\Controller\AccountController:pageForgotPassword')
        ->setName('forgot-password');

    $this->get('/logout', 'UserFrosting\Sprinkle\Frontend\Controller\AccountController:logout')
        ->add('authGuard');

    $this->get('/resend-verification', 'UserFrosting\Sprinkle\Frontend\Controller\AccountController:pageResendVerification');

    $this->get('/set-password/confirm', 'UserFrosting\Sprinkle\Frontend\Controller\AccountController:pageResetPassword');

    $this->get('/set-password/deny', 'UserFrosting\Sprinkle\Frontend\Controller\AccountController:denyResetPassword');

    $this->get('/register', 'UserFrosting\Sprinkle\Frontend\Controller\AccountController:pageRegister')
        ->add('checkEnvironment')
        ->setName('register');

    $this->get('/settings', 'UserFrosting\Sprinkle\Frontend\Controller\AccountController:pageSettings')
        ->add('authGuard');

    $this->get('/sign-in', 'UserFrosting\Sprinkle\Frontend\Controller\AccountController:pageSignIn')
        ->add('checkEnvironment')
        ->setName('login');

    $this->get('/suggest-username', 'UserFrosting\Sprinkle\Frontend\Controller\AccountController:suggestUsername');

    $this->get('/verify', 'UserFrosting\Sprinkle\Frontend\Controller\AccountController:verify');

    $this->post('/forgot-password', 'UserFrosting\Sprinkle\Frontend\Controller\AccountController:forgotPassword');

    $this->post('/login', 'UserFrosting\Sprinkle\Frontend\Controller\AccountController:login');

    $this->post('/register', 'UserFrosting\Sprinkle\Frontend\Controller\AccountController:register');

    $this->post('/resend-verification', 'UserFrosting\Sprinkle\Frontend\Controller\AccountController:resendVerification');

    $this->post('/set-password', 'UserFrosting\Sprinkle\Frontend\Controller\AccountController:setPassword');

    $this->post('/settings', 'UserFrosting\Sprinkle\Frontend\Controller\AccountController:settings')
        ->add('authGuard')
        ->setName('settings');

    $this->post('/settings/profile', 'UserFrosting\Sprinkle\Frontend\Controller\AccountController:profile')
        ->add('authGuard');
})->add(new NoCache());

$app->get('/modals/account/tos', 'UserFrosting\Sprinkle\Frontend\Controller\AccountController:getModalAccountTos');
