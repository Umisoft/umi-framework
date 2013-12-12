<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\func\builder;

use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use umi\dbal\builder\IExpressionGroup;
use umi\dbal\builder\ISelectBuilder;
use umi\dbal\cluster\server\IServer;
use utest\dbal\DbalTestCase;

/**
 * Интеграционные тесты различного рода запросов
 */
class QueriesTest extends DbalTestCase
{
    /**
     * @var IServer $server
     */
    protected $server;
    protected $affectedTables = ['tests_query_table'];

    protected function setUpFixtures()
    {
        $this->server = $this->getDbServer();
        $table = new Table('tests_query_table');

        $table
            ->addColumn('id', Type::INTEGER)
            ->setAutoincrement(true);
        $table
            ->addColumn('name', Type::STRING)
            ->setNotnull(false);
        $table
            ->addColumn('title', Type::STRING)
            ->setNotnull(false);
        $table
            ->addColumn('is_active', Type::BOOLEAN, ['DEFAULT' => 1])
            ->setNotnull(false);
        $table
            ->addColumn('height', Type::INTEGER)
            ->setNotnull(false);
        $table
            ->addColumn('weight', Type::DECIMAL)
            ->setNotnull(false);
        $table->setPrimaryKey(['id']);

        $this->server->getConnection()
            ->getSchemaManager()
            ->createTable(
                $table
            );
    }

    public function testQueryResult()
    {
        $selectQuery = $this->server
            ->select('id')
            ->from('tests_query_table');
        $result = $selectQuery->execute();

        $this->assertFalse(
            $result->fetchColumn(),
            'Ожидается, что метод IQueryResult::fetchColumn() вернет null, если данных в выборке нет'
        );

        $insertQuery = $this->server
            ->insert('tests_query_table')
            ->set('name', ':name')
            ->bindString(':name', 'тест');
        $insertQuery->execute();

        $result = $selectQuery->execute();
        $this->assertEquals(1, $result->fetchColumn());
        /** @noinspection PhpUndefinedFieldInspection */
        $this->assertNotEmpty(
            $this
                ->sqlLogger()
                ->getQueries(),
            'Ожидается, что была выведена информация о запросе для дебага'
        );
    }

    public function testBindParams()
    {
        // Insert tests
        $insertQuery = $this->server
            ->insert('tests_query_table')
            ->set('name', ':name')
            ->set('title', ':title')
            ->set('is_active', ':activity')
            ->set('height', ':height')
            ->set('weight', ':weight');

        $insertQuery->bindVarString(':name', $name);
        $insertQuery->bindVarString(':title', $title);
        $insertQuery->bindVarBool(':activity', $activity);
        $insertQuery->bindVarInt(':height', $height);
        $insertQuery->bindVarFloat(':weight', $weight);

        // row 1
        /** @noinspection PhpUnusedLocalVariableInspection */
        $name = 'Record1';
        /** @noinspection PhpUnusedLocalVariableInspection */
        $title = 'Title1';
        /** @noinspection PhpUnusedLocalVariableInspection */
        $activity = false;
        /** @noinspection PhpUnusedLocalVariableInspection */
        $height = 163;
        /** @noinspection PhpUnusedLocalVariableInspection */
        $weight = 51.7;
        $result = $insertQuery->execute();
        $this->assertEquals(1, $result->rowCount(), 'Ожидается 1 добавленная строка');

        // row 2
        /** @noinspection PhpUnusedLocalVariableInspection */
        $name = 'Record2';
        /** @noinspection PhpUnusedLocalVariableInspection */
        $title = 'Title2';
        /** @noinspection PhpUnusedLocalVariableInspection */
        $activity = true;
        /** @noinspection PhpUnusedLocalVariableInspection */
        $height = 170;
        /** @noinspection PhpUnusedLocalVariableInspection */
        $weight = 48.2;
        $result = $insertQuery->execute();
        $this->assertEquals(1, $result->rowCount());

        // row 3
        $insertQuery->execute();
        $this->assertEquals(
            3,
            $this->server
                ->getConnection()
                ->lastInsertId(),
            'Неверный последний вставленный id'
        );

        // Insert ON DUPLICATE KEY UPDATE
        $insertUpdate = $this->server
            ->insert('tests_query_table')
            ->set('id', ':id')
            ->set('name', ':name')
            ->set('is_active', ':activity')
            ->onDuplicateKey('id')
            ->set('name', ':updateName')
            ->set('is_active', ':updateActivity');

        $insertUpdate->bindInt(':id', 1);
        $insertUpdate->bindString(':name', 'New name');
        $insertUpdate->bindBool(':activity', true);

        $insertUpdate->bindString(':updateName', 'Record1 updated');
        $insertUpdate->bindBool(':updateActivity', true);

        $result = $insertUpdate->execute();

        // TODO: Mysql  вернет 2, если вставленная строка существовала
        $this->assertEquals(1, $result->rowCount(), 'Ожидается 1 модифицированная строка');

        /** @var ISelectBuilder $inserted */
        $duplicateUpdated = $this->server
            ->select(['name', 'is_active'])
            ->from('tests_query_table')
            ->where()
            ->expr('id', '=', ':id')
            ->orderBy('id', 'ASC');
        $duplicateUpdatedRow = $duplicateUpdated
            ->bindInt(':id', 1)
            ->execute()
            ->fetch(\PDO::FETCH_ASSOC);

        $this->assertEquals('Record1 updated', $duplicateUpdatedRow['name'], 'Name not rewritten on duplicate key');
        $this->assertEquals('1', $duplicateUpdatedRow['is_active'], 'Activation not rewritten on duplicate key');

        // UpdateBuilder
        $update = $this->server
            ->update('tests_query_table')
            ->set('name', ':updateName')
            ->set('is_active', ':activity')
            ->where()
            ->begin(IExpressionGroup::MODE_OR)
            ->expr('name', 'LIKE', ':name')
            ->expr('name', 'IS', ':nullName')
            ->end();

        $update->bindBool(':activity', false);
        $update->bindString(':updateName', 'Record2.3 updated');
        $update->bindString(':name', "%rd2");
        $update->bindNull(':nullName');

        $result = $update->execute();
        $this->assertEquals(2, $result->rowCount(), 'Ожидается 2 модифицированные строки');

        /** @noinspection PhpUnusedLocalVariableInspection */
        $selectQuery = $this->server
            ->select(['id', 'name', 'title', 'is_active as activity'])
            ->from('tests_query_table');

        // test IN expression
        $selectQuery = $this->server
            ->select('id')
            ->from('tests_query_table')
            ->where()
            ->expr('id', 'IN', ':ids')
            ->orderBy('id', 'ASC');

        // first bind array
        $selectQuery->bindArray(':ids', array(1, 2, null));
        $this->assertEquals(
            array(array('id' => '1'), array('id' => '2')),
            $selectQuery
                ->execute()
                ->fetchAll()
        );
        // second bind array
        $selectQuery->bindArray(':ids', array(2, 3));
        $this->assertEquals(
            array(array('id' => '2'), array('id' => '3')),
            $selectQuery
                ->execute()
                ->fetchAll()
        );
        // bind other size array
        $selectQuery->bindArray(':ids', array(2, 3, 1, 5));
        $this->assertEquals(
            array(array('id' => '1'), array('id' => '2'), array('id' => '3')),
            $selectQuery
                ->execute()
                ->fetchAll()
        );
    }

