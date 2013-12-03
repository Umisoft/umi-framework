<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\collection;

use umi\orm\collection\ICollectionFactory;
use umi\orm\toolbox\factory\CollectionFactory;
use umi\orm\toolbox\factory\ObjectSetFactory;
use umi\orm\toolbox\factory\SelectorFactory;
use utest\orm\ORMDbTestCase;

/**
 * Тесты для фабрики коллекций
 */
class CollectionFactoryTest extends ORMDbTestCase
{

    /**
     * @var ICollectionFactory $collectionFactory
     */
    protected $collectionFactory;

    /**
     * {@inheritdoc}
     */
    protected function getCollectionConfig()
    {
        return [
            self::METADATA_DIR . '/mock/collections',
            [
                self::SYSTEM_HIERARCHY       => [
                    'type' => ICollectionFactory::TYPE_COMMON_HIERARCHY
                ],
                self::BLOGS_BLOG             => [
                    'type'      => ICollectionFactory::TYPE_LINKED_HIERARCHIC,
                    'class'     => 'utest\orm\mock\collections\BlogsCollection',
                    'hierarchy' => self::SYSTEM_HIERARCHY
                ],
                self::USERS_USER             => [
                    'type' => ICollectionFactory::TYPE_SIMPLE
                ]
            ],
            false
        ];
    }

    protected function setUpFixtures()
    {
        $objectSetFactory = new ObjectSetFactory();
        $selectorFactory = new SelectorFactory($objectSetFactory);
        $this->collectionFactory = new CollectionFactory($selectorFactory);
        $this->resolveOptionalDependencies($this->collectionFactory);
    }

    public function testWrongConfig()
    {
        $metadata = $this->getMetadataManager()->getMetadata(self::USERS_USER);
        $e = null;
        try {
            $this->collectionFactory->create(self::USERS_USER, $metadata, []);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\RuntimeException',
            $e,
            'Ожидается исключение при создании коллекции, если в конфиге не задан ее тип'
        );

        $e = null;
        try {
            $this->collectionFactory->create(self::USERS_USER, $metadata, ['type' => 'WrongCollectionType']);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\RuntimeException',
            $e,
            'Ожидается исключение при создании коллекции, если в конфиге задан неверный тип'
        );
    }

    public function testSimpleCollection()
    {

        $metadata = $this->getMetadataManager()->getMetadata(self::USERS_USER);
        $collection = $this->collectionFactory->create(
            self::USERS_USER,
            $metadata,
            ['type' => ICollectionFactory::TYPE_SIMPLE]
        );
        $this->assertInstanceOf(
            'umi\orm\collection\ISimpleCollection',
            $collection,
            'Ожидается, что CollectionFactory::create() вернет ISimpleCollection'
        );
        $this->assertEquals(
            'umi\orm\collection\SimpleCollection',
            get_class($collection),
            'Ожидается, что CollectionFactory::create() вернет SimpleCollection, если в конфиге не задан класс'
        );

        $collection = $this->collectionFactory->create(
            self::USERS_USER,
            $metadata,
            ['type' => ICollectionFactory::TYPE_SIMPLE, 'class' => 'utest\orm\mock\collections\UsersCollection']
        );
        $this->assertInstanceOf(
            'umi\orm\collection\ISimpleCollection',
            $collection,
            'Ожидается, что CollectionFactory::create() вернет ISimpleCollection'
        );
        $this->assertEquals(
            'utest\orm\mock\collections\UsersCollection',
            get_class($collection),
            'Ожидается, что была создана коллекция с заданным классом'
        );

    }

    public function testSimpleHierarchicCollection()
    {

        $metadata = $this->getMetadataManager()->getMetadata(self::SYSTEM_HIERARCHY);
        $collection = $this->collectionFactory->create(
            self::SYSTEM_HIERARCHY,
            $metadata,
            ['type' => ICollectionFactory::TYPE_SIMPLE_HIERARCHIC]
        );
        $this->assertInstanceOf(
            'umi\orm\collection\ISimpleHierarchicCollection',
            $collection,
            'Ожидается, что CollectionFactory::create() вернет ISimpleHierarchicCollection'
        );
        $this->assertEquals(
            'umi\orm\collection\SimpleHierarchicCollection',
            get_class($collection),
            'Ожидается, что CollectionFactory::create() вернет SimpleHierarchicCollection, '
            . 'если в конфиге не задан класс'
        );

        $collection = $this->collectionFactory->create(
            self::SYSTEM_HIERARCHY,
            $metadata,
            ['type' => ICollectionFactory::TYPE_SIMPLE_HIERARCHIC, 'class' => 'utest\orm\mock\collections\Menu']
        );
        $this->assertInstanceOf(
            'umi\orm\collection\ISimpleHierarchicCollection',
            $collection,
            'Ожидается, что CollectionFactory::create() вернет ISimpleHierarchicCollection'
        );
        $this->assertEquals(
            'utest\orm\mock\collections\Menu',
            get_class($collection),
            'Ожидается, что была создана коллекция с заданным классом'
        );

    }

