<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\http\unit\toolbox;

use utest\AwareTestCase;

/**
 * Класс HttpAwareTest
 */
class HttpAwareTest extends AwareTestCase
{

    protected function setUpFixtures()
    {
        $this->getTestToolkit()->registerToolbox(
            require(__DIR__ . '/../../../../../library/umi/http/toolbox/config.php')
        );
    }

    public function testHttpAware()
    {
        $this->awareClassTest(
            'utest\http\mock\toolbox\MockHttpAware',
            'umi\http\exception\RequiredDependencyException',
            'Http factory is not injected in class "utest\http\mock\toolbox\MockHttpAware".'
        );

        $this->successfulInjectionTest('utest\http\mock\toolbox\MockHttpAware', 'umi\http\IHttpFactory');
    }

    public function testHttpParamCollectionAware()
    {
        $this->awareClassTest(
            'utest\http\mock\toolbox\MockParamCollectionAware',
            'umi\http\exception\RequiredDependencyException',
            'Param collection factory is not injected in class "utest\http\mock\toolbox\MockParamCollectionAware".'
        );

        $this->successfulInjectionTest(
            'utest\http\mock\toolbox\MockParamCollectionAware',
            'umi\http\request\param\IParamCollectionFactory'
        );
    }

    public function testHttpHeaderCollectionAware()
    {
        $this->awareClassTest(
            'utest\http\mock\toolbox\MockHeaderCollectionAware',
            'umi\http\exception\RequiredDependencyException',
            'HTTP response header collection factory is not injected in class "utest\http\mock\toolbox\MockHeaderCollectionAware".'
        );

        $this->successfulInjectionTest(
            'utest\http\mock\toolbox\MockHeaderCollectionAware',
            'umi\http\response\header\IHeaderCollectionFactory'
        );
    }
}