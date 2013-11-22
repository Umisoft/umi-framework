<?php

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

    $ftUsers = $schemaManager->listTableDetails('umi_mock_users');
    $ftBlogs = $schemaManager->listTableDetails('umi_mock_blogs');

    $tableScheme->addForeignKeyConstraint(
        $ftUsers,
        ['user_id'],
        ['id'],
        ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'],
        'FK_user'
    );
    $tableScheme->addForeignKeyConstraint(
        $ftBlogs,
        ['blog_id'],
        ['id'],
        ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'],
        'FK_blog'
    );
    $schemaManager->createTable($tableScheme);

};
