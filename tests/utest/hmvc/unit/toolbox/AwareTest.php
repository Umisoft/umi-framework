<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\hmvc\unit\toolbox;

use utest\AwareTestCase;
use utest\hmvc\THMVCSupport;

/**
 * Class AwareTest
 */
class AwareTest extends AwareTestCase
{
    use THMVCSupport;

    const EXCEPTION_CLASS = 'umi\hmvc\exception\RequiredDependencyException';

    protected function setUpFixtures() {
        $this->registerHMVCTools();
    }

    public function testComponentAware()
    {
        $this->awareClassTest(
            'utest\hmvc\mock\toolbox\MockComponentAware',
            self::EXCEPTION_CLASS,
            'HMVC component factory is not injected in class "utest\hmvc\mock\toolbox\MockComponentAware".'
        );

        $this->successfulInjectionTest(
            'utest\hmvc\mock\toolbox\MockComponentAware',
            'umi\hmvc\component\IComponentFactory'
        );
    }

    public function testComponentRequestAware()
    {
        $this->awareClassTest(
            'utest\hmvc\mock\toolbox\MockComponentRequestAware',
            self::EXCEPTION_CLASS,
            'HMVC component request factory is not injected in class "utest\hmvc\mock\toolbox\MockComponentRequestAware".'
        );

        $this->successfulInjectionTest(
            'utest\hmvc\mock\toolbox\MockComponentRequestAware',
            'umi\hmvc\component\request\IComponentRequestFactory'
        );
    }

    public function testComponentResponseAware()
    {
        $this->awareClassTest(
            'utest\hmvc\mock\toolbox\MockComponentResponseAware',
            self::EXCEPTION_CLASS,
            'HMVC component response factory is not injected in class "utest\hmvc\mock\toolbox\MockComponentResponseAware".'
        );

        $this->successfulInjectionTest(
            'utest\hmvc\mock\toolbox\MockComponentResponseAware',
            'umi\hmvc\component\response\IComponentResponseFactory'
        );
    }

    public function testMVCLayerAware()
    {
        $this->awareClassTest(
            'utest\hmvc\mock\toolbox\MockMVCLayerAware',
            self::EXCEPTION_CLASS,
            'MVC layer factory is not injected in class "utest\hmvc\mock\toolbox\MockMVCLayerAware".'
        );

        $this->successfulInjectionTest(
            'utest\hmvc\mock\toolbox\MockMVCLayerAware',
            'umi\hmvc\IMVCLayerFactory'
        );
    }
}
 