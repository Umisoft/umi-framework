<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\metadata;

use umi\config\entity\Config;
use umi\orm\metadata\field\IField;
use umi\orm\metadata\IMetadata;
use umi\orm\metadata\IObjectType;
use umi\orm\metadata\Metadata;
use umi\orm\toolbox\factory\MetadataFactory;
use utest\TestCase;

class MetadataTest extends TestCase
{

    protected $metadataFactory;
    protected $config = [];

    protected function setUpFixtures()
    {
        $this->config = [
            'dataSource' => [
                'sourceName' => 'source'
            ],
            'fields'     => [
                'field1' => [
                    'type'         => 'manyToManyRelation',
                    'bridge'       => 'emarket.goodsImages',
                    'target'       => 'fs.images',
                    'targetField'  => 'image',
                    'relatedField' => 'good'
                ],
                'field2' => [
                    'type' => IField::TYPE_STRING
                ],
                'field3' => [
                    'type' => IField::TYPE_STRING
                ]
            ],
            'types'      => [
                'base'        => [
                    'fields' => ['field1']
                ],
                'type1'       => [
                    'fields' => ['field1']
                ],
                'type1.type2' => [
                    'fields' => ['field1', 'field3']
                ],
                'type1.type3' => [
                    'fields' => ['field1', 'field3']
                ],
                'type4'       => [
                    'fields' => ['field1', 'field2']
                ]
            ]
        ];
        $this->metadataFactory = new MetadataFactory($this->getDbCluster());
        $this->resolveOptionalDependencies($this->metadataFactory);
    }

    public function testArrayMetadata()
    {
        $metadata = new Metadata('testCollection', $this->config, $this->metadataFactory);

        $this->collectionDataSourceTest($metadata);
        $this->fieldsTest($metadata);
        $this->relatedFieldsTest($metadata);
        $this->typesTest($metadata);
        $this->findTypesTest($metadata);
    }

    public function testConfigMetadata()
    {
        $config = new Config($this->config);
        $metadata = new Metadata('testCollection', $config, $this->metadataFactory);

        $this->collectionDataSourceTest($metadata);
        $this->fieldsTest($metadata);
        $this->relatedFieldsTest($metadata);
        $this->typesTest($metadata);
        $this->findTypesTest($metadata);
    }

    public function testWrongMetadata()
    {
        $config = 'wrongConfig';

        $e = null;
        try {
            new Metadata('testCollection', $config, $this->metadataFactory);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\InvalidArgumentException',
            $e,
            'Ожидается исключение, если конфигурация метаданных задана не массивом'
        );

        $config = [];
        $e = null;
        try {
            new Metadata('testCollection', $config, $this->metadataFactory);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение, если в конфигурации метаданных отсутствует информация о таблице'
        );

        $config['dataSource'] = [];
        $e = null;
        try {
            new Metadata('testCollection', $config, $this->metadataFactory);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение, если в конфигурации метаданных отсутствует информация о типах'
        );
        $config['types'] = [];
        $e = null;
        try {
            new Metadata('testCollection', $config, $this->metadataFactory);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение, если в конфигурации метаданных отсутствует информация о полях'
        );
    }

    protected function collectionDataSourceTest(IMetadata $metadata)
    {
        $this->assertInstanceOf('umi\orm\metadata\ICollectionDataSource', $metadata->getCollectionDataSource());
    }

    protected function fieldsTest(IMetadata $metadata)
    {
        $this->assertEquals(
            ['field1', 'field2', 'field3'],
            $metadata->getFieldsList(),
            'Неверный список полей метаданных'
        );
        $this->assertTrue($metadata->getFieldExists('field1'), 'Ожидается, что поле field1 есть в метаданных.');
        $this->assertFalse(
            $metadata->getFieldExists('nonExistentField'),
            'Ожидается, что поле nonExistentField отсутствует в метаданных.'
        );

        $this->assertInstanceOf(
            'umi\orm\metadata\field\IField',
            $metadata->getField('field1'),
            'Ожидается, что при запросе поля будет возвращен IField'
        );
        $e = null;
        try {
            $metadata->getField('nonExistentField');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при запросе несуществующего поля'
        );
    }

