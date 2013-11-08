<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\toolbox;

use umi\orm\toolbox\IORMTools;
use utest\TestCase;

/**
 * Тестирование инструментария ORM
 */
class ORMToolsTest extends TestCase
{

    public function testORMServices()
    {
        /**
         * @var IORMTools $ormTools
         */
        $ormTools = $this->getTestToolkit()
            ->getToolbox(IORMTools::ALIAS);
        $this->resolveOptionalDependencies($ormTools);

        $objectManager = $ormTools->getObjectManager();
        $this->assertInstanceOf(
            'umi\orm\manager\IObjectManager',
            $objectManager,
            'Ожидается, что IORMTools::getObjectManager() вернет IObjectManager'
        );
        $this->assertTrue(
            $objectManager === $ormTools->getObjectManager(),
            'Ожидается, что у инструментария ORM один менеджер объектов'
        );

        $metadataManager = $ormTools->getMetadataManager();
        $this->assertInstanceOf(
            'umi\orm\metadata\IMetadataManager',
            $metadataManager,
            'Ожидается, что IORMTools::getMetadataManager() вернет IMetadataManager'
        );
        $this->assertTrue(
            $metadataManager === $ormTools->getMetadataManager(),
            'Ожидается, что у инструментария ORM один менеджер метаданных'
        );

        $collectionManager = $ormTools->getCollectionManager();
        $this->assertInstanceOf(
            'umi\orm\collection\ICollectionManager',
            $collectionManager,
            'Ожидается, что IORMTools::getCollectionManager() вернет ICollectionManager'
        );
        $this->assertTrue(
            $collectionManager === $ormTools->getCollectionManager(),
            'Ожидается, что у инструментария ORM один менеджер коллекций'
        );

        $objectPersister = $ormTools->getObjectPersister();
        $this->assertInstanceOf(
            'umi\orm\persister\IObjectPersister',
            $objectPersister,
            'Ожидается, что IORMTools::getObjectPersister() вернет IObjectPersister'
        );
        $this->assertTrue(
            $objectPersister === $ormTools->getObjectPersister(),
            'Ожидается, что у инструментария ORM один синхронизатор объектов'
        );

    }
}