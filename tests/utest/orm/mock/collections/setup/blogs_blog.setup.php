<?php

use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use umi\orm\metadata\ICollectionDataSource;

return function (ICollectionDataSource $dataSource) {

    $masterServer = $dataSource->getMasterServer();
    $schemaManager = $masterServer
        ->getConnection()
        ->getSchemaManager();
    $table = new Table($dataSource->getSourceName());

    $table->addOption('engine', 'InnoDB');

    $table
        ->addColumn('id', Type::INTEGER)
        ->setAutoincrement(true);
    $table
        ->addColumn('guid', Type::GUID)
        ->setNotnull(false);
    $table
        ->addColumn('type', Type::TEXT)
        ->setNotnull(false);
    $table
        ->addColumn('version', Type::INTEGER)
        ->setUnsigned(true)
        ->setDefault(1);

    $table
        ->addColumn('pid', Type::INTEGER)
        ->setNotnull(false);
    $table
        ->addColumn('mpath', Type::TEXT)
        ->setNotnull(false);
    $table
        ->addColumn('uri', Type::TEXT)
        ->setNotnull(false);
    $table
        ->addColumn('slug', Type::STRING)
        ->setNotnull(false);
    $table
        ->addColumn('level', Type::INTEGER)
        ->setUnsigned(true)
        ->setNotnull(false);
    $table
        ->addColumn('order', Type::INTEGER)
        ->setUnsigned(true)
        ->setNotnull(false);
    $table
        ->addColumn('child_count', Type::INTEGER)
        ->setUnsigned(true)
        ->setDefault(0);

    $table
        ->addColumn('publish_time', Type::DATE)
        ->setNotnull(false);
    $table
        ->addColumn('title', Type::STRING)
        ->setNotnull(false);
    $table
        ->addColumn('title_en', Type::STRING)
        ->setNotnull(false);
    $table
        ->addColumn('title_gb', Type::STRING)
        ->setNotnull(false);
    $table
        ->addColumn('title_ua', Type::STRING)
        ->setNotnull(false);
    $table
        ->addColumn('owner_id', Type::INTEGER)
        ->setNotnull(false);

    $table->setPrimaryKey(['id']);
    $table->addUniqueIndex(['guid'], 'blog_guid');
    $table->addIndex(['pid'], 'blog_parent');
    $table->addUniqueIndex(['pid', 'slug'], 'blog_pid_slug');
//    $table->addUniqueIndex(['mpath'], 'hierarchy_mpath', [], ['mpath' => ['size' => 64]]);
//    $table->addIndex(['uri'], 'hierarchy_uri', [], ['uri' => ['size' => 64]]);
//    $table->addIndex(['type'], 'hierarchy_type', [], ['type' => ['size' => 64]]);

    $table->addIndex(['owner_id'], 'blog_owner');

    $fTableHierarchy = $schemaManager->listTableDetails('umi_mock_hierarchy');
    $table->addForeignKeyConstraint(
        $fTableHierarchy,
        ['pid'],
        ['id'],
        ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'],
        'FK_blog_parent'
    );

    $fTableUsers = $schemaManager->listTableDetails('umi_mock_users');
    $table->addForeignKeyConstraint(
        $fTableUsers,
        ['owner_id'],
        ['id'],
        ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'],
        'FK_blog_owner'
    );

    $schemaManager->createTable($table);

};
