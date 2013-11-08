<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\builder;

use umi\dbal\driver\sqlite\SqliteDriver;
use umi\dbal\toolbox\factory\QueryBuilderFactory;
use umi\dbal\toolbox\factory\TableFactory;
use utest\TestCase;

/**
 * Тест фабрики построителей запросов
 *
 */
class QueryBuilderFactoryTest extends TestCase
{

    public function testQueryBuilderFactory()
    {

        $queryBuilderFactory = new QueryBuilderFactory();
        $this->resolveOptionalDependencies($queryBuilderFactory);

        $tableFactory = new TableFactory();
        $this->resolveOptionalDependencies($tableFactory);

        $tableFactory->columnSchemeClass = 'umi\dbal\driver\ColumnScheme';
        $tableFactory->constraintSchemeClass = 'umi\dbal\driver\ConstraintScheme';
        $tableFactory->tableSchemeClass = 'umi\dbal\driver\sqlite\SqliteTable';
        $tableFactory->indexSchemeClass = 'umi\dbal\driver\sqlite\SqliteIndex';

        $driver = new SqliteDriver($tableFactory);

        $this->assertInstanceOf(
            'umi\dbal\builder\IInsertBuilder',
            $queryBuilderFactory->createInsertBuilder($driver),
            'Ожидается, что IQueryBuilderFactory::createInsertBuilder() вернет IInsertBuilder'
        );
        $this->assertInstanceOf(
            'umi\dbal\builder\ISelectBuilder',
            $queryBuilderFactory->createSelectBuilder($driver),
            'Ожидается, что IQueryBuilderFactory::createInsertBuilder() вернет ISelectBuilder'
        );
        $this->assertInstanceOf(
            'umi\dbal\builder\IUpdateBuilder',
            $queryBuilderFactory->createUpdateBuilder($driver),
            'Ожидается, что IQueryBuilderFactory::createInsertBuilder() вернет IUpdateBuilder'
        );
        $this->assertInstanceOf(
            'umi\dbal\builder\IDeleteBuilder',
            $queryBuilderFactory->createDeleteBuilder($driver),
            'Ожидается, что IQueryBuilderFactory::createInsertBuilder() вернет IDeleteBuilder'
        );
    }
}
