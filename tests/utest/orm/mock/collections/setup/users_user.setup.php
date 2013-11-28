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
        ->addColumn('login', Type::STRING)
        ->setNotnull(false);
    $tableScheme
        ->addColumn('email', Type::STRING)
        ->setNotnull(false);
    $tableScheme
        ->addColumn('password', Type::STRING)
        ->setNotnull(false);
    $tableScheme
        ->addColumn('is_active', Type::BOOLEAN)
        ->setDefault(1);

    $tableScheme
        ->addColumn(
            'rating',
            Type::FLOAT
        )
        ->setUnsigned(true)
        ->setNotnull(false)
        ->setDefault(0);

    $tableScheme
        ->addColumn('height', Type::INTEGER)
        ->setUnsigned(true)
        ->setNotnull(false);

    $tableScheme
        ->addColumn('group_id', Type::INTEGER)
        ->setNotnull(false);

    $tableScheme
        ->addColumn('supervisor_field', Type::INTEGER)
        ->setUnsigned(true)
        ->setNotnull(false);
    $tableScheme
        ->addColumn('guest_field', Type::INTEGER)
        ->setUnsigned(true)
        ->setNotnull(false);

    $tableScheme->setPrimaryKey(['id']);
    $tableScheme->addUniqueIndex(['guid'], 'user_guid');
    $tableScheme->addIndex(['group_id'], 'user_group');

    $tableScheme->addForeignKeyConstraint(
        'umi_mock_groups',
        ['group_id'],
        ['id'],
        ['onUpdate' => 'CASCADE', 'onDelete' => 'SET NULL'],
        'FK_user_group'
    );

    return $schemaManager->getDatabasePlatform()->getCreateTableSQL(
        $tableScheme,
        AbstractPlatform::CREATE_INDEXES | AbstractPlatform::CREATE_FOREIGNKEYS
    );
};
