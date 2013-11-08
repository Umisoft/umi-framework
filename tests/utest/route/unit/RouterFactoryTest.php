<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\router\unit;

use umi\route\IRouterFactory;
use umi\route\toolbox\factory\RouterFactory;
use utest\TestCase;

/**
 * Тесты инструментов роутинга
 */
class RouterFactoryTest extends TestCase
{
    /**
     * @var IRouterFactory $routeTools
     */
    public $factory;

    public function setUpFixtures()
    {
        $this->factory = new RouterFactory();
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
                    'type'      => 'fixed',
                    'route'     => '/sample/route',
                    'defaults'  => ['controller' => 'sample', 'method' => 'route'],
                    'subroutes' => [
                        'ext' => [
                            'type'     => 'fixed',
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
}