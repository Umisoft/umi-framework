<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\func\drivers\mysql;

use Doctrine\DBAL\Connection;
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
     * @var Connection $dbDriver
     */
    protected $dbDriver;
    /**
     * @var SelectBuilder $select
     */
    protected $select;

    protected function setUpFixtures()
    {
        $this->dbDriver = $this
            ->getSqliteServer()
            ->getConnection();
        $this->dbDriver->exec('CREATE TABLE IF NOT EXISTS temp_test_table (id INTEGER)');
        $this->dbDriver->exec('INSERT INTO temp_test_table(id) VALUES (1)');
        $this->dbDriver->exec('INSERT INTO temp_test_table(id) VALUES (2)');
        $this->dbDriver->exec('INSERT INTO temp_test_table(id) VALUES (3)');

        $queryBuilderFactory = new QueryBuilderFactory();
        $this->resolveOptionalDependencies($queryBuilderFactory);
        $this->select = new SelectBuilder($this->dbDriver, new SqliteDialect(), $queryBuilderFactory);
    }

    protected function tearDownFixtures()
    {
        $this->dbDriver->exec('DROP TABLE temp_test_table');
    }

    public function testSelectTotal()
    {
        // @formatter:off

        $this->select
            ->select('t.id')
            ->from('temp_test_table as t')
            ->where()
            ->expr('t.id', '!=', ':zero')
            ->limit(':limit', ':offset');
        // @formatter:on

        $this->select
            ->bindInt(':zero', 0)
            ->bindInt(':limit', 2)
            ->bindInt(':offset', 1);
        //todo! как это комментировать?
        /* IMPORTANT *
           если не закрыть курсор явно и выполнить еще запросы (в т.ч. скрытые. например, для подсчета)
           - не получится сделать DROP TABLE
        */
        $this->select
            ->execute()
            ->closeCursor();

        $this->assertEquals(3, $this->select->getTotal(), 'Ожидается, что записей удовлетворяющих запросу будет 3');

        $this->dbDriver->exec('INSERT INTO temp_test_table(id) VALUES (4)');
        $this->select
            ->execute()
            ->closeCursor();
        $this->assertEquals(
            4,
            $this->select->getTotal(),
            'Ожидается, что после добавления записи записей удовлетворяющих запросу будет 4'
        );
    }

    public function testResultsCount()
    {
        $this->select
            ->select('t.id')
            ->from('temp_test_table as t')
            ->where()
            ->expr('t.id', '!=', ':zero');
        $this->select
            ->bindInt(':zero', 0)
            ->bindInt(':limit', 2)
            ->bindInt(':offset', 1);

        $this->assertEquals(3, $this->select->getTotal(), 'Ожидается, что записей удовлетворяющих запросу будет 3');

        $this->dbDriver->exec('INSERT INTO temp_test_table(id) VALUES (4)');

        $this->assertEquals(
            4,
            $this->select->getTotal(),
            'Ожидается, что после добавления записи записей удовлетворяющих запросу будет 4'
        );
    }
}
