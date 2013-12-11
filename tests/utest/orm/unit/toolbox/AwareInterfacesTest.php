<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\toolbox;

use utest\AwareTestCase;
use utest\dbal\TDbalSupport;
use utest\orm\TORMSupport;

/**
 * Тестирование внедрения обслуживающих сервисов ORMTools
 */
class AwareInterfacesTest extends AwareTestCase
{

    use TORMSupport;
    use TDbalSupport;

    /**
     * {@inheritdoc}
     */
    protected function setUpFixtures()
    {
        $this->registerDbalTools();
        $this->registerORMTools();
    }

    public function testObjectManagerAware()
    {
        $this->awareClassTest(
            'utest\orm\mock\MockObjectManagerAware',
            'umi\orm\exception\RequiredDependencyException',
            'Object manager is not injected in class "utest\orm\mock\MockObjectManagerAware".'
        );

        $this->successfulInjectionTest('utest\orm\mock\MockObjectManagerAware', 'umi\orm\manager\IObjectManager');
    }

    public function testObjectPersisterAware()
    {
        $this->awareClassTest(
            'utest\orm\mock\MockObjectPersisterAware',
            'umi\orm\exception\RequiredDependencyException',
            'Object persister is not injected in class "utest\orm\mock\MockObjectPersisterAware".'
        );

        $this->successfulInjectionTest('utest\orm\mock\MockObjectPersisterAware', 'umi\orm\persister\IObjectPersister');
    }

    public function testCollectionManagerAware()
    {
        $this->awareClassTest(
            'utest\orm\mock\MockCollectionManagerAware',
            'umi\orm\exception\RequiredDependencyException',
            'Collection manager is not injected in class "utest\orm\mock\MockCollectionManagerAware".'
        );

        $this->successfulInjectionTest(
            'utest\orm\mock\MockCollectionManagerAware',
            'umi\orm\collection\ICollectionManager'
        );
    }

    public function testMetadataManagerAware()
    {
        $this->awareClassTest(
            'utest\orm\mock\MockMetadataManagerAware',
            'umi\orm\exception\RequiredDependencyException',
            'Metadata manager is not injected in class "utest\orm\mock\MockMetadataManagerAware".'
        );

        $this->successfulInjectionTest('utest\orm\mock\MockMetadataManagerAware', 'umi\orm\metadata\IMetadataManager');
    }
}
