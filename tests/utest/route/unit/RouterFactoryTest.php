<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\router\unit;

use umi\route\exception\InvalidArgumentException;
use umi\route\exception\OutOfBoundsException;
use umi\route\IRouteFactory;
use umi\route\toolbox\factory\RouteFactory;
use utest\TestCase;

/**
 * Тесты инструментов роутинга
 */
class RouterFactoryTest extends TestCase
{
    /**
     * @var IRouteFactory $routeTools
     */
    public $factory;

    public function setUpFixtures()
    {
        $this->factory = new RouteFactory();
        $this->resolveOptionalDependencies($this->factory);
    }

    /**
     * Тест получения роутера
     */
    public function testGetRouter()
    {
        $router = $this->factory->createRouter(
            [
                'sample' => [
                    'type'      => IRouteFactory::ROUTE_FIXED,
                    'route'     => '/sample/route',
                    'defaults'  => ['controller' => 'sample', 'method' => 'route'],
                    'subroutes' => [
                        'ext' => [
                            'type'     => IRouteFactory::ROUTE_FIXED,
                            'route'    => '/mode',
                            'defaults' => ['mode' => 'extended']
                        ]
                    ]
                ]
            ]
        );

        $this->assertInstanceOf('umi\route\IRouter', $router, 'Ожидается, что будет получен роутер.');
        $this->assertEquals(
            '/sample/route/mode',
            $router->assemble('sample/ext'),
            'Ожидается, что роутер настроен'
        );
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function noRouteType()
    {
        $this->factory->createRouter(
            [
                'test' => [
                    'route' => '/test'
                ]
            ]
        );
    }

    /**
     * @test
     * @expectedException OutOfBoundsException
     */
    public function wrongRouteType()
    {
        $this->factory->createRouter(
            [
                'test' => [
                    'type' => 'wrong',
                    'route' => '/test'
                ]
            ]
        );
    }
}