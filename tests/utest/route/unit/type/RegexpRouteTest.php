<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\router\unit;

use umi\route\type\RegexpRoute;
use utest\TestCase;

/**
 * Тестирование правила роутинга, на основе регулярных выражений.
 */
class RegexpRouteTest extends TestCase
{
    /**
     * @var RegexpRoute $rule
     */
    private $route;

    public function setUpFixtures()
    {
        $this->route = new RegexpRoute();
        $this->route->route = 'regexp/(?P<name>\d+)';
        $this->route->defaults = ['name' => 0];
    }

    /**
     * Тесты ассемблирования.
     */
    public function testAssemble()
    {
        $this->assertEquals(
            'regexp/0',
            $this->route->assemble(),
            'Ожидается, что будут подставлены параметры по умолчанию'
        );
        $this->assertEquals(
            'regexp/15',
            $this->route->assemble(['name' => 15]),
            'Ожидается, что параметры будут подставлены'
        );
    }

    /**
     * @test исключения, при отсутвии обязательного аргумента.
     * @expectedException \umi\route\exception\RuntimeException
     */
    public function assemblingWithoutParam()
    {
        $this->route->defaults = [];
        $this->route->assemble();
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
            'Ожидается, что параметры по умолчанию будут изменены'
        );

        $this->assertFalse($this->route->match('regexp/NaN'), 'Ожидается, что URL не подходит');
        $this->assertEquals(
            $this->route->defaults,
            $this->route->getParams(),
            'Ожидается, что параметры по умолчанию не будут изменены'
        );
    }
}