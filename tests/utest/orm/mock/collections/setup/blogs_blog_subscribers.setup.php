<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use umi\orm\metadata\ICollectionDataSource;

return function (ICollectionDataSource $dataSource) {

    $masterServer = $dataSource->getMasterServer();
    $schemaManager = $masterServer
        ->getConnection()
        ->getSchemaManager();
    $tableScheme = new Table($dataSource->getSourceName());

    $tableScheme->addOption('engine', 'InnoDB');

    $tableScheme
        ->addColumn('id', Type::INTEGER)
        ->setAutoincrement(true);
    $tableScheme
        ->addColumn('guid', Type::GUID)
        ->setNotnull(false);
    $tableScheme
        ->addColumn('type', Type::TEXT)
        ->setNotnull(false);
    $tableScheme
        ->addColumn('version', Type::INTEGER)
        ->setUnsigned(true)
        ->setDefault(1);

    $tableScheme
        ->addColumn('user_id', Type::INTEGER)
        ->setNotnull(false);
    $tableScheme
        ->addColumn('blog_id', Type::INTEGER)
        ->setNotnull(false);

    $tableScheme->setPrimaryKey(['id']);
    $tableScheme->addUniqueIndex(['guid'], 'blog_subscriber_guid');
    $tableScheme->addIndex(['blog_id'], 'subscriber_blog_id');
    $tableScheme->addIndex(['user_id'], 'subscriber_user_id');
    $tableScheme->addIndex(['type'], 'subscribers_type');

    /** @noinspection PhpParamsInspection */
    $tableScheme->addForeignKeyConstraint(
        'umi_mock_users',
        ['user_id'],
        ['id'],
        ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'],
        'FK_user'
    );

    /** @noinspection PhpParamsInspection */
    $tableScheme->addForeignKeyConstraint(
        'umi_mock_blogs',
        ['blog_id'],
        ['id'],
        ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'],
        'FK_blog'
    );

    return $schemaManager->getDatabasePlatform()->getCreateTableSQL(
        $tableScheme,
        AbstractPlatform::CREATE_INDEXES | AbstractPlatform::CREATE_FOREIGNKEYS
    );

};
