<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\router\unit;

use umi\route\exception\InvalidArgumentException;
use umi\route\exception\OutOfBoundsException;
use umi\route\toolbox\factory\RouteFactory;
use umi\route\type\factory\IRouteFactory;
use umi\route\type\FixedRoute;
use utest\TestCase;

/**
 * Тесты фабрики роутов.
 */
class RouteFactoryTest extends TestCase
{
    /**
     * @var IRouteFactory $factory
     */
    protected $factory;

    public function setUpFixtures()
    {
        $this->factory = new RouteFactory();
        $this->resolveOptionalDependencies($this->factory);
    }

    /**
     * Тесты создания роута.
     */
    public function testCreateRoutes()
    {
        /**
         * @var FixedRoute $route
         */
        $route = current(
            $this->factory->createRoutes(
                [
                    ['type' => IRouteFactory::ROUTE_FIXED, 'route' => 'fixed/route']
                ]
            )
        );

        $this->assertInstanceOf('umi\route\type\IRoute', $route, 'Ожидается объект IRoute');
        $this->assertEquals('fixed/route', $route->route, 'Ожидается, что опции будут установлены.');
    }

    /**
     * Тесты получения роутов на основе конфигурации.
     */
    public function testGetRoutes()
    {
        $routes = $this->factory->createRoutes(
            [
                'routeName' => [
                    'type'  => IRouteFactory::ROUTE_FIXED,
                    'route' => 'fixed/route'
                ]
            ]
        );

        $this->assertArrayHasKey(
            'routeName',
            $routes,
            'Ожидается, что роутер будет список роутеров будет именован.'
        );
        /**
         * @var FixedRoute $route
         */
        $route = $routes['routeName'];

        $this->assertInstanceOf('umi\route\type\IRoute', $route, 'Ожидается объект IRoute');
        $this->assertEquals('fixed/route', $route->route, 'Ожидается, что опции будут установлены.');
    }

    /**
     * @test исключения, при не заданом типе роута
     * @expectedException InvalidArgumentException
     */
    public function missedRouteType()
    {
        $this->factory->createRoutes(
            [
                ['route' => 'fixed/route']
            ]
        );
    }

    /**
     * @test исключения, при неверном типе роута
     * @expectedException OutOfBoundsException
     */
    public function wrongRouteType()
    {
        $this->factory->createRoutes(
            [
                ['type' => 'wrong', 'route' => 'fixed/route']
            ]
        );
    }
}