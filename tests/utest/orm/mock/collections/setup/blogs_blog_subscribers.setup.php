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
    $tableScheme->addColumn('blog_id', IColumnScheme::TYPE_RELATION);

    $tableScheme->setPrimaryKey('id');
    $tableScheme->addIndex('blog_subscriber_guid')
        ->addColumn('guid')
        ->setIsUnique(true);
    $tableScheme->addIndex('subscriber_blog_id')
        ->addColumn('blog_id');
    $tableScheme->addIndex('subscriber_user_id')
        ->addColumn('user_id');
    $tableScheme->addIndex('subscribers_type')
        ->addColumn('type');

    $tableScheme->addConstraint('FK_user', 'user_id', 'umi_mock_users', 'id', 'CASCADE', 'CASCADE');
    $tableScheme->addConstraint('FK_blog', 'blog_id', 'umi_mock_blogs', 'id', 'CASCADE', 'CASCADE');

};
