<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\metadata;

use umi\orm\metadata\field\IField;
use umi\orm\metadata\IMetadata;
use umi\orm\metadata\IObjectType;
use umi\orm\metadata\Metadata;
use umi\orm\metadata\ObjectType;
use umi\orm\toolbox\factory\MetadataFactory;
use utest\orm\ORMTestCase;

class ObjectTypeTest extends ORMTestCase
{

    /**
     * @var array $config
     */
    protected $config = [];
    /**
     * @var IMetadata $metadata
     */
    protected $metadata;
    /**
     * @var IObjectType $baseType
     */
    protected $baseType;
    /**
     * @var IObjectType $subtype1
     */
    protected $subtype1;
    /**
     * @var IObjectType $subtype2
     */
    protected $subtype2;

    protected function setUpFixtures()
    {
        $this->config = [
            'dataSource' => ['sourceName' => 'source'],
            'fields'     => [
                'id'         => ['type' => IField::TYPE_IDENTIFY],
                'guid'       => ['type' => IField::TYPE_GUID],
                'type'       => ['type' => IField::TYPE_STRING],
                'parent'     => ['type' => IField::TYPE_BELONGS_TO, 'target' => 'system_hierarchy'],
                'mpath'      => ['type' => IField::TYPE_STRING],
                'level'      => ['type' => IField::TYPE_INTEGER],
                'order'      => ['type' => IField::TYPE_INTEGER],
                'version'    => ['type' => IField::TYPE_INTEGER],
                'childCount' => ['type' => IField::TYPE_INTEGER],
                'slug'       => ['type' => IField::TYPE_SLUG],
                'uri'        => ['type' => IField::TYPE_URI],
                'test1'      => [
                    'type'          => IField::TYPE_STRING,
                    'localizations' => ['ru' => ['columnName' => 'test1'], 'en' => ['columnName' => 'test1_en']]
                ],
                'test2'      => [
                    'type'          => IField::TYPE_STRING,
                    'localizations' => ['ru' => ['columnName' => 'test2'], 'en' => ['columnName' => 'test2_en']]
                ]
            ],
            'types'      => [
                'base'     => [
                    'locked' => 1
                ],
                'subtype1' => [
                    'locked'      => 0,
                    'objectClass' => 'User',
                    'fields'      => [
                        'id',
                        'guid',
                        'type',
                        'parent',
                        'mpath',
                        'level',
                        'order',
                        'version',
                        'childCount',
                        'slug',
                        'uri'
                    ]
                ],
                'subtype2' => [
                    'fields' => ['id', 'guid', 'test1', 'test2']
                ]
            ]
        ];

        $metadataFactory = new MetadataFactory($this->getDbCluster());
        $this->resolveOptionalDependencies($metadataFactory);

        $this->metadata = new Metadata('testCollection', $this->config, $metadataFactory);

        $this->baseType = new ObjectType('base', $this->config['types']['base'], $this->metadata);
        $this->subtype1 = new ObjectType('subtype1', $this->config['types']['subtype1'], $this->metadata);
        $this->subtype2 = new ObjectType('subtype2', $this->config['types']['subtype2'], $this->metadata);
    }

    public function testWrongTypeConfig()
    {

        $e = null;
        try {
            new ObjectType('wrongType', ['fields' => 'wrongFieldsConfig'], $this->metadata);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение при попытке создать тип с неверной конфигурацией'
        );

        $e = null;
        try {
            new ObjectType('wrongType', ['fields' => 'wrongFieldGroupConfig'], $this->metadata);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение при попытке создать тип с неверной конфигурацией'
        );
    }

    public function testTypeProperties()
    {
        $this->assertEquals('subtype1', $this->subtype1->getName(), 'Неверное имя типа');
        $this->assertSame(
            null,
            $this->baseType->getObjectClass(),
            'Ожидается, что у базового типа не задано имя класса'
        );
        $this->assertEquals(
            'User',
            $this->subtype1->getObjectClass(),
            'Ожидается, что у типа subtype1 имя класса "User"'
        );
    }

    public function testTypeContent()
    {

        $this->assertTrue(
            $this->subtype1->getFieldExists('id'),
            'Ожидается, что поле id присутствует у типа subtype1'
        );
        $this->assertFalse(
            $this->baseType->getFieldExists('guid'),
            'Ожидается, что поле guid отсутствует у базового типа'
        );
        $this->assertFalse(
            $this->baseType->getFieldExists('nonExistentField'),
            'Ожидается, что поле nonExistentField отсутствует у базового типа'
        );

        $this->assertInstanceOf(
            'umi\orm\metadata\field\IField',
            $this->subtype1->getField('id'),
            'Ожидается, что IObjectType::getField() вернет IField'
        );
        $e = null;
        try {
            $this->baseType->getField('guid');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при запросе у типа несуществующего поля'
        );

        $this->assertEquals(
            ['id', 'guid', 'type', 'parent', 'mpath', 'level', 'order', 'version', 'childCount', 'slug', 'uri'],
            array_keys($this->subtype1->getFields()),
            'Ожидается, что у типа subtype1 11 полей'
        );

        $this->assertEquals(
            ['test1', 'test2'],
            array_keys($this->subtype2->getLocalizedFields()),
            'Ожидается, что у типа 2 локализованных поля'
        );
    }
}
