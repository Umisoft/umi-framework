<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\collection;

use umi\orm\collection\SimpleHierarchicCollection;
use umi\orm\metadata\field\IField;
use umi\orm\metadata\Metadata;
use umi\orm\object\IHierarchicObject;
use umi\orm\object\IObject;
use umi\orm\toolbox\factory\MetadataFactory;
use umi\orm\toolbox\factory\ObjectSetFactory;
use umi\orm\toolbox\factory\SelectorFactory;
use utest\orm\ORMTestCase;

/**
 * Тесты иерархической коллекции
 */
class SimpleHierarchicCollectionTest extends ORMTestCase
{

    /**
     * @param array $metadataConfig
     * @return SimpleHierarchicCollection
     */
    protected function getCollection(array $metadataConfig = [])
    {

        if (empty($metadataConfig)) {
            $metadataConfig = [
                'dataSource' => ['sourceName' => 'source'],
                'fields'     => [
                    IObject::FIELD_IDENTIFY                  => [
                        'type'       => IField::TYPE_IDENTIFY,
                        'columnName' => 'id',
                        'accessor'   => 'getId'
                    ],
                    IObject::FIELD_GUID                      => [
                        'type'       => IField::TYPE_GUID,
                        'columnName' => 'guid',
                        'accessor'   => 'getGuid',
                        'mutator'    => 'setGuid'
                    ],
                    IObject::FIELD_TYPE                      => [
                        'type'       => IField::TYPE_STRING,
                        'columnName' => 'type',
                        'accessor'   => 'getType',
                        'readOnly'   => true
                    ],
                    IObject::FIELD_VERSION                   => [
                        'type'         => IField::TYPE_VERSION,
                        'columnName'   => 'version',
                        'accessor'     => 'getVersion',
                        'mutator'      => 'setVersion',
                        'defaultValue' => 1
                    ],
                    IHierarchicObject::FIELD_PARENT          => [
                        'type'       => IField::TYPE_BELONGS_TO,
                        'columnName' => 'pid',
                        'accessor'   => 'getParent',
                        'target'     => 'system_hierarchy',
                        'readOnly'   => true
                    ],
                    IHierarchicObject::FIELD_MPATH           => [
                        'type'       => IField::TYPE_MPATH,
                        'columnName' => 'mpath',
                        'accessor'   => 'getMaterializedPath',
                        'readOnly'   => true
                    ],
                    IHierarchicObject::FIELD_SLUG            => [
                        'type'       => IField::TYPE_SLUG,
                        'columnName' => 'slug',
                        'accessor'   => 'getSlug',
                        'readOnly'   => true
                    ],
                    IHierarchicObject::FIELD_URI             => [
                        'type'       => IField::TYPE_URI,
                        'columnName' => 'uri',
                        'accessor'   => 'getURI',
                        'readOnly'   => true
                    ],
                    IHierarchicObject::FIELD_CHILD_COUNT     => [
                        'type'         => IField::TYPE_COUNTER,
                        'columnName'   => 'child_count',
                        'accessor'     => 'getChildCount',
                        'readOnly'     => true,
                        'defaultValue' => 0
                    ],
                    IHierarchicObject::FIELD_ORDER           => [
                        'type'       => IField::TYPE_ORDER,
                        'columnName' => 'order',
                        'accessor'   => 'getOrder',
                        'readOnly'   => true
                    ],
                    IHierarchicObject::FIELD_HIERARCHY_LEVEL => [
                        'type'       => IField::TYPE_LEVEL,
                        'columnName' => 'level',
                        'accessor'   => 'getLevel',
                        'readOnly'   => true
                    ]
                ],
                'types'      => [
                    'base'  => [
                        'fields' => [
                            IObject::FIELD_IDENTIFY,
                            IObject::FIELD_GUID,
                            IObject::FIELD_TYPE,
                            IObject::FIELD_VERSION,
                            IHierarchicObject::FIELD_PARENT,
                            IHierarchicObject::FIELD_MPATH,
                            IHierarchicObject::FIELD_SLUG,
                            IHierarchicObject::FIELD_URI,
                            IHierarchicObject::FIELD_CHILD_COUNT,
                            IHierarchicObject::FIELD_ORDER,
                            IHierarchicObject::FIELD_HIERARCHY_LEVEL
                        ]
                    ],
                    'type1' => [
                        'fields' => [
                            IObject::FIELD_IDENTIFY,
                            IObject::FIELD_GUID,
                            IObject::FIELD_TYPE,
                            IObject::FIELD_VERSION,
                            IHierarchicObject::FIELD_PARENT,
                            IHierarchicObject::FIELD_MPATH,
                            IHierarchicObject::FIELD_SLUG,
                            IHierarchicObject::FIELD_URI,
                            IHierarchicObject::FIELD_CHILD_COUNT,
                            IHierarchicObject::FIELD_ORDER,
                            IHierarchicObject::FIELD_HIERARCHY_LEVEL
                        ]
                    ]
                ]
            ];
        }

        $metadataFactory = new MetadataFactory($this->getDbCluster());
        $this->resolveOptionalDependencies($metadataFactory);
        $metadata = new Metadata('system_menu', $metadataConfig, $metadataFactory);
        $objectSetFactory = new ObjectSetFactory();
        $selectorFactory = new SelectorFactory($objectSetFactory);

        $collection = new SimpleHierarchicCollection('system_menu', $metadata, $selectorFactory);
        $this->resolveOptionalDependencies($collection);

        return $collection;
    }

