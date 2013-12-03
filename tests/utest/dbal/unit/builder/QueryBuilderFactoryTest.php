<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\builder;

use Doctrine\DBAL\DriverManager;
use umi\dbal\driver\dialect\SqliteDialect;
use umi\dbal\toolbox\factory\QueryBuilderFactory;
use utest\dbal\DbalTestCase;

/**
 * Тест фабрики построителей запросов
 *
 */
class QueryBuilderFactoryTest extends DbalTestCase
{

    public function testQueryBuilderFactory()
    {

        $queryBuilderFactory = new QueryBuilderFactory();
        $this->resolveOptionalDependencies($queryBuilderFactory);

        $connection = DriverManager::getConnection(
            ['driver' => 'pdo_sqlite', 'memory' => 1]
        );

        $this->assertInstanceOf(
            'umi\dbal\builder\IInsertBuilder',
            $queryBuilderFactory->createInsertBuilder($connection, new SqliteDialect()),
            'Ожидается, что IQueryBuilderFactory::createInsertBuilder() вернет IInsertBuilder'
        );
        $this->assertInstanceOf(
            'umi\dbal\builder\ISelectBuilder',
            $queryBuilderFactory->createSelectBuilder($connection, new SqliteDialect()),
            'Ожидается, что IQueryBuilderFactory::createInsertBuilder() вернет ISelectBuilder'
        );
        $this->assertInstanceOf(
            'umi\dbal\builder\IUpdateBuilder',
            $queryBuilderFactory->createUpdateBuilder($connection, new SqliteDialect()),
            'Ожидается, что IQueryBuilderFactory::createInsertBuilder() вернет IUpdateBuilder'
        );
        $this->assertInstanceOf(
            'umi\dbal\builder\IDeleteBuilder',
            $queryBuilderFactory->createDeleteBuilder($connection, new SqliteDialect()),
            'Ожидается, что IQueryBuilderFactory::createInsertBuilder() вернет IDeleteBuilder'
        );
    }
}
