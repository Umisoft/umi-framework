<?php

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
        ->addColumn('pid', Type::INTEGER)
        ->setNotnull(false);
    $tableScheme
        ->addColumn('mpath', Type::TEXT)
        ->setNotnull(false);
    $tableScheme
        ->addColumn('uri', Type::TEXT)
        ->setNotnull(false);
    $tableScheme
        ->addColumn('slug', Type::STRING)
        ->setNotnull(false);
    $tableScheme
        ->addColumn('level', Type::INTEGER)
        ->setUnsigned(true)
        ->setNotnull(false);
    $tableScheme
        ->addColumn('order', Type::INTEGER)
        ->setUnsigned(true)
        ->setNotnull(false);
    $tableScheme
        ->addColumn(
            'child_count',
            Type::INTEGER
        )
        ->setUnsigned(true)
        ->setDefault(0);

    $tableScheme
        ->addColumn('title', Type::STRING)
        ->setNotnull(false);
    $tableScheme
        ->addColumn('title_en', Type::STRING)
        ->setNotnull(false);

    $tableScheme->setPrimaryKey(['id']);
    $tableScheme->addUniqueIndex(['guid'], 'hierarchy_guid');
    $tableScheme->addIndex(['pid'], 'hierarchy_parent');
    $tableScheme->addUniqueIndex(['pid', 'slug'], 'hierarchy_pid_slug');

    $tableScheme->addUniqueIndex(['mpath'], 'hierarchy_mpath');
    $tableScheme->addIndex(['uri'], 'hierarchy_uri');
    $tableScheme->addIndex(['type'], 'hierarchy_type');

    $tableScheme->addForeignKeyConstraint(
        $tableScheme->getName(),
        ['pid'],
        ['id'],
        ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE'],
        'FK_hierarchy_parent'
    );
    return $schemaManager->getDatabasePlatform()->getCreateTableSQL(
        $tableScheme,
        AbstractPlatform::CREATE_INDEXES | AbstractPlatform::CREATE_FOREIGNKEYS
    );
};
