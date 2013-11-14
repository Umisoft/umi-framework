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
use umi\dbal\cluster\IConnection;
use umi\dbal\driver\IDbDriver;
use umi\dbal\toolbox\factory\QueryBuilderFactory;
use umi\event\IEvent;
use utest\dbal\DbalTestCase;

/**
 * Тестирование mysql запросов
 * @package
 */
class MySqlQueriesTest extends DbalTestCase
{
    public $queries = array();
    /**
     * @var IDbDriver $dbDriver
     */
    protected $dbDriver;
    /**
     * @var SelectBuilder $select
     */
    protected $select;
    /**
     * @var SelectBuilder $select2
     */
    protected $select2;

    protected function setUpFixtures()
    {

        $this->dbDriver = $this->getMysqlServer()
            ->getDbDriver();
        $this->dbDriver->modify('DROP TABLE IF EXISTS `temp_test_table1`');
        $this->dbDriver->modify('CREATE TABLE `temp_test_table1` (id INTEGER)');
        $this->dbDriver->modify('INSERT INTO `temp_test_table1` (id) VALUES (1)');
        $this->dbDriver->modify('INSERT INTO `temp_test_table1` (id) VALUES (2)');
        $this->dbDriver->modify('INSERT INTO `temp_test_table1` (id) VALUES (3)');

        $queryBuilderFactory = new QueryBuilderFactory();
        $this->resolveOptionalDependencies($queryBuilderFactory);

        $this->select = new SelectBuilder($this->dbDriver, $queryBuilderFactory);

        $this->select2 = new SelectBuilder($this->dbDriver, $queryBuilderFactory);

        $this->queries = array();
        $self = $this;
        $this->dbDriver->bindEvent(
            IConnection::EVENT_BEFORE_PREPARE_QUERY,
            function (IEvent $event) use ($self) {
                $self->queries[] = $event->getParam('sql');
            }
        );
    }

    protected function tearDownFixtures()
    {
        $this->dbDriver->modify('DROP TABLE `temp_test_table1`');
    }

    public function testSelectTotal()
    {

        $this->select->select('t.id')
            ->from(array('temp_test_table1', 't'))
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
            $this->queries,
            'Неверные запросы для получения общего количества значений в выборке, если не было лимита'
        );
        $this->select->getTotal();
        $this->assertEquals(
            $expectedResult,
            $this->queries,
            'Ожидается, что повторного запроса на получение общего количества записей не будет'
        );
        $this->queries = [];

        $this->select2->select('t.id')
            ->from(array('temp_test_table1', 't'))
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
            $this->queries,
            'Неверный запрос для получения общего количества значений в выборке, если выборка не была выполнена'
        );

        $this->dbDriver->modify('INSERT INTO `temp_test_table1` (id) VALUES (4)');
        $this->queries = [];

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
            $this->queries,
            'Неверные запросы для получения общего количества значений в выборке, если был задан лимит и опция SQL_CALC_FOUND_ROWS'
        );
    }
}
