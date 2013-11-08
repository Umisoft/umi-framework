<?php

use umi\dbal\driver\IColumnScheme;
use umi\orm\metadata\ICollectionDataSource;

return function (ICollectionDataSource $dataSource) {

    $masterServer = $dataSource->getMasterServer();
    $tableScheme = $masterServer->getDbDriver()
        ->addTable($dataSource->getSourceName());

    $tableScheme->setEngine('InnoDB');

    $tableScheme->addColumn('id', IColumnScheme::TYPE_SERIAL);
    $tableScheme->addColumn('guid', IColumnScheme::TYPE_VARCHAR);
    $tableScheme->addColumn('type', IColumnScheme::TYPE_TEXT);
    $tableScheme->addColumn(
        'version',
        IColumnScheme::TYPE_INT,
        [IColumnScheme::OPTION_UNSIGNED => true, IColumnScheme::OPTION_DEFAULT_VALUE => 1]
    );

    $tableScheme->addColumn('login', IColumnScheme::TYPE_VARCHAR);
    $tableScheme->addColumn('email', IColumnScheme::TYPE_VARCHAR);
    $tableScheme->addColumn('password', IColumnScheme::TYPE_VARCHAR);
    $tableScheme->addColumn('is_active', IColumnScheme::TYPE_BOOL, [IColumnScheme::OPTION_DEFAULT_VALUE => 1]);

    $tableScheme->addColumn(
        'rating',
        IColumnScheme::TYPE_REAL,
        [IColumnScheme::OPTION_UNSIGNED => true, IColumnScheme::OPTION_DEFAULT_VALUE => 0]
    );
    $tableScheme->addColumn('height', IColumnScheme::TYPE_INT, [IColumnScheme::OPTION_UNSIGNED => true]);

    $tableScheme->addColumn('group_id', IColumnScheme::TYPE_RELATION);

    $tableScheme->addColumn('supervisor_field', IColumnScheme::TYPE_INT, [IColumnScheme::OPTION_UNSIGNED => true]);
    $tableScheme->addColumn('guest_field', IColumnScheme::TYPE_INT, [IColumnScheme::OPTION_UNSIGNED => true]);

    $tableScheme->setPrimaryKey('id');
    $tableScheme->addIndex('user_guid')
        ->addColumn('guid')
        ->setIsUnique(true);
    $tableScheme->addIndex('user_group')
        ->addColumn('group_id');

    $tableScheme->addConstraint('FK_user_group', 'group_id', 'umi_mock_groups', 'id', 'SET NULL', 'CASCADE');

};