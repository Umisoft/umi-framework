<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\hmvc\func;

use umi\hmvc\component\IComponent;
use utest\hmvc\HMVCTestCase;

/**
 * Тестирование HMVC
 */
class HMVCTest extends HMVCTestCase
{
    /**
     * @var IComponent $mvc
     */
    protected $component;

    public function setUpFixtures()
    {
        $componentFactory = $this->getTestToolkit()->getService('umi\hmvc\component\IComponentFactory');

        $this->component = $componentFactory
            ->createComponent(
            require dirname(__DIR__) . '/fixture/component1/component.config.php'
        );
    }

    public function testResultCall()
    {
        $request = $this->getRequest('/result');

        $response = $this->component->execute($request);
        $this->assertEquals('route: example, sample: Hello world', $response->getContent());
        $this->assertEquals(200, $response->getCode());
    }

    public function testResponseCall()
    {
        $request = $this->getRequest('/response');

        $response = $this->component->execute($request);
        $this->assertEquals('example', $response->getContent());
        $this->assertEquals(200, $response->getCode());
    }

    public function testChildComponentCall()
    {
        $request = $this->getRequest('/component2/test');
        $response = $this->component->execute($request);
        $this->assertEquals('Hello world, UMI', $response->getContent());
        $this->assertEquals(200, $response->getCode());
    }

    /**
     * @test
     * @expectedException \umi\hmvc\exception\http\HttpNotFound
     * @expectedExceptionCode 404
     */
    public function wrongRouteCall()
    {
        $request = $this->getRequest('/wrong/route');
        $this->component->execute($request);
    }
}