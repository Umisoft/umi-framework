<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\func\collection\simplehierarchic;

use umi\orm\collection\SimpleHierarchicCollection;
use umi\orm\metadata\IObjectType;
use utest\orm\ORMTestCase;

class SimpleHierarchicCollectionTest extends ORMTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getCollections()
    {
        return [];
    }

    public function testMethods()
    {

        /**
         * @var SimpleHierarchicCollection $collection
         */
        $collection = $this->collectionManager->getCollection(self::SYSTEM_MENU);

        $object = $collection->add('menuItem1');
        $this->assertInstanceOf(
            'umi\orm\object\IHierarchicObject',
            $object,
            'Ожидается, что метод IHierarchicCollection::add() вернет IHierarchicObject'
        );
        $this->assertEquals(
            IObjectType::BASE,
            $object->getTypeName(),
            'Ожидается, что по умолчанию добавляется объект базового типа'
        );
        $this->assertNull($object->getParent(), 'Ожидается, что по умолчанию объект был добавлен у корень иерархии');

        $object2 = $collection->add('menuItem2', IObjectType::BASE, $object);
        $this->assertTrue(
            $object === $object2->getParent(),
            'Ожидается, что объект был созданным с заданным родителем'
        );
        $this->assertEquals(
            1,
            $object->getChildCount(),
            'Ожидается, что при добавлении объекта у его родителя возрастет количество детей'
        );

        $e = null;
        try {
            $collection->add('menuItem3', 'not_existing_type');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается, что нельзя добавить объект несуществующего типа'
        );

        $wrongObject = $this->collectionManager->getCollection(self::BLOGS_BLOG)
            ->add('blog1');

        $this->assertTrue($collection->contains($object), 'Ожидается, что коллекция содержит созданный в ней объект');
        $this->assertFalse(
            $collection->contains($wrongObject),
            'Ожидается, что коллекция не содержит объект, коллекция которого не равна этой коллекции'
        );

        $e = null;
        try {
            $collection->add('menuItem4', IObjectType::BASE, $wrongObject);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\InvalidArgumentException',
            $e,
            'Ожидается, что при добавлении объекта нельзя выставить в качестве родителя объект другой коллекции'
        );

        $e = null;
        try {
            $collection->delete($wrongObject);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\RuntimeException',
            $e,
            'Ожидается, исключение при попытке удалить из коллекции не принадлежащий ей объект'
        );

        $this->assertInstanceOf(
            'umi\orm\collection\IHierarchicCollection',
            $collection->delete($object2),
            'Ожидается, что метод ISimpleCollection::delete() вернет ISimpleCollection'
        );
        $this->assertEquals(
            0,
            $object->getChildCount(),
            'Ожидается, что при удалении объекта у его родителя уменьшится количество детей'
        );

    }

}
