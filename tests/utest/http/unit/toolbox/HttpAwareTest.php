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
use utest\http\THttpSupport;

/**
 * Класс HttpAwareTest
 */
class HttpAwareTest extends AwareTestCase
{

    use THttpSupport;

    protected function setUpFixtures()
    {
        $this->registerHttpTools();
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
}
