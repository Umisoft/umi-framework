<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\config\unit\toolbox;

use umi\config\entity\ConfigSource;
use utest\AwareTestCase;

/**
 * Класс ConfigAwareTest
 */
class ConfigAwareTest extends AwareTestCase
{

    public function testConfigIOAware()
    {
        $this->awareClassTest(
            'utest\config\mock\toolbox\MockConfigIOAware',
            'umi\config\exception\RequiredDependencyException',
            'Config IO service is not injected in class "utest\config\mock\toolbox\MockConfigIOAware".'
        );
    }

    public function testConfigEntityFactoryAware()
    {
        $this->awareClassTest(
            'utest\config\mock\toolbox\MockConfigEntityFactoryAware',
            'umi\config\exception\RequiredDependencyException',
            'Config entity factory is not injected in class "utest\config\mock\toolbox\MockConfigEntityFactoryAware".'
        );
    }

    public function testConfigAliasResolverAware()
    {
        $this->awareClassTest(
            'utest\config\mock\toolbox\MockConfigAliasResolverAware',
            'umi\config\exception\RequiredDependencyException',
            'Config IO service is not injected in class "utest\config\mock\toolbox\MockConfigAliasResolverAware".'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function resolveParam($className)
    {
        switch ($className) {
            case 'umi\config\entity\IConfigSource':
                $src = [];

                return new ConfigSource($src, '~/alias.php');
        }

        return parent::resolveParam($className);
    }
}