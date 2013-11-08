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

    $tableScheme->addColumn('user_id', IColumnScheme::TYPE_RELATION);
    $tableScheme->addColumn('city_id', IColumnScheme::TYPE_RELATION);
    $tableScheme->addColumn('name', IColumnScheme::TYPE_VARCHAR);
    $tableScheme->addColumn('org_name', IColumnScheme::TYPE_VARCHAR);

    $tableScheme->setPrimaryKey('id');
    $tableScheme->addIndex('profile_guid')
        ->addColumn('guid')
        ->setIsUnique(true);
    $tableScheme->addIndex('profile_user')
        ->addColumn('user_id');
    $tableScheme->addIndex('profile_city')
        ->addColumn('city_id');

    $tableScheme->addConstraint('FK_profile_user', 'user_id', 'umi_mock_users', 'id', 'CASCADE', 'CASCADE');
    $tableScheme->addConstraint('FK_profile_city', 'city_id', 'umi_mock_cities', 'id', 'CASCADE', 'CASCADE');

};