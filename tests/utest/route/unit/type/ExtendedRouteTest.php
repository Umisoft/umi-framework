<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\router\unit;

use umi\route\exception\InvalidArgumentException;
use umi\route\exception\RuntimeException;
use umi\route\type\ExtendedRoute;
use utest\TestCase;

/**
 * Тестирование маршрутизатора(роутера) запросов
 */
class ExtendedRouteTest extends TestCase
{
    /**
     * @var ExtendedRoute $rule
     */
    private $route;

    public function setUpFixtures()
    {
        $this->route = new ExtendedRoute([
            ExtendedRoute::OPTION_ROUTE => 'regexp/{name}/{var}',
            ExtendedRoute::OPTION_RULES => [
                'name' => '\d+',
                'var'  => '\d+'
            ]
        ]);
    }

    /**
     * Тесты ассемблирования.
     */
    public function testAssemble()
    {
        $this->assertEquals(
            'regexp/15/123',
            $this->route->assemble(
                [
                    'name' => 15,
                    'var'  => 123
                ]
            ),
            'Ожидается, что параметры будут подставлены'
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
        $this->route->assemble();
    }

    /**
     * Тесты проверки.
     */
    public function testMatch()
    {
        $this->assertEquals(12, $this->route->match('regexp/15/12'), 'Ожидается, что URL подходит');
        $this->assertEquals(
            ['name' => 15, 'var' => 12],
            $this->route->getParams(),
            'Ожидается, что параметры по умолчанию будут изменены.'
        );

        $this->assertNotEquals(13, $this->route->match('regexp/NaN/12'), 'Ожидается, что URL не подходит полностью.');
        $this->assertEquals(
            [],
            $this->route->getParams(),
            'Ожидается, что параметры по умолчанию не будут изменены'
        );
    }
}