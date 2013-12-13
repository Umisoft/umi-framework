<?php

use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use umi\orm\metadata\ICollectionDataSource;

return function (ICollectionDataSource $dataSource) {

    $tableScheme = new Table($dataSource->getSourceName());

    $tableScheme->addOption('engine', 'InnoDB');

    $tableScheme->addColumn('id', Type::INTEGER, ['autoincrement' => true]);
    $tableScheme->addColumn('guid', Type::STRING, ['notnull' => false]);
    $tableScheme->addColumn('type', Type::TEXT, ['notnull' => false]);
    $tableScheme->addColumn(
        'version',
        Type::INTEGER,
        ['unsigned' => true, 'default' => 1, 'notnull' => false]
    );

    $tableScheme->addColumn('login', Type::STRING, ['notnull' => false]);

    $tableScheme->setPrimaryKey(['id']);

    return $dataSource
        ->getMasterServer()
        ->getConnection()
        ->getDatabasePlatform()
        ->getCreateTableSQL($tableScheme);
};
