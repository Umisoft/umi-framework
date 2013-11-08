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

    $tableScheme->addColumn('name', IColumnScheme::TYPE_VARCHAR);
    $tableScheme->addColumn('country_id', IColumnScheme::TYPE_RELATION);

    $tableScheme->setPrimaryKey('id');
    $tableScheme->addIndex('city_guid')
        ->addColumn('guid')
        ->setIsUnique(true);

    $tableScheme->addConstraint('FK_country', 'country_id', 'umi_mock_countries', 'id', 'CASCADE', 'CASCADE');

};
