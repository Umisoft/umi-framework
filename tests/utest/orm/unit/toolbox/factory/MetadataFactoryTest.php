<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\toolbox\factory;

use umi\orm\metadata\field\IField;
use umi\orm\toolbox\factory\MetadataFactory;
use utest\TestCase;

/**
 * Тест фабрики метаданных
 */
class MetadataFactoryTest extends TestCase
{

    /**
     * @var MetadataFactory $metadataFactory
     */
    protected $metadataFactory;

    protected $config = [];

    protected function setUpFixtures()
    {
        $this->metadataFactory = new MetadataFactory($this->getDbCluster());
        $this->resolveOptionalDependencies($this->metadataFactory);

        $this->config = [
            'dataSource' => ['sourceName' => 'source'],
            'fields'     => [
                'id'     => ['type' => IField::TYPE_IDENTIFY],
                'name'   => [
                    'type'          => IField::TYPE_STRING,
                    'localizations' => ['ru' => ['columnName' => 'name_ru'], 'en' => ['columnName' => 'name_en']]
                ],
                'parent' => ['type' => IField::TYPE_BELONGS_TO, 'target' => 'system_hierarchy']
            ],
            'types'      => [
                'base' => [
                    'locked'      => 0,
                    'objectClass' => 'User',
                    'fields'      => ['id'],
                ]
            ]
        ];
    }

    public function testMetadataFactory()
    {

        $metadata = $this->metadataFactory->create('testCollection', $this->config);
        $this->assertInstanceOf(
            'umi\orm\metadata\IMetadata',
            $metadata,
            'Ожидается, что IMetadataFactory::create() вкрнет IMetadata'
        );

        $this->assertInstanceOf(
            'umi\orm\metadata\IObjectType',
            $this->metadataFactory->createObjectType('base', $this->config['types']['base'], $metadata)
        );

        $this->assertInstanceOf(
            'umi\orm\metadata\field\IField',
            $this->metadataFactory->createField('id', $this->config['fields']['id'])
        );
        $this->assertInstanceOf(
            'umi\orm\metadata\field\ILocalizableField',
            $this->metadataFactory->createField('name', $this->config['fields']['name'])
        );
        $this->assertInstanceOf(
            'umi\orm\metadata\field\relation\BelongsToRelationField',
            $this->metadataFactory->createField('parent', $this->config['fields']['parent'])
        );

        $e = null;
        try {
            $this->metadataFactory->createField('id', []);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение при создании поля без указания его типа'
        );

        $e = null;
        try {
            $this->metadataFactory->createField('id', ['type' => 'nonExistentType']);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение, если фабрика не знает заданного типа поля'
        );
    }
}
