<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\collection;

use umi\orm\collection\BaseCollection;
use umi\orm\metadata\field\IField;
use umi\orm\metadata\Metadata;
use umi\orm\object\IObject;
use umi\orm\toolbox\factory\MetadataFactory;
use umi\orm\toolbox\factory\ObjectSetFactory;
use umi\orm\toolbox\factory\SelectorFactory;
use utest\orm\ORMTestCase;

/**
 * Тест базового класса коллекции объектов.
 */
class BaseCollectionTest extends ORMTestCase
{

    /**
     * @param array $metadataConfig
     * @return BaseCollection
     */
    protected function getCollection(array $metadataConfig)
    {
        $metadataFactory = new MetadataFactory($this->getDbCluster());
        $this->resolveOptionalDependencies($metadataFactory);

        $metadata = new Metadata('mock', $metadataConfig, $metadataFactory);
        $objectSetFactory = new ObjectSetFactory();
        $this->resolveOptionalDependencies($objectSetFactory);

        $selectorFactory = new SelectorFactory($objectSetFactory);
        $this->resolveOptionalDependencies($selectorFactory);

        $collection = new MockCollection('mock', $metadata, $selectorFactory);
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
                    ],
                ],
                'types'      => ['base' => [], 'type1' => []]
            ]
        );

        $this->assertEquals(
            'mock',
            $collection->getName(),
            'Ожидается, что у коллекции имя заданное на конструкторе'
        );
        $this->assertInstanceOf(
            'umi\orm\metadata\IMetadata',
            $collection->getMetadata(),
            'Ожидается, что метод BaseCollection::getMetadata() вернет IMetadata'
        );

        $this->assertEquals(
            $collection->getName(),
            $collection->getSourceAlias(),
            'Ожидается, что alias для источника данных совпадает с именем коллекции'
        );
        $this->assertEquals(
            'mock:id',
            $collection->getFieldAlias('id'),
            'Ожидается, что alias для поля тестируемой коллекции совмещает имя коллекции и имя поля'
        );

        $this->assertInstanceOf(
            'umi\orm\selector\ISelector',
            $collection->select(),
            'Ожидается, что метод BaseCollection::select() вернет ISelector'
        );
    }

    public function testCollectionFields()
    {
        $collection1 = $this->getCollection(
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
                    ],
                ],
                'types'      => [
                    'base' => [
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

        $collection2 = $this->getCollection(
            [
                'dataSource' => ['sourceName' => 'source'],
                'fields'     => [],
                'types'      => ['base' => []]
            ]
        );

        $field1 = $collection1->getIdentifyField();
        $this->assertInstanceOf(
            'umi\orm\metadata\field\special\IdentifyField',
            $field1,
            'Ожидается, что у коллекции 1 есть поле, являющееся первичным ключом'
        );
        $this->assertEquals('id', $field1->getName(), 'Неверное имя поля первичного ключа');

        $e = null;
        try {
            $collection2->getIdentifyField();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при попытке получить у коллекции поле, являющееся первичным ключом, когда такого нет'
        );

        $field2 = $collection1->getGUIDField();
        $this->assertInstanceOf(
            'umi\orm\metadata\field\special\GuidField',
            $field2,
            'Ожидается, что у коллекции 1 есть поле, являющееся для хранения GUID'
        );
        $this->assertEquals('guid', $field2->getName(), 'Неверное имя поля guid');
        $e = null;
        try {
            $collection2->getGUIDField();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при попытке получить у коллекции поле для хранения GUID, когда такого нет'
        );

        $field3 = $collection1->getObjectTypeField();
        $this->assertInstanceOf(
            'umi\orm\metadata\field\string\StringField',
            $field3,
            'Ожидается, что у коллекции 1 есть поле для хранения информации о типе'
        );
        $this->assertEquals('type', $field3->getName(), 'Неверное имя поля типа');
        $e = null;
        try {
            $collection2->getObjectTypeField();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при попытке получить у типа поле для хранения информации о типе, когда такого нет'
        );

        $field4 = $collection1->getVersionField();
        $this->assertInstanceOf(
            'umi\orm\metadata\field\IField',
            $field4,
            'Ожидается, что у коллекции 1 есть поле для хранения версионности'
        );
        $this->assertEquals('version', $field4->getName(), 'Неверное имя поля версии');
        $e = null;
        try {
            $collection2->getVersionField();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при попытке получить у коллекции поле для хранения версионности, когда такого нет'
        );
    }
}

class MockCollection extends BaseCollection
{

    /**
     * {@inheritdoc}
     */
    public function persistNewObject(IObject $object)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function persistModifiedObject(IObject $object)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function persistDeletedObject(IObject $object)
    {
    }
}
