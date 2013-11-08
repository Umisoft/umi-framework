<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\collection;

use umi\orm\collection\SimpleCollection;
use umi\orm\metadata\field\IField;
use umi\orm\metadata\IObjectType;
use umi\orm\metadata\Metadata;
use umi\orm\object\IObject;
use umi\orm\toolbox\factory\MetadataFactory;
use umi\orm\toolbox\factory\ObjectSetFactory;
use umi\orm\toolbox\factory\SelectorFactory;
use utest\TestCase;

/**
 * Тест коллекции простых обьектов.
 */
class SimpleCollectionTest extends TestCase
{

    /**
     * @param array $metadataConfig
     * @return SimpleCollection
     */
    protected function getCollection(array $metadataConfig)
    {
        $metadataFactory = new MetadataFactory($this->getDbCluster());
        $this->resolveOptionalDependencies($metadataFactory);

        $metadata = new Metadata('mock', $metadataConfig, $metadataFactory);
        $objectSetFactory = new ObjectSetFactory();
        $selectorFactory = new SelectorFactory($objectSetFactory);

        $collection = new SimpleCollection('mock', $metadata, $selectorFactory);
        $this->resolveOptionalDependencies($collection);

        return $collection;
    }

    public function testMethods()
    {

        $collection = $this->getCollection(
            [
                'dataSource' => ['sourceName' => 'source'],
                'fields'     => [
                    IObject::FIELD_IDENTIFY => [
                        'type'       => IField::TYPE_IDENTIFY,
                        'columnName' => 'id',
                        'accessor'   => 'getId'
                    ],
                    IObject::FIELD_GUID     => [
                        'type'       => IField::TYPE_GUID,
                        'columnName' => 'guid',
                        'accessor'   => 'getGuid',
                        'mutator'    => 'setGuid'
                    ],
                    IObject::FIELD_TYPE     => [
                        'type'       => IField::TYPE_STRING,
                        'columnName' => 'type',
                        'accessor'   => 'getType',
                        'readOnly'   => true
                    ],
                    IObject::FIELD_VERSION  => [
                        'type'         => IField::TYPE_VERSION,
                        'columnName'   => 'version',
                        'accessor'     => 'getVersion',
                        'mutator'      => 'setVersion',
                        'defaultValue' => 1
                    ]
                ],
                'types'      => [
                    'base'  => [
                        'fields' => [
                            IObject::FIELD_IDENTIFY,
                            IObject::FIELD_GUID,
                            IObject::FIELD_TYPE,
                            IObject::FIELD_VERSION
                        ]
                    ],
                    'type1' => [
                        'fields' => [
                            IObject::FIELD_IDENTIFY,
                            IObject::FIELD_GUID,
                            IObject::FIELD_TYPE,
                            IObject::FIELD_VERSION
                        ]
                    ]
                ]
            ]
        );

        $object = $collection->add();
        $this->assertInstanceOf(
            'umi\orm\object\IObject',
            $object,
            'Ожидается, что метод ISimpleCollection::add() вернет IObject'
        );
        $this->assertEquals(
            IObjectType::BASE,
            $object->getTypeName(),
            'Ожидается, что по умолчанию добавляется объект базового типа'
        );

        $object2 = $collection->add('type1');
        $this->assertEquals('type1', $object2->getTypeName(), 'Ожидается, что создается объект заданного типа');

        $e = null;
        try {
            $collection->add('not_existing_type');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается, что нельзя добавить объект несуществующего типа'
        );

        /**
         * @var IObject $mockObject
         */
        $mockObject = $this->getMock('umi\orm\object\Object', ['getCollection'], [], '', false);
        $mockObject->expects($this->any())
            ->method('getCollection')
            ->will(
                $this->returnCallback(
                    function () {
                        return new \stdClass();
                    }
                )
            );

        $this->assertTrue(
            $collection->contains($object),
            'Ожидается, что коллекция содержит созданный в ней объект'
        );
        $this->assertFalse(
            $collection->contains($mockObject),
            'Ожидается, что коллекция не содержит объект, коллекция которого не равна этой коллекции'
        );

        $e = null;
        try {
            $collection->delete($mockObject);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\RuntimeException',
            $e,
            'Ожидается, исключение при попытке удалить из коллекции не принадлежащий ей объект'
        );

        $e = null;
        try {
            $collection->persistNewObject($mockObject);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NotAllowedOperationException',
            $e,
            'Ожидается, что связанная иерархическая коллекция не может сохранить новый не принадлежащий ей объект'
        );

        $e = null;
        try {
            $collection->persistModifiedObject($mockObject);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NotAllowedOperationException',
            $e,
            'Ожидается, что связанная иерархическая коллекция не может сохранить измененный не принадлежащий ей объект'
        );

        $e = null;
        try {
            $collection->persistDeletedObject($mockObject);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NotAllowedOperationException',
            $e,
            'Ожидается, что связанная иерархическая коллекция не может сохранить удаленный не принадлежащий ей объект'
        );

        /*			$object->setGUID('9ee6745f-f40d-46d8-8043-d959594628ce');
                    $this->assertTrue($object === $collection->get('9ee6745f-f40d-46d8-8043-d959594628ce'), 'Ожидается, что метод SimpleCollection::get() вернет IObject');*/

        $this->assertInstanceOf(
            'umi\orm\collection\ISimpleCollection',
            $collection->delete($object),
            'Ожидается, что метод ISimpleCollection::delete() вернет ISimpleCollection'
        );
    }
}