    public function testCommonHierarchy()
    {

        $metadata = $this->getMetadataManager()->getMetadata(self::SYSTEM_HIERARCHY);
        $collection = $this->collectionFactory->create(
            self::SYSTEM_HIERARCHY,
            $metadata,
            ['type' => ICollectionFactory::TYPE_COMMON_HIERARCHY]
        );
        $this->assertInstanceOf(
            'umi\orm\collection\ICommonHierarchy',
            $collection,
            'Ожидается, что CollectionFactory::create() вернет ICommonHierarchy'
        );
        $this->assertEquals(
            'umi\orm\collection\CommonHierarchy',
            get_class($collection),
            'Ожидается, что CollectionFactory::create() вернет CommonHierarchy, если в конфиге не задан класс'
        );

        $collection = $this->collectionFactory->create(
            self::SYSTEM_HIERARCHY,
            $metadata,
            [
                'type'  => ICollectionFactory::TYPE_COMMON_HIERARCHY,
                'class' => 'utest\orm\mock\collections\SystemHierarchy'
            ]
        );
        $this->assertInstanceOf(
            'umi\orm\collection\ICommonHierarchy',
            $collection,
            'Ожидается, что CollectionFactory::create() вернет ICommonHierarchy'
        );
        $this->assertEquals(
            'utest\orm\mock\collections\SystemHierarchy',
            get_class($collection),
            'Ожидается, что была создана коллекция с заданным классом'
        );

    }

    public function testLinkedHierarchicCollection()
    {

        $metadata = $this->getMetadataManager()->getMetadata(self::BLOGS_BLOG);

        $e = null;
        try {
            $this->collectionFactory->create(
                self::USERS_USER,
                $metadata,
                ['type' => ICollectionFactory::TYPE_LINKED_HIERARCHIC]
            );
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\RuntimeException',
            $e,
            'Ожидается исключение при создани связанной иерархической коллекции без указания ее иерархии'
        );

        $e = null;
        try {
            $this->collectionFactory->create(
                self::USERS_USER,
                $metadata,
                ['type' => ICollectionFactory::TYPE_LINKED_HIERARCHIC, 'hierarchy' => self::USERS_USER]
            );
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\RuntimeException',
            $e,
            'Ожидается исключение при создании связанной иерархической коллекции c указанием неверной иерархии'
        );

        $collection = $this->collectionFactory->create(
            self::BLOGS_BLOG,
            $metadata,
            ['type' => ICollectionFactory::TYPE_LINKED_HIERARCHIC, 'hierarchy' => self::SYSTEM_HIERARCHY]
        );
        $this->assertInstanceOf(
            'umi\orm\collection\ILinkedHierarchicCollection',
            $collection,
            'Ожидается, что CollectionFactory::create() вернет ILinkedHierarchicCollection'
        );
        $this->assertEquals(
            'umi\orm\collection\LinkedHierarchicCollection',
            get_class($collection),
            'Ожидается, что CollectionFactory::create() вернет LinkedHierarchicCollection, '
            . 'если в конфиге не задан класс'
        );

        $collection = $this->collectionFactory->create(
            self::BLOGS_BLOG,
            $metadata,
            [
                'type'      => ICollectionFactory::TYPE_LINKED_HIERARCHIC,
                'hierarchy' => self::SYSTEM_HIERARCHY,
                'class'     => 'utest\orm\mock\collections\BlogsCollection'
            ]
        );
        $this->assertInstanceOf(
            'umi\orm\collection\ILinkedHierarchicCollection',
            $collection,
            'Ожидается, что CollectionFactory::create() вернет ISimpleCollection'
        );
        $this->assertEquals(
            'utest\orm\mock\collections\BlogsCollection',
            get_class($collection),
            'Ожидается, что была создана коллекция с заданным классом'
        );
    }
}
