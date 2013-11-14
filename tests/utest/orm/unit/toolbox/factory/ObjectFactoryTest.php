<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\toolbox\factory;

use umi\orm\toolbox\factory\ObjectFactory;
use umi\orm\toolbox\factory\PropertyFactory;
use utest\orm\ORMDbTestCase;

/**
 * Тест фабрики объектов
 */
class ObjectFactoryTest extends ORMDbTestCase
{

    /**
     * @var ObjectFactory $objectFactory
     */
    protected $objectFactory;

    /**
     * {@inheritdoc}
     */
    protected function getCollections()
    {
        return [];
    }

    protected function setUpFixtures()
    {
        $this->objectFactory = new ObjectFactory(new PropertyFactory());
        $this->resolveOptionalDependencies($this->objectFactory);
    }

    public function testObjectCreation()
    {
        $user = $this->objectFactory->createObject(
            $this->collectionManager->getCollection(self::USERS_USER),
            $this->metadataManager->getMetadata(self::USERS_USER)
                ->getBaseType()
        );

        $this->assertInstanceOf(
            'umi\orm\object\IObject',
            $user,
            'Ожидается, что IObjectFactory::createObject() вернет IObject'
        );
        $this->assertInstanceOf(
            'utest\orm\mock\collections\users\User',
            $user,
            'Ожидается, что IObjectFactory::createObject() вернет объект класса, соответсвующего заданному типу'
        );

        $userGroup = $this->objectFactory->createObject(
            $this->collectionManager->getCollection(self::USERS_GROUP),
            $this->metadataManager->getMetadata(self::USERS_GROUP)
                ->getBaseType()
        );
        $this->assertInstanceOf(
            'umi\orm\object\Object',
            $userGroup,
            'Ожидается, что IObjectFactory::createObject() вернет объект дефолтного класса, если класс у типа не задан'
        );

        $blog = $this->objectFactory->createObject(
            $this->collectionManager->getCollection(self::BLOGS_BLOG),
            $this->metadataManager->getMetadata(self::BLOGS_BLOG)
                ->getBaseType()
        );
        $this->assertInstanceOf(
            'umi\orm\object\IHierarchicObject',
            $blog,
            'Ожидается, что IObjectFactory::createObject() вернет IHierarchicObject, если у коллекции есть иерархия'
        );
        $this->assertInstanceOf(
            'umi\orm\object\HierarchicObject',
            $blog,
            'Ожидается, что IObjectFactory::createObject() вернет объект дефолтного класса, если класс у типа не задан'
        );
    }
}
