<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\route\unit\toolbox;

use utest\AwareTestCase;

class RouteAwareTests extends AwareTestCase
{

    public function testRouteAware()
    {
        $this->awareClassTest(
            'utest\route\mock\toolbox\MockRouteAware',
            'umi\route\exception\RequiredDependencyException',
            'Router factory is not injected in class "utest\route\mock\toolbox\MockRouteAware".'
        );

        $this->successfulInjectionTest(
            'utest\route\mock\toolbox\MockRouteAware',
            'umi\route\IRouterFactory'
        );
    }

    public function testRouteFactoryAware()
    {
        $this->awareClassTest(
            'utest\route\mock\toolbox\MockRouteFactoryAware',
            'umi\route\exception\RequiredDependencyException',
            'Route factory is not injected in class "utest\route\mock\toolbox\MockRouteFactoryAware".'
        );

        $this->successfulInjectionTest(
            'utest\route\mock\toolbox\MockRouteFactoryAware',
            'umi\route\type\factory\IRouteFactory'
        );
    }
}