    protected function relatedFieldsTest(IMetadata $metadata)
    {

        $this->assertEquals(
            'field1',
            $metadata->getFieldByRelation('good', 'emarket.goodsImages')
                ->getName(),
            'Неверное имя поля, использующего поле good в коллекции emarket.goodsImages'
        );

        $e = null;
        try {
            $metadata->getFieldByRelation('good', 'emarket.images');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при попытке найти несуществующее связанное поле'
        );

        $this->assertEquals(
            'field1',
            $metadata->getFieldByTarget('image', 'fs.images')
                ->getName(),
            'Неверное имя поля, которое имеет доступ к коллекции fs.images через поле image'
        );

        $e = null;
        try {
            $metadata->getFieldByTarget('image', 'fileSystem.images');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при попытке найти несуществующее связанное поле'
        );
    }

    protected function typesTest(IMetadata $metadata)
    {
        $this->assertEquals(
            ['base', 'type1', 'type1.type2', 'type1.type3', 'type4'],
            $metadata->getTypesList(),
            'Неверный список типов метаданных'
        );
        $this->assertTrue($metadata->getTypeExists('type1'), 'Ожидается, что тип type1 есть в метаданных.');
        $this->assertFalse(
            $metadata->getTypeExists('nonExistentType'),
            'Ожидается, что тип nonExistentType отсутствует в метаданных.'
        );

        $this->assertInstanceOf(
            'umi\orm\metadata\IObjectType',
            $metadata->getType('type1'),
            'Ожидается, что при запросе типа будет возвращен IObjectType'
        );
        $e = null;
        try {
            $metadata->getType('nonExistentType');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при запросе несуществующего типа'
        );

        $baseType = $metadata->getBaseType();
        $this->assertEquals(IObjectType::BASE, $baseType->getName());

        $this->assertEquals(['type1', 'type4'], $metadata->getChildTypesList());
        $this->assertEquals(['type1.type2', 'type1.type3'], $metadata->getChildTypesList('type1'));

        $this->assertEquals(['type1', 'type1.type2', 'type1.type3', 'type4'], $metadata->getDescendantTypesList());
        $this->assertEquals(['type1', 'type4'], $metadata->getDescendantTypesList(IObjectType::BASE, 1));
        $this->assertEquals(
            ['type1', 'type1.type2', 'type1.type3', 'type4'],
            $metadata->getDescendantTypesList(IObjectType::BASE, 10)
        );
        $this->assertEquals(['type1.type2', 'type1.type3'], $metadata->getDescendantTypesList('type1', 1));

        $this->assertInstanceOf(
            'umi\orm\metadata\IObjectType',
            $metadata->getParentType('type1'),
            'Ожидается, что IMetadata::getParentType() вернет IObjectType'
        );
        $this->assertInstanceOf(
            'umi\orm\metadata\IObjectType',
            $metadata->getParentType('type1.type2'),
            'Ожидается, что IMetadata::getParentType() вернет IObjectType'
        );
        $this->assertNull(
            $metadata->getParentType(IObjectType::BASE),
            'Ожидается, что IMetadata::getParentType() вернет null для базового типа'
        );

        $e = null;
        try {
            $metadata->getParentType('nonExistentType');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при запросе родителя несуществующего типа'
        );

        $e = null;
        try {
            $metadata->getDescendantTypesList('nonExistentType', 1);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при запросе дочерних типов у несуществующего типа'
        );
    }

    protected function findTypesTest(IMetadata $metadata)
    {

        $this->assertEquals(
            ['type1', 'type1.type2', 'type1.type3', 'type4', 'base'],
            $metadata->getTypesByFields(['field1']),
            'Ожидаются 5 типов, у которых есть поле field1'
        );
        $this->assertEquals(
            ['type1.type2', 'type1.type3', 'type1'],
            $metadata->getTypesByFields(['field1'], 'type1'),
            'Ожидаются 3 типа, начиная с type1, у которых есть поле field1'
        );
        $this->assertEquals(
            ['type1.type2', 'type1.type3'],
            $metadata->getTypesByFields(['field3']),
            'Ожидаются 2 типа, у которых есть поле field3'
        );
        $this->assertEquals(
            ['type4'],
            $metadata->getTypesByFields(['field2', 'field1']),
            'Ожидается 1 тип, у которых есть поля field2 и field1'
        );
        $this->assertEquals(
            ['type1.type2', 'type1.type3'],
            $metadata->getTypesByFields(['field1', 'field3']),
            'Ожидается 2 типа, у которых есть поля field2 и field3'
        );
    }
}
