<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\driver\mysql;

use umi\dbal\cluster\IConnection;
use utest\dbal\DbalTestCase;

/**
 * Тестирование mysql-билдера запросов
 * @package
 */
class MySqlBuildQueriesTest extends DbalTestCase
{
    /**
     * @var IConnection $dbDriver
     */
    private $connection;

    protected function setUpFixtures()
    {
        $this->connection = $this->getMysqlServer();
    }

    public function testBuildSelectQuery()
    {
        $select = $this->connection->select(
            'p.id',
            'p.date',
            'p.post',
            'u.login',
            "c.comment as last``\0\"'_comment",
            'c.date as last_comment_date'
        )
            ->distinct()
            ->from(array('tests_post', 'p'))
            ->join('tests_user as u')
            ->on('u.id', '=', 'p.user_id')
            ->on('u.id', '=', 'p.user_id')
            ->leftJoin('tests_comment as c')
            ->on('c.id', '=', 'p.latest_comment_id')
            ->where('OR')
            ->expr('p.id', 'IN', ':postId')
            ->begin('AND')
            ->expr('u.id', '!=', ':user')
            ->end()
            ->groupBy('p.id', 'DESC')
            ->having('OR')
            ->expr('last_comment_date', '!=', ':excludeDate')
            ->orderBy("last_comment_date")
            ->orderBy('p.id', 'DESC')
            ->limit(':limit', ':offset');

        $select->bindArray(':postId', array('1', 2, 'boo'));

        $expectedResult = 'SELECT DISTINCT `p`.`id`, `p`.`date`, `p`.`post`, `u`.`login`, `c`.`comment` AS `last````\000"\'_comment`, `c`.`date` AS `last_comment_date`
FROM `tests_post` AS `p`
	INNER JOIN `tests_user` AS `u` ON (`u`.`id` = `p`.`user_id` AND `u`.`id` = `p`.`user_id`)
	LEFT JOIN `tests_comment` AS `c` ON `c`.`id` = `p`.`latest_comment_id`
WHERE `p`.`id` IN (:postId0, :postId1, :postId2) OR (`u`.`id` != :user)
GROUP BY `p`.`id` DESC
HAVING `last_comment_date` != :excludeDate
ORDER BY `last_comment_date` ASC, `p`.`id` DESC
LIMIT :limit OFFSET :offset';

        $this->assertEquals($expectedResult, $select->getSql(), 'Select builder failed!');

        $select = $this->connection->select()
            ->from('tests_post')
            ->groupBy('user_id');
        $expectedResult = 'SELECT *
FROM `tests_post`
GROUP BY `user_id`';
        $this->assertEquals($expectedResult, $select->getSql(), 'Неверный текст запроса');

        $select = $this->connection->select('SHOW CREATE TABLE tests_post');
        $e = null;
        try {
            $select->getSql();
        } catch (\Exception $e) {
        }
        $this->assertTrue(is_null($e), 'Ожидается, что не будет исключения для выборки без таблиц');
    }

    public function testBuildUpdateQuery()
    {
        $update = $this->connection->update('tests_post', true)
            ->set('latest_comment_id', ':comment_id')
            ->set('date', ':date')
            ->where()
            ->expr('user_id', '=', ':user_id')
            ->orderBy('id')
            ->limit(':limit');

        $expectedResult = 'UPDATE IGNORE `tests_post`
SET `latest_comment_id` = :comment_id, `date` = :date
WHERE `user_id` = :user_id
ORDER BY `id` ASC
LIMIT :limit';
        $this->assertEquals($expectedResult, $update->getSql(), 'Update builder failed!');
    }

    public function testBuildInsertQuery()
    {
        $insert = $this->connection->insert('tests_post', true)
            ->set('latest_comment_id', ':comment_id')
            ->set('date', ':date')
            ->onDuplicateKey('latest_comment_id')
            ->set('date', ':date');

        $expectedResult = 'INSERT IGNORE INTO `tests_post`
SET `latest_comment_id` = :comment_id, `date` = :date';
        $this->assertEquals($expectedResult, $insert->getSql(), 'Insert builder failed!');
    }

    public function testBuildInsertOnDuplicateKeyQuery()
    {
        $insert = $this->connection->insert('tests_post')
            ->set('latest_comment_id', ':comment_id')
            ->set('date', ':date')
            ->onDuplicateKey('latest_comment_id')
            ->set('latest_comment_id', ':comment_id');

        $expectedResult = 'INSERT INTO `tests_post`
SET `latest_comment_id` = :comment_id, `date` = :date
ON DUPLICATE KEY UPDATE `latest_comment_id` = :comment_id';

        $this->assertEquals($expectedResult, $insert->getSql(), 'Insert OnDuplicateKey builder failed!');
    }

    public function testBuildDeleteQuery()
    {
        $delete = $this->connection->delete('tests_post')
            ->where()
            ->expr('user_id', '=', ':user_id')
            ->orderBy('id', 'DESC')
            ->limit(':limit');
        $expectedResult = 'DELETE FROM `tests_post`
WHERE `user_id` = :user_id
ORDER BY `id` DESC
LIMIT :limit';

        $this->assertEquals($expectedResult, $delete->getSql(), 'Delete builder failed!');
    }
}
