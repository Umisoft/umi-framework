<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\func\drivers\mysql;

use umi\dbal\builder\SelectBuilder;
use umi\dbal\driver\IDbDriver;
use umi\dbal\toolbox\factory\QueryBuilderFactory;
use utest\TestCase;

/**
 * Тестирование mysql-запросов
 * @package
 */
class SqliteQueriesTest extends TestCase
{
    /**
     * @var IDbDriver $dbDriver
     */
    protected $dbDriver;
    /**
     * @var SelectBuilder $select
     */
    protected $select;

    protected function setUpFixtures()
    {
        $this->dbDriver = $this->getSqliteServer()
            ->getDbDriver();
        $this->dbDriver->modify('CREATE TABLE IF NOT EXISTS temp_test_table (id INTEGER)');
        $this->dbDriver->modify('INSERT INTO temp_test_table(id) VALUES (1)');
        $this->dbDriver->modify('INSERT INTO temp_test_table(id) VALUES (2)');
        $this->dbDriver->modify('INSERT INTO temp_test_table(id) VALUES (3)');

        $queryBuilderFactory = new QueryBuilderFactory();
        $this->resolveOptionalDependencies($queryBuilderFactory);

        $this->select = new SelectBuilder($this->dbDriver, $queryBuilderFactory);
    }

    protected function tearDownFixtures()
    {
        $this->dbDriver->modify('DROP TABLE temp_test_table');
    }

    public function testSelectTotal()
    {

        $this->select->select('t.id')
            ->from(array('temp_test_table', 't'))
            ->where()
            ->expr('t.id', '!=', ':zero')
            ->limit(':limit', ':offset');

        $this->select
            ->bindInt(':zero', 0)
            ->bindInt(':limit', 2)
            ->bindInt(':offset', 1);

        $this->select->execute();

        $this->assertEquals(3, $this->select->getTotal(), 'Ожидается, что записей удовлетворяющих запросу будет 3');

        $this->dbDriver->modify('INSERT INTO temp_test_table(id) VALUES (4)');
        $this->select->execute();
        $this->assertEquals(
            4,
            $this->select->getTotal(),
            'Ожидается, что после добавления записи записей удовлетворяющих запросу будет 4'
        );
    }
}
