<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\driver;

use umi\dbal\driver\IColumnScheme;
use umi\dbal\driver\IDbDriver;
use utest\TestCase;

/**
 * Тестирование работы со схемой таблицы
 */
class DriverTest extends TestCase
{
    /**
     * @var IDbDriver $driver
     */
    protected $driver;

    protected function setUpFixtures()
    {
        $this->driver = $this->getMysqlServer()
            ->getDbDriver();

        $table0 = $this->driver->addTable('test_table_0');
        $table0->addColumn('test', IColumnScheme::TYPE_INT);
        $table0->addIndex('test_index0')
            ->addColumn('test');
        $table0->setEngine('InnoDB');

        $this->driver->addTable('test_table_1')
            ->addColumn('test', IColumnScheme::TYPE_INT);
        $this->driver->applyMigrations();
    }

    protected function tearDownFixtures()
    {
        $this->driver->dropTable('test_table_2');
        $this->driver->dropTable('test_table_0');
        $this->driver->dropTable('test_table_1');
    }

    public function testDbDriver()
    {

        $this->assertEquals(
            '`table````\000"\'name`',
            $this->driver->sanitizeTableName("table``\0\"'name"),
            'Неверная санитизация имени таблицы'
        );
        $this->assertEquals(
            '`column````\000"\'name`',
            $this->driver->sanitizeColumnName("column``\0\"'name"),
            'Неверная санитизация имени колонки'
        );
        $this->assertEquals(
            '`table_name`.`column````\000"\'name`',
            $this->driver->sanitizeColumnName("table_name.column``\0\"'name"),
            'Неверная санитизация имени колонки'
        );

        $e = null;
        try {
            $this->driver->getColumnInternalType('missedType');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\dbal\exception\RuntimeException',
            $e,
            'Ожидается исключение при попытке получить внутренний тип колонки, если его нет'
        );
        $this->assertNotEmpty(
            $this->driver->getColumnInternalType(IColumnScheme::TYPE_INT),
            'Не удалось получить внутренний тип колонки'
        );

        $table2 = $this->driver->addTable('test_table_2');
        $this->assertInstanceOf(
            'umi\dbal\driver\ITableScheme',
            $table2,
            'Ожидается, что IDbDriver::addTable() вернет ITableScheme'
        );
        $this->assertTrue($this->driver->getTableExists('test_table_2'), 'Ожидается, что созданная таблица существует');
        $this->assertEquals(
            $table2,
            $this->driver->getTable('test_table_2'),
            'Ожидается, что повторном получении таблицы драйвер вернет уже ранее полученную'
        );
        $table2->addColumn('test', IColumnScheme::TYPE_INT);
        $table2->addIndex('test_index2')
            ->addColumn('test');
        $table2->addConstraint('test_constraint', 'test', 'test_table_0', 'test', 'NO ACTION', 'NO ACTION');
        $table2->setEngine('InnoDB');

        $this->assertTrue(
            $this->driver->getTableExists('test_table_0'),
            'Ожидается, что существовавшая таблица существует'
        );
        $this->assertFalse(
            $this->driver->getTableExists('test_table_3'),
            'Ожидается, что несуществующая таблица не существует'
        );

        $e = null;
        try {
            $this->driver->getTable('test_table_3');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\dbal\exception\RuntimeException',
            $e,
            'Ожидается исключение при запросе несуществующей таблицы'
        );

        $this->assertContains(
            'test_table_0',
            $this->driver->getTableNames(),
            'Ожидается, что в списке таблиц только уже сохраненные таблицы'
        );
        $this->assertNotContains(
            'test_table_2',
            $this->driver->getTableNames(),
            'Ожидается, что в списке таблиц только уже сохраненные таблицы'
        );

        $this->assertEquals(
            ['test_table_2'],
            array_keys($this->driver->getNewTables()),
            'Ожидается, что у драйвера 1 новая таблица'
        );

        $table0 = $this->driver->getTable('test_table_0');
        $this->assertSame(
            $table0,
            $this->driver->addTable('test_table_0'),
            'Ожидается, что при попытке добавить существующую таблицу будет возвращена существующая таблица'
        );

        $table0->addColumn('test1', 'text');
        $this->assertEquals(
            ['test_table_0'],
            array_keys($this->driver->getModifiedTables()),
            'Ожидается, что у драйвера 1 модифицированная таблица'
        );

        $this->assertInstanceOf(
            'umi\dbal\driver\IDbDriver',
            $this->driver->deleteTable('test_table_1'),
            'Ожидается, что IDbDriver::deleteTable() вернет себя'
        );

        $this->assertCount(
            3,
            $this->driver->getMigrationQueries(),
            'Ожидается, что у драйвера 3 запроса на применение миграций таблиц'
        );
        $this->assertTrue(
            $this->driver->applyMigrations(),
            'Ожидается, что в случае наличия запросов на изменения IDbDriver::applyMigrations() вернет true'
        );
        $this->assertFalse(
            $this->driver->applyMigrations(),
            'Ожидается, что в случае отсутствия запросов на изменения IDbDriver::applyMigrations() вернет false'
        );
        $this->assertNotEquals(
            $table2,
            $this->driver->getTable('test_table_2'),
            'Ожидается, что после применения миграций схемы таблиц были сброшены'
        );

        $this->assertEquals(1, $this->driver->modify('INSERT INTO `test_table_0` (`test`) VALUES ("1")'));
        $this->assertEquals(
            1,
            $this->driver->modify('INSERT INTO `test_table_2` (`test`) VALUES (:test)', [':test' => "1"])
        );

        $this->assertInstanceOf(
            'PDOStatement',
            $this->driver->select('SELECT * FROM `test_table_0` WHERE `test` != :test', [':test' => '2']),
            'IDbDriver::select() должен вернуть PDOStatement'
        );
        $pdoStatement = $this->driver->select('SELECT * FROM `test_table_2` WHERE `test` != "2"');
        $this->assertInstanceOf('PDOStatement', $pdoStatement, 'IDbDriver::select() должен вернуть PDOStatement');

        $this->assertTrue(
            $this->driver->disableKeys('test_table_0'),
            'Ожидается, что IDbDriver::disableKeys() вернет true'
        );
        $this->assertTrue(
            $this->driver->enableKeys('test_table_0'),
            'Ожидается, что IDbDriver::enableKeys() вернет true'
        );

        $this->assertFalse(
            $this->driver->truncateTable('test_table_0'),
            'Ожидается, что IDbDriver::truncateTable() вернет false, если невозможно очистить таблицу'
        );
        $this->assertFalse(
            $this->driver->dropTable('test_table_0'),
            'Ожидается, что IDbDriver::dropTable() вернет false, если невозможно удалить таблицу'
        );

        $this->assertTrue(
            $this->driver->disableForeignKeysCheck(),
            'Ожидается, что IDbDriver::disableForeignKeysCheck() вернет true'
        );
        $this->assertTrue(
            $this->driver->truncateTable('test_table_0'),
            'Ожидается, что IDbDriver::truncateTable() вернет true, если была отключена проверка внешних ключей'
        );
        $this->assertTrue(
            $this->driver->enableForeignKeysCheck(),
            'Ожидается, что IDbDriver::enableForeignKeysCheck() вернет true'
        );

        $this->assertTrue(
            $this->driver->dropTable('test_table_2'),
            'Ожидается, что IDbDriver::dropTable() вернет true, если можно удалить таблицу'
        );

        $this->assertFalse(
            $this->driver->disableKeys('test_table_2'),
            'Ожидается, что IDbDriver::disableKeys() вернет false, если таблица не существует'
        );
        $this->assertFalse(
            $this->driver->enableKeys('test_table_2'),
            'Ожидается, что IDbDriver::enableKeys() вернет false, если таблица не существует'
        );

        $e = null;
        try {
            $this->driver->executeStatement($pdoStatement);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\dbal\exception\RuntimeException',
            $e,
            'Ожидается исключение при попытке выполнить невыполнимый запрос'
        );
    }
}

/**
 * Class WrongClass для тестирования
 */
class WrongTableClass
{

}
