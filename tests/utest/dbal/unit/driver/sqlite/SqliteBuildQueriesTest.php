<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\driver\sqlite;

use umi\dbal\cluster\IConnection;
use utest\dbal\DbalTestCase;

/**
 * Тестирование sqlite-билдера запросов
 * @package
 */
class SqliteBuildQueriesTest extends DbalTestCase
{
    /**
     * @var IConnection $dbDriver
     */
    private $connection;

    protected function setUpFixtures()
    {
        $this->connection = $this->getSqliteServer();
    }

    public function testBuildSelectQuery()
    {
        $select = $this->connection->select(
            'p.id',
            'p.date',
            'p.post',
            'u.login',
            'c.comment as last""\0\"\'_comment',
            'c.date as last_comment_date'
        )
            ->distinct()
            ->from(['tests_post', 'p'])
                ->join('tests_user as u')
                    ->on('u.id', '=', 'p.user_id')
                    ->on('u.id', '=', 'p.user_id')
                ->leftJoin('tests_comment as c')
                    ->on('c.id', '=', 'p.latest_comment_id')
            ->where('OR')
            ->expr('p.id', '!=', ':postId')
            ->begin('AND')
                ->expr('u.id', '!=', ':user')
                    ->begin('OR')
                        ->expr('1', '=', 1)
                        ->expr('2', '!=', 3)
                    ->end()
            ->end()
            ->groupBy('p.id', 'DESC')
            ->having('OR')
            ->expr('last_comment_date', '!=', ':excludeDate')
            ->orderBy("last_comment_date")
            ->orderBy('p.id', 'DESC')
            ->limit(':limit', ':offset');

        $expectedResult = 'SELECT DISTINCT "p"."id", "p"."date", "p"."post", "u"."login", "c"."comment" '
                . 'AS "last""""\0\""\'_comment", "c"."date" AS "last_comment_date"
FROM "tests_post" AS "p"
	INNER JOIN "tests_user" AS "u" ON ("u"."id" = "p"."user_id" AND "u"."id" = "p"."user_id")
	LEFT JOIN "tests_comment" AS "c" ON "c"."id" = "p"."latest_comment_id"
WHERE "p"."id" != :postId OR ("u"."id" != :user AND ("1" = "1" OR "2" != "3"))
GROUP BY "p"."id"
HAVING "last_comment_date" != :excludeDate
ORDER BY "last_comment_date" ASC, "p"."id" DESC
LIMIT :limit OFFSET :offset';

        $this->assertEquals($expectedResult, $select->getSql(), 'Select builder failed!');

        $select = $this->connection->select()->from('tests_post');
        $expectedResult = 'SELECT *
FROM "tests_post"';
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
        $query = $this->connection->update('tests_post', true)
            ->set('latest_comment_id', ':comment_id')
            ->set('date', ':date')
            ->where()
                ->expr('user_id', '=', ':user_id')
            ->orderBy('id')
            ->limit(':limit');

        $expectedResult = 'UPDATE OR IGNORE "tests_post"
SET "latest_comment_id" = :comment_id, "date" = :date
WHERE "user_id" = :user_id';
        $this->assertEquals($expectedResult, $query->getSql(), 'Update builder failed!');
    }

    public function testBuildInsertQuery()
    {
        $query = $this->connection->insert('tests_post', true)
            ->set('latest_comment_id', ':comment_id')
            ->set('date', ':date');

        $expectedResult = 'INSERT OR IGNORE INTO "tests_post"
( "latest_comment_id", "date" ) VALUES ( :comment_id, :date )';
        $this->assertEquals($expectedResult, $query->getSql(), 'Insert builder failed!');
    }

    public function testBuildInsertOnDuplicateKeyQuery()
    {
        $query = $this->connection->insert('tests_post')
            ->set('latest_comment_id', ':comment_id')
            ->set('date', ':date')
            ->onDuplicateKey('latest_comment_id')
            ->set('date', ':newDate');

            $expectedResult = 'INSERT OR IGNORE INTO "tests_post"
( "latest_comment_id", "date" ) VALUES ( :comment_id, :date );
UPDATE "tests_post"
SET "date" = :newDate
WHERE "latest_comment_id" = :comment_id;';

        $this->assertEquals($expectedResult, $query->getSql(), 'Insert OnDuplicateKey builder failed!');
    }

    public function testBuildDeleteQuery()
    {
        $query = $this->connection->delete('tests_post')
            ->where()
            ->expr('user_id', '=', ':user_id')
            ->orderBy('id', 'DESC')
            ->limit(':limit');

            $expectedResult = 'DELETE FROM "tests_post"
WHERE "user_id" = :user_id';
            $this->assertEquals($expectedResult, $query->getSql(), 'Delete builder failed!');
    }
}
