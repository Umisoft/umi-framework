<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\session\unit\toolbox;

use utest\AwareTestCase;
use utest\http\THttpSupport;
use utest\session\TSessionSupport;

/**
 * Класс SessionAwareTest
 */
class SessionAwareTest extends AwareTestCase
{
    use THttpSupport;
    use TSessionSupport;

    protected function setUpFixtures() {
        $this->registerHttpTools();
        $this->registerSessionTools();
    }

    public function testSessionAware()
    {
        $this->awareClassTest(
            'utest\session\mock\toolbox\MockSessionAware',
            'umi\session\exception\RequiredDependencyException',
            'Session service is not injected in class "utest\session\mock\toolbox\MockSessionAware".'
        );

        $this->successfulInjectionTest(
            'utest\session\mock\toolbox\MockSessionAware',
            'umi\session\ISession'
        );
    }

    public function testSessionManagerAware()
    {
        $this->awareClassTest(
            'utest\session\mock\toolbox\MockSessionManagerAware',
            'umi\session\exception\RequiredDependencyException',
            'Session manager is not injected in class "utest\session\mock\toolbox\MockSessionManagerAware".'
        );

        $this->successfulInjectionTest(
            'utest\session\mock\toolbox\MockSessionManagerAware',
            'umi\session\ISessionManager'
        );
    }

}