    public function testBindReferences()
    {
        $this->markTestIncomplete('Реализовать привязку переменных к результатам');

        /** @noinspection PhpUnusedLocalVariableInspection */
        $name = 'Record2';
        /** @noinspection PhpUnusedLocalVariableInspection */
        $title = 'Title2';
        /** @noinspection PhpUnusedLocalVariableInspection */
        $activity = true;
        /** @noinspection PhpUnusedLocalVariableInspection */
        $height = 170;
        /** @noinspection PhpUnusedLocalVariableInspection */
        $weight = 48.2;

        $id = 12345;
        $float = 123.45;

        /** @noinspection PhpUnusedLocalVariableInspection */
        $selectQuery = $this->server
            ->select(['id', 'name', 'title', 'is_active as activity'])
            ->from('tests_query_table');
        $selectQuery
            ->bindInt('id', $id)
            ->bindString('name', $name)
            ->bindString('title', $title)
            ->bindBool('activity', $activity)
            ->bindFloat('float_val', $float);

        $expectedResult = [
            0 => ['id' => 1, 'name' => 'Record1 updated', 'title' => 'Title1', 'activity' => 1],
            1 => ['id' => 2, 'name' => 'Record2.3 updated', 'title' => 'Title2', 'activity' => 0],
            2 => ['id' => 3, 'name' => 'Record2.3 updated', 'title' => 'Title2', 'activity' => 0]
        ];

        $result = $selectQuery->execute();
        $rows = $result->fetchAll();

        $this->assertEquals($expectedResult, $rows);
        $this->assertSame($rows, $result->fetchAll(), 'Ожидается такой же результат для повторного fetchAll');

        $i = 0;
        foreach ($result as $key => $value) {
            $this->assertTrue($key == $i, $value == $expectedResult[$i], 'Неверная итерация результата запроса');
            $i++;
        }

        $result = $selectQuery->execute();
        $iteratorResult = [];
        while ($result->fetch()) {
            $iteratorResult[] = [
                'id'        => $id,
                'name'      => $name,
                'title'     => $title,
                'activity'  => $activity,
                'float_val' => $float
            ];
        }

        $expectedResult = [
            0 => ['id' => 1, 'name' => 'Record1 updated', 'title' => 'Title1', 'activity' => 1, 'float_val' => null],
            1 => ['id' => 2, 'name' => 'Record2.3 updated', 'title' => 'Title2', 'activity' => 0, 'float_val' => null],
            2 => ['id' => 3, 'name' => 'Record2.3 updated', 'title' => 'Title2', 'activity' => 0, 'float_val' => null]
        ];

        $this->assertEquals(
            $expectedResult,
            $iteratorResult,
            'Bound variables should refresh their values after select'
        );
    }
}
