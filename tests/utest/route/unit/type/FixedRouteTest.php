<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\router\unit;

use umi\route\type\FixedRoute;
use utest\TestCase;

/**
 * Тестирование статического правила роутинга.
 */
class FixedRouteTest extends TestCase
{
    /**
     * @var FixedRoute $rule
     */
    private $route;

    public function setUpFixtures()
    {
        $this->route = new FixedRoute([
            FixedRoute::OPTION_ROUTE    => 'my/static/url',
            FixedRoute::OPTION_DEFAULTS => [
                'controller' => 'index',
                'method'     => 'list'
            ],
        ]);
    }

    /**
     * Тесты ассемблирования.
     */
    public function testAssemble()
    {
        $this->assertEquals(
            'my/static/url',
            $this->route->assemble(),
            'Ожидается, что фиксированный URL не будет изменен'
        );
        $this->assertEquals(
            'my/static/url',
            $this->route->assemble(['static' => 'url']),
            'Ожидается, что фиксированный URL не будет изменен'
        );
    }

    /**
     * Тесты проверки.
     */
    public function testMatch()
    {
        $this->assertEquals(13, $this->route->match('my/static/url'), 'Ожидается, что URL подходит');
        $this->assertEquals(
            [
                'controller' => 'index',
                'method'     => 'list'
            ],
            $this->route->getParams(),
            'Ожидается, что параметры по умолчанию не будут изменены'
        );
    }
}