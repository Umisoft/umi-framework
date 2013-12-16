<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\func\drivers\sqlite;

use umi\dbal\builder\SelectBuilder;
use umi\dbal\driver\dialect\SqliteDialect;
use umi\dbal\toolbox\factory\QueryBuilderFactory;
use utest\dbal\DbalTestCase;

/**
 * Тестирование mysql-запросов
 * @package
 */
class SqliteQueriesTest extends DbalTestCase
{
    /**
     * @var SelectBuilder $select
     */
    protected $selectBuilder;
    protected $affectedTables = ['temp_test_table'];

    protected function setUpFixtures()
    {
        $this->connection->exec('CREATE TABLE IF NOT EXISTS temp_test_table (id INTEGER)');
        $this->connection->exec('INSERT INTO temp_test_table(id) VALUES (1)');
        $this->connection->exec('INSERT INTO temp_test_table(id) VALUES (2)');
        $this->connection->exec('INSERT INTO temp_test_table(id) VALUES (3)');

        $queryBuilderFactory = new QueryBuilderFactory();
        $this->resolveOptionalDependencies($queryBuilderFactory);
        $this->selectBuilder = new SelectBuilder($this->connection, new SqliteDialect(), $queryBuilderFactory);
    }

    public function testSelectTotal()
    {
        $this->selectBuilder
            ->select('t.id')
            ->from('temp_test_table as t')
            ->where()
            ->expr('t.id', '!=', ':zero')
            ->limit(':limit', ':offset');

        $this->selectBuilder
            ->bindInt(':zero', 0)
            ->bindInt(':limit', 2)
            ->bindInt(':offset', 1);

        $this->selectBuilder->execute()->closeCursor();

        $this->assertEquals(3, $this->selectBuilder->getTotal(), 'Ожидается, что записей удовлетворяющих запросу будет 3');

        $this->connection->exec('INSERT INTO temp_test_table(id) VALUES (4)');
        $this->selectBuilder
            ->execute()
            ->closeCursor();
        $this->assertEquals(
            4,
            $this->selectBuilder->getTotal(),
            'Ожидается, что после добавления записи записей удовлетворяющих запросу будет 4'
        );
    }

    public function testResultsCount()
    {
        $this->selectBuilder
            ->select('t.id')
            ->from('temp_test_table as t')
            ->where()
            ->expr('t.id', '!=', ':zero');

        $this->selectBuilder
            ->bindInt(':zero', 0)
            ->bindInt(':limit', 2)
            ->bindInt(':offset', 1);


        $this->assertEquals(3, $this->selectBuilder->getTotal(), 'Ожидается, что записей удовлетворяющих запросу будет 3');

        $this->connection->exec('INSERT INTO temp_test_table(id) VALUES (4)');

        $this->assertEquals(
            4,
            $this->selectBuilder->getTotal(),
            'Ожидается, что после добавления записи записей удовлетворяющих запросу будет 4'
        );
    }
}
