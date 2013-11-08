<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\func\builder;

use umi\dbal\builder\IExpressionGroup;
use umi\dbal\cluster\server\IServer;
use umi\dbal\driver\IColumnScheme;
use utest\TestCase;

/**
 * Интеграционные тесты различного рода запросов
 */
class QueriesTest extends TestCase
{
    /**
     * @var IServer $connection
     */
    protected $connection;

    protected function setUpFixtures()
    {

        $this->connection = $this->getDbServer();

        $table = $this->connection->getDbDriver()
            ->addTable('tests_query_table');
        $table->addColumn('id', IColumnScheme::TYPE_SERIAL);
        $table->addColumn('name', IColumnScheme::TYPE_VARCHAR);
        $table->addColumn('title', IColumnScheme::TYPE_VARCHAR);
        $table->addColumn('is_active', IColumnScheme::TYPE_BOOL, [IColumnScheme::OPTION_DEFAULT_VALUE => 1]);
        $table->addColumn('height', IColumnScheme::TYPE_INT);
        $table->addColumn('weight', IColumnScheme::TYPE_REAL);
        $table->setPrimaryKey('id');

        $this->connection->getDbDriver()
            ->applyMigrations();
    }

    protected function tearDownFixtures()
    {
        $this->connection->getDbDriver()
            ->deleteTable('tests_query_table')
            ->applyMigrations();
    }

    public function testQueryResult()
    {

        $selectQuery = $this->connection->select('id')
            ->from('tests_query_table');
        $result = $selectQuery->execute();

        $this->assertNull(
            $result->fetchVal(),
            'Ожидается, что метод IQueryResult::fetchVal() вернет null, если данных в выборке нет'
        );

        $insertQuery = $this->connection->insert('tests_query_table')
            ->set('name', ':name')
            ->bindString(':name', 'тест');
        $insertQuery->execute();

        $result = $selectQuery->execute();
        $this->assertEquals(1, $result->fetchVal());

        ob_start();
        $result->debugInfo();
        $debugInfo = ob_get_contents();
        ob_get_clean();

        $this->assertNotEmpty($debugInfo, 'Ожидается, что была выведена информация о запросе для дебага');
    }

    public function testBindParams()
    {
        // Insert tests
        $insertQuery = $this->connection->insert('tests_query_table')
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
        $name = 'Record1';
        $title = 'Title1';
        $activity = false;
        $height = 163;
        $weight = 51.7;
        $result = $insertQuery->execute();
        $this->assertEquals(1, $result->count(), 'Ожидается 1 добавленная строка');

        // row 2
        $name = 'Record2';
        $title = 'Title2';
        $activity = true;
        $height = 170;
        $weight = 48.2;
        $result = $insertQuery->execute();
        $this->assertEquals(1, $result->countRows());

        // row 3
        $insertQuery->execute();
        $this->assertEquals(3, $result->lastInsertId(), 'Неверный последний вставленный id');

        // Insert ON DUPLICATE KEY UPDATE
        $insertUpdate = $this->connection->insert('tests_query_table')
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
        // Mysql bug: приходит 2 строки вместо одной
        //$this->assertEquals(1, $result->count(), 'Ожидается 1 модифицированная строка');

        // UpdateBuilder
        $update = $this->connection->update('tests_query_table')
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
        $this->assertEquals(2, $result->count(), 'Ожидается 2 модифицированные строки');

        $selectQuery = $this->connection->select('id', 'name', 'title', 'is_active as activity')
            ->from('tests_query_table');

        $selectQuery
            ->bindColumnInt('id', $id)
            ->bindColumnString('name', $name)
            ->bindColumnString('title', $title)
            ->bindColumnBool('activity', $activity)
            ->bindColumnFloat('float_val', $float);

        $expectedResult = [
            0 => ['id' => 1, 'name' => 'Record1 updated', 'title' => 'Title1', 'activity' => true],
            1 => ['id' => 2, 'name' => 'Record2.3 updated', 'title' => 'Title2', 'activity' => false],
            2 => ['id' => 3, 'name' => 'Record2.3 updated', 'title' => 'Title2', 'activity' => false]
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
            0 => ['id' => 1, 'name' => 'Record1 updated', 'title' => 'Title1', 'activity' => true, 'float_val' => null],
            1 => [
                'id'        => 2,
                'name'      => 'Record2.3 updated',
                'title'     => 'Title2',
                'activity'  => false,
                'float_val' => null
            ],
            2 => [
                'id'        => 3,
                'name'      => 'Record2.3 updated',
                'title'     => 'Title2',
                'activity'  => false,
                'float_val' => null
            ]
        ];

        $this->assertEquals($expectedResult, $iteratorResult);

        // test IN expression
        $selectQuery = $this->connection->select('id')
            ->from('tests_query_table')
            ->where()
            ->expr('id', 'IN', ':ids')
            ->orderBy('id', 'ASC');

        // first bind array
        $selectQuery->bindArray(':ids', array(1, 2, null));
        $this->assertEquals(
            array(array('id' => '1'), array('id' => '2')),
            $selectQuery->execute()
                ->fetchAll()
        );
        // second bind array
        $selectQuery->bindArray(':ids', array(2, 3));
        $this->assertEquals(
            array(array('id' => '2'), array('id' => '3')),
            $selectQuery->execute()
                ->fetchAll()
        );
        // bind other size array
        $selectQuery->bindArray(':ids', array(2, 3, 1, 5));
        $this->assertEquals(
            array(array('id' => '1'), array('id' => '2'), array('id' => '3')),
            $selectQuery->execute()
                ->fetchAll()
        );
    }
}
