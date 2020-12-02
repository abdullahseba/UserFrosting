<?php

/*
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/UserFrosting
 * @copyright Copyright (c) 2019 Alexander Weissman
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Sprinkle\Core\Tests;

use UserFrosting\Sprinkle\Frontend\Controller\FrontendController;
use UserFrosting\Support\Exception\NotFoundException;

/**
 * Tests FrontendController
 */
class FrontendControllerTest extends ControllerTestCase
{
    /**
     * @return FrontendController
     */
    public function testControllerConstructor()
    {
        $controller = new FrontendController($this->ci);
        $this->assertInstanceOf(FrontendController::class, $controller);

        return $controller;
    }

    /**
     * @depends testControllerConstructor
     * @param FrontendController $controller
     */
    public function testPageIndex(FrontendController $controller)
    {
        $result = $controller->pageIndex($this->getRequest(), $this->getResponse(), []);
        $this->assertSame($result->getStatusCode(), 200);
        $this->assertTrue((bool) preg_match('/<\/html>/', (string) $result->getBody()));
    }

    /**
     * @depends testControllerConstructor
     * @param FrontendController $controller
     */
    public function testJsonAlerts(FrontendController $controller)
    {
        $result = $controller->jsonAlerts($this->getRequest(), $this->getResponse(), []);
        $this->assertSame($result->getStatusCode(), 200);
        $this->assertJson((string) $result->getBody());
    }

    /**
     * @depends testControllerConstructor
     * @param FrontendController $controller
     */
    public function testGetAsset_ExceptionNoUrl(FrontendController $controller)
    {
        $this->expectException(NotFoundException::class);
        $controller->getAsset($this->getRequest(), $this->getResponse(), []);
    }

    /**
     * @depends testControllerConstructor
     * @param FrontendController $controller
     */
    public function testGetAsset_ExceptionBadUrl(FrontendController $controller)
    {
        $this->expectException(NotFoundException::class);
        $url = '/' . rand(0, 99999);
        $controller->getAsset($this->getRequest(), $this->getResponse(), ['url' => $url]);
    }

    /**
     * @depends testControllerConstructor
     * @param FrontendController $controller
     */
    public function testGetAsset_ExceptionEmptyUrl(FrontendController $controller)
    {
        $this->expectException(NotFoundException::class);
        $controller->getAsset($this->getRequest(), $this->getResponse(), ['url' => '']);
    }

    /**
     * @depends testControllerConstructor
     * @depends testGetAsset_ExceptionNoUrl
     * @depends testGetAsset_ExceptionBadUrl
     * @param FrontendController $controller
     */
    public function testGetAsset(FrontendController $controller)
    {
        $result = $controller->getAsset($this->getRequest(), $this->getResponse(), ['url' => 'userfrosting/js/uf-alerts.js']);
        $this->assertSame($result->getStatusCode(), 200);
        $this->assertNotEmpty((string) $result->getBody());
    }
}
