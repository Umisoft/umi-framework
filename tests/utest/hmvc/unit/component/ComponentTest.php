<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\hmvc\unit\component;

use umi\hmvc\component\IComponent;
use umi\hmvc\exception\OutOfBoundsException;
use umi\route\IRouteFactory;
use umi\templating\engine\ITemplateEngineFactory;
use utest\hmvc\HMVCTestCase;

/**
 * Class MvcComponentTest
 */
class ComponentTest extends HMVCTestCase
{
    public function testCallController()
    {
        $component = $this->getComponent(
            [
                IComponent::OPTION_CONTROLLERS => [
                    'test' => 'utest\hmvc\mock\controller\MockController'
                ]
            ]
        );

        $response = $component->call('test', $this->getRequest(''));

        $this->assertEquals('mock', $response->getContent());
        $this->assertEquals(200, $response->getCode());
    }

    public function testCallErrorController()
    {
        $component = $this->getComponent(
            [
                IComponent::OPTION_CONTROLLERS => [
                    'exception'                  => 'utest\hmvc\mock\controller\MockExceptionController',
                    IComponent::ERROR_CONTROLLER => 'utest\hmvc\mock\controller\MockErrorController',
                ]
            ]
        );

        $response = $component->call('exception', $this->getRequest(''));

        $this->assertEquals('Http exception thrown.', $response->getContent());
        $this->assertEquals(401, $response->getCode());
    }

    /**
     * @test
     * @expectedException \umi\hmvc\exception\http\HttpException
     */
    public function noErrorController()
    {
        $component = $this->getComponent(
            [
                IComponent::OPTION_CONTROLLERS => [
                    'exception' => 'utest\hmvc\mock\controller\MockExceptionController',
                ]
            ]
        );

        $component->call('exception', $this->getRequest(''));
    }

    public function testRouter()
    {
        $component = $this->getComponent(
            [
                IComponent::OPTION_ROUTES => [
                    'route' => [
                        'type'     => IRouteFactory::ROUTE_FIXED,
                        'route'    => '/',
                        'defaults' => [
                            'data' => 'route data'
                        ]
                    ]
                ]
            ]
        );

        $this->assertInstanceOf('umi\route\IRouter', $component->getRouter());

        $component->getRouter()
            ->setBaseUrl('/baseUrl');
        $this->assertEquals(
            '/baseUrl',
            $component->getRouter()
                ->getBaseUrl()
        );

        $this->assertEquals(
            [
                'data' => 'route data'
            ],
            $component->getRouter()
                ->match('/')
                ->getMatches()
        );
    }

    public function testChildComponent()
    {
        $component = $this->getComponent(
            [
                IComponent::OPTION_COMPONENTS => [
                    'child' => []
                ]
            ]
        );

        $childComponent = $component->getChildComponent('child');
        $this->assertInstanceOf('umi\hmvc\component\IComponent', $childComponent);
    }

    /**
     * @test
     * @expectedException OutOfBoundsException
     */
    public function noChildComponent()
    {
        $this->getComponent([])
            ->getChildComponent('child');
    }

    public function testExecuteController()
    {
        $component = $this->getComponent(
            [
                IComponent::OPTION_COMPONENTS  => [
                    'child' => [
                        IComponent::OPTION_CONTROLLERS => [
                            'exception' => 'utest\hmvc\mock\controller\MockExceptionController',
                        ],
                        IComponent::OPTION_ROUTES      => [
                            'exception' => [
                                'type'     => IRouteFactory::ROUTE_FIXED,
                                'route'    => '/exception',
                                'defaults' => [
                                    IComponent::MATCH_CONTROLLER => 'exception'
                                ]
                            ]
                        ]
                    ],
                ],
                IComponent::OPTION_CONTROLLERS => [
                    'mock'                       => 'utest\hmvc\mock\controller\MockController',
                    'exception'                  => 'utest\hmvc\mock\controller\MockExceptionController',
                    IComponent::ERROR_CONTROLLER => 'utest\hmvc\mock\controller\MockErrorController',
                ],
                IComponent::OPTION_ROUTES      => [
                    'mockController'      => [
                        'type'     => IRouteFactory::ROUTE_FIXED,
                        'route'    => '/mock',
                        'defaults' => [
                            IComponent::MATCH_CONTROLLER => 'mock'
                        ]
                    ],
                    'exceptionController' => [
                        'type'     => IRouteFactory::ROUTE_FIXED,
                        'route'    => '/exception',
                        'defaults' => [
                            IComponent::MATCH_CONTROLLER => 'exception'
                        ]
                    ],
                    'childComponent'      => [
                        'type'     => IRouteFactory::ROUTE_FIXED,
                        'route'    => '/child',
                        'defaults' => [
                            IComponent::MATCH_COMPONENT => 'child'
                        ]
                    ],
                ]
            ]
        );

        /*
         * Call mock controller.
         */
        $request = $this->getRequest('/mock');
        $response = $component->execute($request);

        $this->assertEquals('mock', $response->getContent());
        $this->assertEquals(200, $response->getCode());

        /*
         * Call exception controller.
         */
        $request = $this->getRequest('/exception');
        $response = $component->execute($request);

        $this->assertEquals('Http exception thrown.', $response->getContent());
        $this->assertEquals(401, $response->getCode());

        /*
         * Call exception child component.
         */
        $request = $this->getRequest('/child/exception');
        $response = $component->execute($request);

        $this->assertEquals('Http exception thrown.', $response->getContent());
        $this->assertEquals(401, $response->getCode());

        /**
         * No url in component.
         */
        $request = $this->getRequest('/wrong');
        $response = $component->execute($request);

        $this->assertEquals('URL not found by router.', $response->getContent());
        $this->assertEquals(404, $response->getCode());

        /**
         * No url in child component.
         */
        $request = $this->getRequest('/child/wrong');
        $response = $component->execute($request);

        $this->assertEquals('URL not found by router.', $response->getContent());
        $this->assertEquals(404, $response->getCode());
    }

    public function testRenderController()
    {
        $component = $this->getComponent(
            [
                IComponent::OPTION_CONTROLLERS => [
                    'test' => 'utest\hmvc\mock\controller\MockRenderController'
                ],
                IComponent::OPTION_VIEW        => [
                    'type'      => ITemplateEngineFactory::PHP_ENGINE,
                    'directory' => HMVCTestCase::DIRECTORY . '/mock/view',
                    'extension' => 'phtml'
                ]
            ]
        );

        $request = $this->getRequest(
            '/',
            [
                'data' => 'route data'
            ]
        );
        $response = $component->call('test', $request);

        $this->assertEquals('Controller result: route data', $response->getContent());
        $this->assertEquals(200, $response->getCode());
    }
}