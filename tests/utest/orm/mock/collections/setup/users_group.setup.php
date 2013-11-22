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
        ->addColumn('guid', Type::STRING)
        ->setNotnull(false);
    $tableScheme
        ->addColumn('type', Type::TEXT)
        ->setNotnull(false);

    $tableScheme
        ->addColumn(
            'version',
            Type::INTEGER
        )
        ->setUnsigned(true)
        ->setDefault(1);

    $tableScheme
        ->addColumn('name', Type::STRING)
        ->setNotnull(false);
    $tableScheme
        ->addColumn('title', Type::STRING)
        ->setNotnull(false);
    $tableScheme
        ->addColumn('title_en', Type::STRING)
        ->setNotnull(false);
    $tableScheme
        ->addColumn('title_gb', Type::STRING)
        ->setNotnull(false);
    $tableScheme
        ->addColumn('title_ua', Type::STRING)
        ->setNotnull(false);

    $tableScheme->setPrimaryKey(['id']);
    $tableScheme->addUniqueIndex(['guid'], 'group_guid');
    $schemaManager->createTable($tableScheme);
};
