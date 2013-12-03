<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\templating\unit\toolbox;

use utest\AwareTestCase;
use utest\templating\TTemplatingSupport;

/**
 * Class AwareTest
 */
class AwareTest extends AwareTestCase
{
    use TTemplatingSupport;

    protected function setUpFixtures() {
        $this->registerTemplatingTools();
    }
    
    public function testTemplatingAware()
    {
        $this->awareClassTest(
            'utest\templating\mock\toolbox\MockTemplatingAware',
            'umi\templating\exception\RequiredDependencyException',
            'Template engine factory is not injected in class "utest\templating\mock\toolbox\MockTemplatingAware".'
        );

        $this->successfulInjectionTest(
            'utest\templating\mock\toolbox\MockTemplatingAware',
            'umi\templating\engine\ITemplateEngineFactory'
        );
    }
}
 