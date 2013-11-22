<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\func\drivers\mysql;

//todo join platform-separated similar tests to one, dataProvided
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Logging\DebugStack;
use umi\dbal\builder\SelectBuilder;
use umi\dbal\driver\dialect\MySqlDialect;
use umi\dbal\toolbox\factory\QueryBuilderFactory;
use utest\dbal\DbalTestCase;

/**
 * Тестирование mysql запросов
 * @package
 */
class MySqlQueriesTest extends DbalTestCase
{
    /**
     * @var Connection $connection
     */
    protected $connection;
    /**
     * @var SelectBuilder $select
     */
    protected $select;
    /**
     * @var SelectBuilder $select2
     */
    protected $select2;

    /**
     * @return array
     */
    final protected function getQueries()
    {
        //todo move sqlLogger up to testCase
        return array_values(
            array_map(
                function ($a) {
                    return $a['sql'];
                },
                $this->sqlLogger()->queries
            )
        );
    }

    /**
     * @param array $queries
     */
    public function setQueries($queries)
    {
        $this->sqlLogger()->queries = $queries;
    }

    /**
     * @return DebugStack
     */
    public function sqlLogger()
    {
        return $this->connection
            ->getConfiguration()
            ->getSQLLogger();
    }

    protected function setUpFixtures()
    {

        $this->connection = $this
            ->getMysqlServer()
            ->getConnection();
        $this->connection
            ->getConfiguration()
            ->setSQLLogger(new DebugStack());
        $this->connection->exec('DROP TABLE IF EXISTS `temp_test_table1`');
        $this->connection->exec('CREATE TABLE `temp_test_table1` (id INTEGER)');
        $this->connection->exec('INSERT INTO `temp_test_table1` (id) VALUES (1)');
        $this->connection->exec('INSERT INTO `temp_test_table1` (id) VALUES (2)');
        $this->connection->exec('INSERT INTO `temp_test_table1` (id) VALUES (3)');

        $queryBuilderFactory = new QueryBuilderFactory();
        $this->select = new SelectBuilder($this->connection, new MySqlDialect(), $queryBuilderFactory);

        $this->select2 = new SelectBuilder($this->connection, new MySqlDialect(), $queryBuilderFactory);

        $this->setQueries([]);

        $this->resolveOptionalDependencies($queryBuilderFactory);
    }

    protected function tearDownFixtures()
    {
        $this->connection->exec('DROP TABLE `temp_test_table1`');
    }

    public function testSelectTotal()
    {

        $this->select
            ->select('t.id')
            ->from(['temp_test_table1', 't'])
            ->where()
            ->expr('t.id', '!=', ':zero');
        $this->select
            ->bindInt(':zero', 0);

        $this->select->execute();
        $this->assertEquals(3, $this->select->getTotal(), 'Ожидается, что записей удовлетворяющих запросу будет 3');

        $expectedResult = [
            'SELECT `t`.`id`
FROM `temp_test_table1` AS `t`
WHERE `t`.`id` != :zero',
            'SELECT count(*) FROM (SELECT `t`.`id`
FROM `temp_test_table1` AS `t`
WHERE `t`.`id` != :zero) AS `mainQuery`'
        ];
        $this->assertEquals(
            $expectedResult,
            $this->getQueries(),
            'Неверные запросы для получения общего количества значений в выборке, если не было лимита'
        );
        $this->setQueries([]);

        $this->select2
            ->select('t.id')
            ->from(['temp_test_table1', 't'])
            ->where()
            ->expr('t.id', '!=', ':zero')
            ->limit(':limit', ':offset', true);

        $this->select2
            ->bindInt(':zero', 0)
            ->bindInt(':limit', 2)
            ->bindInt(':offset', 1);

        $this->assertEquals(3, $this->select2->getTotal(), 'Ожидается, что записей удовлетворяющих запросу будет 3');
        $expectedResult = [
            'SELECT count(*) FROM (SELECT `t`.`id`
FROM `temp_test_table1` AS `t`
WHERE `t`.`id` != :zero) AS `mainQuery`'
        ];
        $this->assertEquals(
            $expectedResult,
            $this->getQueries(),
            'Неверный запрос для получения общего количества значений в выборке, если выборка не была выполнена'
        );

        $this->connection->exec('INSERT INTO `temp_test_table1` (id) VALUES (4)');
        $this->setQueries([]);

        $this->select2->execute();
        $this->assertEquals(
            4,
            $this->select2->getTotal(),
            'Ожидается, что после добавления записи записей удовлетворяющих запросу будет 4'
        );
        $expectedResult = [
            'SELECT SQL_CALC_FOUND_ROWS `t`.`id`
FROM `temp_test_table1` AS `t`
WHERE `t`.`id` != :zero
LIMIT :limit OFFSET :offset',
            'SELECT FOUND_ROWS()'
        ];
        $this->assertEquals(
            $expectedResult,
            $this->getQueries(),
            'Неверные запросы для получения общего количества значений в выборке, если был задан лимит и опция SQL_CALC_FOUND_ROWS'
        );
    }
}
