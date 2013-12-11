<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\toolbox;

use utest\AwareTestCase;
use utest\dbal\TDbalSupport;

/**
 * Тестирование внедрения компонента для работы с бд
 */
class ClusterAwareTest extends AwareTestCase
{
    use TDbalSupport;

    protected function setUpFixtures()
    {
        $this->registerDbalTools();
    }

    public function testClusterAware()
    {
        $this->awareClassTest(
            'utest\dbal\mock\MockClusterAware',
            'umi\dbal\exception\RequiredDependencyException',
            'DB cluster is not injected in class "utest\dbal\mock\MockClusterAware".'
        );

        $this->successfulInjectionTest('utest\dbal\mock\MockClusterAware', 'umi\dbal\cluster\IDbCluster');
    }
}
