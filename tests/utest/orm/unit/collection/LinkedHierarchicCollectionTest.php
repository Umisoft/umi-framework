<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\collection;

use umi\orm\collection\CommonHierarchy;
use umi\orm\collection\LinkedHierarchicCollection;
use umi\orm\metadata\field\IField;
use umi\orm\metadata\Metadata;
use umi\orm\object\IHierarchicObject;
use umi\orm\object\IObject;
use umi\orm\toolbox\factory\MetadataFactory;
use umi\orm\toolbox\factory\ObjectSetFactory;
use umi\orm\toolbox\factory\SelectorFactory;
use utest\TestCase;

class LinkedHierarchicCollectionTest extends TestCase
{

    public function testLinkedCollection()
    {

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

        $metadataFactory = new MetadataFactory($this->getDbCluster());
        $this->resolveOptionalDependencies($metadataFactory);

        $metadata = new Metadata('mock', $metadataConfig, $metadataFactory);
        $objectSetFactory = new ObjectSetFactory();

        $selectorFactory = new SelectorFactory($objectSetFactory);
        $this->resolveOptionalDependencies($selectorFactory);

        $linkedCollection = new LinkedHierarchicCollection('mock', $metadata, $selectorFactory);
        $this->resolveOptionalDependencies($linkedCollection);

        $commonHierarchy = new CommonHierarchy('hierarchy', $metadata, $selectorFactory);

        $e = null;
        try {
            $linkedCollection->getCommonHierarchy();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\RuntimeException',
            $e,
            'Ожидается исключение при попытке получить у связанной иерархической коллекции иерархию, если она не была установлена'
        );

        $this->assertInstanceOf(
            'umi\orm\collection\ILinkedHierarchicCollection',
            $linkedCollection->setCommonHierarchy($commonHierarchy),
            'Ожидается, что ILinkedHierarchicCollection::setCommonHierarchy() вернет себя'
        );
        $this->assertTrue(
            $commonHierarchy === $linkedCollection->getCommonHierarchy(),
            'Ожидается, что у связанной иерархической коллекции можно получить установленную иерархию'
        );

        $newCommonHierarchy = new CommonHierarchy('hierarchy', $metadata, $selectorFactory);
        $linkedCollection->setCommonHierarchy($newCommonHierarchy);
        $this->assertTrue(
            $commonHierarchy === $linkedCollection->getCommonHierarchy(),
            'Ожидается, что попытка сменить иерархию у связанной иерархической коллекции ни к цему не приведет'
        );

        /**
         * @var IHierarchicObject $mockObject
         */
        $mockObject = $this->getMock('umi\orm\object\HierarchicObject', ['getCollection'], [], '', false);
        $mockObject->expects($this->any())
            ->method('getCollection')
            ->will(
                $this->returnCallback(
                    function () {
                        return new \stdClass();
                    }
                )
            );

        $e = null;
        try {
            $linkedCollection->persistNewObject($mockObject);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NotAllowedOperationException',
            $e,
            'Ожидается, что связанная иерархическая коллекция не может сохранить новый не принадлежащий ей объект'
        );

        $e = null;
        try {
            $linkedCollection->persistModifiedObject($mockObject);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NotAllowedOperationException',
            $e,
            'Ожидается, что связанная иерархическая коллекция не может сохранить измененный не принадлежащий ей объект'
        );

        $e = null;
        try {
            $linkedCollection->persistDeletedObject($mockObject);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NotAllowedOperationException',
            $e,
            'Ожидается, что связанная иерархическая коллекция не может сохранить удаленный не принадлежащий ей объект'
        );

        $e = null;
        try {
            $linkedCollection->getMaxOrder($mockObject);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NotAllowedOperationException',
            $e,
            'Ожидается, что связанная иерархическая коллекция не может определить максимальный порядок среди детей у не принадлежащего ей объекта'
        );
    }
}
