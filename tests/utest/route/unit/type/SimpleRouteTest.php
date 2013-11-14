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
use umi\route\exception\RuntimeException;
use umi\route\type\SimpleRoute;
use utest\route\RouteTestCase;

/**
 * Тестирование маршрутизатора(роутера) запросов
 */
class SimpleRouteTest extends RouteTestCase
{
    /**
     * @var SimpleRoute $rule
     */
    private $route;

    public function setUpFixtures()
    {
        $this->route = new SimpleRoute([
            SimpleRoute::OPTION_ROUTE => 'regexp/{name:integer}',
            SimpleRoute::OPTION_DEFAULTS => ['name' => 0]
        ]);
    }

    /**
     * Тесты ассемблирования.
     */
    public function testAssemble()
    {
        $this->assertEquals(
            'regexp',
            $this->route->assemble(),
            'Ожидается, что будут подставлены параметры по умолчанию'
        );
        $this->assertEquals(
            'regexp/15',
            $this->route->assemble(['name' => 15]),
            'Ожидается, что параметры будут подставлены'
        );
    }

    public function testOptionalParams()
    {
        $this->assertEquals(6, $this->route->match('regexp'));
        $this->assertEquals(['name' => 0], $this->route->getParams());

        $route = new SimpleRoute([
            SimpleRoute::OPTION_ROUTE => '/{lang:string}/{param:string}',
            SimpleRoute::OPTION_DEFAULTS => ['lang' => 'en']
        ]);

        $this->assertEquals(6, $route->match('/param'), 'Ожидается, что роут подойдет.');
        $this->assertEquals(
            ['lang' => 'en', 'param' => 'param'],
            $route->getParams(),
            'Ожидается, что параметры по умолчанию будут подставлены.'
        );

        $this->assertEquals(9, $route->match('/ru/param'), 'Ожидается, что роут подойдет.');
        $this->assertEquals(
            ['lang' => 'ru', 'param' => 'param'],
            $route->getParams(),
            'Ожидается, что параметр по умолчанию будет заменен.'
        );

        $this->assertFalse($route->match('//param'), 'Ожидается, что роут не подойдет.');
    }

    public function testParamsWithoutType()
    {
        $route = new SimpleRoute([
            SimpleRoute::OPTION_ROUTE => '/{lang}',
        ]);

        $this->assertEquals(
            '/123',
            $route->assemble(['lang' => '123']),
            'Ожидается, что параметр будет установлен при ассемблировании.'
        );
        $this->assertEquals(
            5,
            $route->match('/name'),
            'Ожидается, что роут подойдет.'
        );

        $this->assertEquals(
            ['lang' => 'name'],
            $route->getParams(),
            'Ожидается, что параметры будут установлены.'
        );
    }

    /**
     * @test исключения, при неверном типе параметра.
     * @expectedException InvalidArgumentException
     */
    public function assemblingWithWrongParam()
    {
        $this->route->assemble(['name' => 'myName']);
    }

    /**
     * @test исключения, при отсутвии обязательного параметра.
     * @expectedException RuntimeException
     */
    public function assemblingWithoutParam()
    {
        $this->route = new SimpleRoute([
            SimpleRoute::OPTION_ROUTE => 'regexp/{name:integer}'
        ]);

        $this->route->assemble();
    }

    /**
     * @test исключения, при неверном типе параметра.
     * @expectedException OutOfBoundsException
     */
    public function wrongRoutePartType()
    {
        $this->route = new SimpleRoute([
            SimpleRoute::OPTION_ROUTE => 'regexp/{name:int}'
        ]);
        $this->route->assemble(['name' => 12]);
    }

    /**
     * Тесты проверки.
     */
    public function testMatch()
    {
        $this->assertEquals(9, $this->route->match('regexp/15'), 'Ожидается, что URL подходит');
        $this->assertEquals(
            ['name' => 15],
            $this->route->getParams(),
            'Ожидается, что параметры по умолчанию будут изменены.'
        );

        $this->assertNotEquals(10, $this->route->match('regexp/NaN'), 'Ожидается, что URL не подходит полностью.');
        $this->assertEquals(
            ['name' => 0],
            $this->route->getParams(),
            'Ожидается, что параметры по умолчанию не будут изменены'
        );
    }
}