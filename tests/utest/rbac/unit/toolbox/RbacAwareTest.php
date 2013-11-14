<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\rbac\unit\toolbox;

use utest\AwareTestCase;

class RbacAwareTest extends AwareTestCase
{

    protected function setUpFixtures() {
        $this->getTestToolkit()->registerToolbox(
            require(LIBRARY_PATH . '/rbac/toolbox/config.php')
        );
    }

    public function testRbacAware()
    {
        $this->awareClassTest(
            'utest\rbac\mock\toolbox\MockRbacAware',
            'umi\rbac\exception\RequiredDependencyException',
            'Rbac role factory is not injected in class "utest\rbac\mock\toolbox\MockRbacAware".'
        );

        $this->successfulInjectionTest(
            'utest\rbac\mock\toolbox\MockRbacAware',
            'umi\rbac\IRoleFactory'
        );
    }
}