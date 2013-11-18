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
use umi\orm\metadata\MetadataManager;
use umi\orm\toolbox\factory\MetadataFactory;
use utest\orm\ORMTestCase;

/**
 * Тест менеджера метаданных
 */
class MetadataManagerTest extends ORMTestCase
{

    protected $metadataConfig = [
        'users_user' => [
            'dataSource' => ['sourceName' => 'source'],
            'fields'     => [
                'id'      => ['type' => IField::TYPE_IDENTIFY],
                'guid'    => ['type' => IField::TYPE_GUID],
                'type'    => ['type' => IField::TYPE_STRING],
                'version' => ['type' => IField::TYPE_INTEGER]
            ],
            'groups'     => [],
            'types'      => ['base' => [], 'type1' => []]
        ]
    ];

    public function testTraversableConfigMetadata()
    {
        $metadataFactory = new MetadataFactory($this->getDbCluster());
        $this->resolveOptionalDependencies($metadataFactory);
        $metadataManager = new MetadataManager($metadataFactory, new Config($this->metadataConfig));
        $this->resolveOptionalDependencies($metadataManager);

        $this->assertInstanceOf(
            'umi\orm\metadata\IMetadata',
            $metadataManager->getMetadata('users_user'),
            'Ожидается, что IObjectManager::getMetadata() вернет IMetadata'
        );
    }

    public function testArrayConfigMetadata()
    {

        $metadataFactory = new MetadataFactory($this->getDbCluster());
        $this->resolveOptionalDependencies($metadataFactory);
        $metadataManager = new MetadataManager($metadataFactory, $this->metadataConfig);
        $this->resolveOptionalDependencies($metadataManager);

        $metadata = $metadataManager->getMetadata('users_user');
        $this->assertInstanceOf(
            'umi\orm\metadata\IMetadata',
            $metadata,
            'Ожидается, что IObjectManager::getMetadata() вернет IMetadata'
        );
        $this->assertTrue(
            $metadata === $metadataManager->getMetadata('users_user'),
            'Ожидается, что для метаданных создается одна сущность'
        );

        $e = null;
        try {
            $metadataManager->getMetadata('users_user_1');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при попытке получить несуществующую коллекцию'
        );
        $this->assertEquals(
            'Cannot get metadata. Collection "users_user_1" does not exist.',
            $e->getMessage(),
            'Произошло неожидаемое исключение'
        );
    }
}
