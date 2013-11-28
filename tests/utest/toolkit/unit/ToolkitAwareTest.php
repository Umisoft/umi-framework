<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace utest\toolkit\unit;

use utest\AwareTestCase;

/**
 * Class ToolkitAwareTest
 */
class ToolkitAwareTest extends AwareTestCase
{

    public function testToolkitAware()
    {
        $this->awareClassTest(
            'utest\toolkit\mock\MockToolkitAware',
            'umi\toolkit\exception\RequiredDependencyException',
            'Toolkit is not injected in class "utest\toolkit\mock\MockToolkitAware".'
        );

        $this->successfulInjectionTest('utest\toolkit\mock\MockToolkitAware', 'umi\toolkit\IToolkit');
    }

}
 