    public function testFields()
    {

        $collection1 = $this->getCollection();
        $collection2 = $this->getCollection(
            [
                'dataSource' => ['sourceName' => 'source'],
                'fields'     => [],
                'groups'     => [],
                'types'      => ['base' => []]
            ]
        );

        $field1 = $collection1->getHierarchyChildCountField();
        $this->assertInstanceOf(
            'umi\orm\metadata\field\IField',
            $field1,
            'Ожидается, что у коллекции 1 есть поле для хранения количества детей'
        );
        $this->assertEquals('childCount', $field1->getName(), 'Неверное имя поля количества детей');
        $e = null;
        try {
            $collection2->getHierarchyChildCountField();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при попытке получить у коллекции поле для хранения количества детей, когда такого нет'
        );

        $field2 = $collection1->getSlugField();
        $this->assertInstanceOf(
            'umi\orm\metadata\field\special\SlugField',
            $field2,
            'Ожидается, что у коллекции 1 есть поле для хранения последней части ЧПУ'
        );
        $this->assertEquals('slug', $field2->getName(), 'Неверное имя поля последней части ЧПУ');
        $e = null;
        try {
            $collection2->getSlugField();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при попытке получить у коллекции поле для хранения последней части ЧПУ,'
            . ' когда такого нет'
        );

        $field3 = $collection1->getURIField();
        $this->assertInstanceOf(
            'umi\orm\metadata\field\IField',
            $field3,
            'Ожидается, что у коллекции 1 есть поле для хранения URI'
        );
        $this->assertEquals('uri', $field3->getName(), 'Неверное имя поля URI');
        $e = null;
        try {
            $collection2->getURIField();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при попытке получить у коллекции поле для хранения URI, когда такого нет'
        );

        $field4 = $collection1->getHierarchyLevelField();
        $this->assertInstanceOf(
            'umi\orm\metadata\field\IField',
            $field4,
            'Ожидается, что у коллекции 1 есть поле для хранения уровня вложенности'
        );
        $this->assertEquals('level', $field4->getName(), 'Неверное имя поля level');
        $e = null;
        try {
            $collection2->getHierarchyLevelField();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при попытке получить у коллекции поле для хранения уровня вложенности, '
            . 'когда такого нет'
        );

        $field5 = $collection1->getParentField();
        $this->assertInstanceOf(
            'umi\orm\metadata\field\relation\BelongsToRelationField',
            $field5,
            'Ожидается, что у коллекции 1 есть поле для хранения родителя'
        );
        $this->assertEquals('parent', $field5->getName(), 'Неверное имя поля родителя');
        $e = null;
        try {
            $collection2->getParentField();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при попытке получить у коллекции поле для хранения родителя, когда такого нет'
        );

        $field6 = $collection1->getHierarchyOrderField();
        $this->assertInstanceOf(
            'umi\orm\metadata\field\IField',
            $field6,
            'Ожидается, что у коллекции 1 есть поле для хранения иерархического порядка'
        );
        $this->assertEquals('order', $field6->getName(), 'Неверное имя поля порядка в иерархии');
        $e = null;
        try {
            $collection2->getHierarchyOrderField();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при попытке получить у коллекции поле для хранения иерархического порядка,'
            . ' когда такого нет'
        );

        $field7 = $collection1->getMPathField();
        $this->assertInstanceOf(
            'umi\orm\metadata\field\IField',
            $field7,
            'Ожидается, что у коллекции 1 есть поле для хранения materialized path'
        );
        $this->assertEquals('mpath', $field7->getName(), 'Неверное имя поля предков');
        $e = null;
        try {
            $collection2->getMPathField();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при попытке получить у коллекции поле для хранения materialized path,'
            . ' когда такого нет'
        );

    }
}
