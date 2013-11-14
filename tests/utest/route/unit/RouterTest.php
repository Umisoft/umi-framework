<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\router\unit;

use umi\route\exception\RuntimeException;
use umi\route\IRouter;
use umi\route\Router;
use umi\route\type\FixedRoute;
use umi\route\type\IRoute;
use utest\route\RouteTestCase;

/**
 * Тесты роутера
 */
class RouterTest extends RouteTestCase
{
    /**
     * @var IRouter $router
     */
    protected $router;
    /**
     * @var IRouter $router2
     */
    protected $router2;

    public function setUpFixtures()
    {
        $first = new FixedRoute(
            [
                IRoute::OPTION_ROUTE    => '/first/route',
                IRoute::OPTION_DEFAULTS => ['matched' => 'first']
            ]
        );

        $third = new FixedRoute(
            [
                IRoute::OPTION_ROUTE    => '/third',
                IRoute::OPTION_DEFAULTS => ['more' => 'third']
            ]
        );

        $second = new FixedRoute(
            [
                IRoute::OPTION_ROUTE    => '/second/route',
                IRoute::OPTION_DEFAULTS => ['matched' => 'second']
            ], ['third' => $third]);

        $empty = new FixedRoute([
            IRoute::OPTION_ROUTE    => '',
            IRoute::OPTION_DEFAULTS => ['matched' => 'empty']
        ]);

        $this->router = new Router([
            'first'  => $first,
            'second' => $second,
            'empty'  => $empty,
        ]);

        $this->router2 = new Router([
            'first'  => $first,
            'second' => $second,
        ]);
    }

    /**
     * Тесты проверок на совпадение.
     */
    public function testMatch()
    {
        $result = $this->router->match('/second/route');
        $this->assertNull($result->getUnmatchedUrl(), 'Ожидается, что второй route полностью подойдет.');
        $this->assertEquals(
            '/second/route',
            $result->getMatchedUrl(),
            'Ожидается, что совпадение со  вторым route будет найдено.'
        );
        $this->assertEquals(['matched' => 'second'], $result->getMatches(), 'Ожидается, что второй route подойдет.');
        $this->assertEquals('second', $result->getName(), 'Ожидается, что второй route подойдет.');

        $result = $this->router->match('/second/route/third');
        $this->assertNull($result->getUnmatchedUrl(), 'Ожидается, что третий route полностью подойдет.');
        $this->assertEquals(
            '/second/route/third',
            $result->getMatchedUrl(),
            'Ожидается, что совпадение с 3им route будет найдено.'
        );
        $this->assertEquals(
            ['matched' => 'second', 'more' => 'third'],
            $result->getMatches(),
            'Ожидается, что будут совпадения 3го и 2го роута.'
        );
        $this->assertEquals('second/third', $result->getName(), 'Ожидается, что второй route подойдет.');
    }

    public function testWrongUrls()
    {
        $result = $this->router2->match('/second');

        $this->assertEmpty($result->getMatchedUrl(), 'Ожидается, что route не будет найден.');
        $this->assertEmpty($result->getMatches(), 'Ожидается, что route не будет найден.');
        $this->assertEmpty($result->getName(), 'Ожидается, что route не будет найден.');
        $this->assertEquals('/second', $result->getUnmatchedUrl(), 'Ожидается, что route не будет найден.');

        $result = $this->router->match('/second/route/test');
        $this->assertEquals('/test', $result->getUnmatchedUrl(), 'Ожидается, что останется несовпавшая часть.');
        $this->assertEquals(
            '/second/route',
            $result->getMatchedUrl(),
            'Ожидается, что совпадение со  вторым route будет найдено.'
        );
        $this->assertEquals(
            ['matched' => 'second'],
            $result->getMatches(),
            'Ожидается, что при неполном соответствии параметры будут найдены.'
        );
        $this->assertEquals(
            'second',
            $result->getName(),
            'Ожидается, что при неполном соответствии второй route подойдет.'
        );

        $result = $this->router->match('/second/route/');
        $this->assertEquals('/', $result->getUnmatchedUrl(), 'Ожидается, что несовпавшая часть будет /.');
        $this->assertEquals(
            '/second/route',
            $result->getMatchedUrl(),
            'Ожидается, что совпадение со  вторым route будет найдено.'
        );
        $this->assertEquals(
            ['matched' => 'second'],
            $result->getMatches(),
            'Ожидается, что при неполном соответствии параметры будут найдены.'
        );
        $this->assertEquals(
            'second',
            $result->getName(),
            'Ожидается, что при неполном соответствии второй route подойдет.'
        );
    }

    public function testInheritance()
    {
        $result = $this->router->match('/second/route/third/nextPage');

        $this->assertEquals('/second/route/third', $result->getMatchedUrl());
        $this->assertEquals('/nextPage', $result->getUnmatchedUrl());
        $this->assertEquals('second/third', $result->getName());
    }

    public function testEmpty()
    {
        $result = $this->router->match('');

        $this->assertEquals('empty', $result->getName());
        $this->assertEmpty($result->getMatchedUrl());
        $this->assertEmpty($result->getUnmatchedUrl());
    }

    /**
     * Тесты проверок на ассемблирование.
     */
    public function testAssemble()
    {
        $this->assertEquals('/second/route/third', $this->router->assemble('second/third'));
        $this->assertEquals('/second/route', $this->router->assemble('second'));
    }

    /**
     * @test исключения, если роутер для ассемблирования не найден.
     * @expectedException RuntimeException
     */
    public function routePathNotFound()
    {
        $this->router->assemble('second/five');
    }

    public function testBaseUrl()
    {
        $this->assertSame($this->router, $this->router->setBaseUrl('testUrl'));
        $this->assertEquals('testUrl', $this->router->getBaseUrl());

        $this->assertEquals($this->router->getBaseUrl(), $this->router->assemble(''));
    }
}