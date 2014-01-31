<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace utest\hmvc\unit\controller;

use umi\hmvc\dispatcher\http\IHTTPComponentResponse;
use utest\hmvc\HMVCTestCase;
use utest\hmvc\mock\controller\MockRedirectController;

/**
 * Тесты контроллера.
 */
class ControllerTest extends HMVCTestCase
{
    /**
     * @test
     * @expectedException \umi\hmvc\exception\RequiredDependencyException
     */
    public function notInjectedFactory()
    {
        $controller = new MockRedirectController();
        $controller($this->getRequest('/'));
    }

    public function testRedirectResponse()
    {
        $controller = new MockRedirectController();
        $this->resolveOptionalDependencies($controller);

        /**
         * @var IHTTPComponentResponse $response
         */
        $response = $controller($this->getRequest('/'));

        $this->assertEquals(303, $response->getCode());
        $this->assertEmpty($response->getContent());
        $this->assertEquals(['Location' => '/mock_url'], $response->getHeaders()->getHeaders());
    }
}