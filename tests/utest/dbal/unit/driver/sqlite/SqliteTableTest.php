<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\driver\sqlite;

use umi\dbal\driver\IColumnScheme;
use utest\dbal\DbalTestCase;

/**
 * Тестирование sqlite драйвера
 * @package
 */
class SqliteTableTest extends DbalTestCase
{

    public function testGenerateCreateQuery()
    {
        $dbDriver = $this->getSqliteServer()
            ->getDbDriver();

        $wrongTable = $dbDriver->addTable('wrong_table');
        $e = null;
        try {
            $wrongTable->getMigrationQueries();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\dbal\exception\RuntimeException',
            $e,
            'Ожидается исключение при получение миграционных запросов пустой таблицы'
        );

        $table = $dbDriver->addTable('test_query_table');

        $table->addColumn('id', IColumnScheme::TYPE_SERIAL);

        $table->addColumn(
            'nu',
            IColumnScheme::TYPE_REAL,
            [IColumnScheme::OPTION_NULLABLE => false, IColumnScheme::OPTION_DEFAULT_VALUE => 2345]
        );

        $table->addColumn('text', IColumnScheme::TYPE_TEXT, [IColumnScheme::OPTION_COLLATION => 'NOCASE']);

        $table->addColumn('int', IColumnScheme::TYPE_INT, [IColumnScheme::OPTION_DEFAULT_VALUE => 0]);

        $table->addColumn('field5', IColumnScheme::TYPE_INT);

        $table->addIndex('test_index')
            ->addColumn('nu')
            ->addColumn('text');
        $table->addConstraint('fk0', 'field5', 'temp_test_table2', 'id', 'SET NULL', 'CASCADE');

        $expectedResult = array(
            "CREATE TABLE `test_query_table` (
\t`id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
\t`nu` REAL NOT NULL DEFAULT '2345',
\t`text` TEXT COLLATE NOCASE,
\t`int` INTEGER DEFAULT '0',
\t`field5` INTEGER,
\tCONSTRAINT `fk0` FOREIGN KEY (`field5`) REFERENCES `temp_test_table2` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
)",
            'CREATE INDEX `test_index` ON `test_query_table` ( `nu`, `text` )'
        );
        $this->assertEquals($expectedResult, $table->getMigrationQueries(), 'Wrong queries built');
    }

    public function testAlterQueryExceptions()
    {

        $driver = $this->getSqliteServer()
            ->getDbDriver();

        $driver->dropTable('test_table1');
        $driver->dropTable('test_table2');

        $table1 = $driver->addTable('test_table1');
        $table2 = $driver->addTable('test_table2');

        $table1->addColumn('test1', IColumnScheme::TYPE_INT);
        $table1->addColumn('test2', IColumnScheme::TYPE_VARCHAR);

        $table2->addColumn('test1', IColumnScheme::TYPE_SERIAL);
        $table2->addColumn('test2', IColumnScheme::TYPE_VARCHAR);
        $table2->addColumn('test3', IColumnScheme::TYPE_INT);

        $table2->setPrimaryKey('test1');
        $table2->addIndex('test2')
            ->addColumn('test2');
        $table2->addIndex('test3')
            ->addColumn('test3');

        $table2->addConstraint('fk0', 'test3', 'test_table1', 'test1');
        $table2->addConstraint('fk1', 'test2', 'test_table1', 'test2');

        $driver->applyMigrations();

        $table = $driver->getTable('test_table2')
            ->deleteConstraint('fk0');
        $e = null;
        try {
            $table->getMigrationQueries();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf('umi\dbal\exception\RuntimeException', $e);
        $this->assertEquals('Sqlite driver does not support foreign keys deleting.', $e->getMessage());
        $driver->reset();

        $table = $driver->getTable('test_table2')
            ->deleteIndex('test2');
        $e = null;
        try {
            $table->getMigrationQueries();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf('umi\dbal\exception\RuntimeException', $e);
        $this->assertEquals('Sqlite driver does not support keys deleting.', $e->getMessage());
        $driver->reset();

        $table = $driver->getTable('test_table2');
        $table->getIndex('test2')
            ->addColumn('test3');
        $e = null;
        try {
            $table->getMigrationQueries();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf('umi\dbal\exception\RuntimeException', $e);
        $this->assertEquals('Sqlite driver does not support keys modification.', $e->getMessage());
        $driver->reset();

        $table = $driver->getTable('test_table2');
        $table->getConstraint('fk0')
            ->setOnDeleteAction('CASCADE');
        $e = null;
        try {
            $table->getMigrationQueries();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf('umi\dbal\exception\RuntimeException', $e);
        $this->assertEquals('Sqlite driver does not support foreign keys modification.', $e->getMessage());
        $driver->reset();

        $table = $driver->getTable('test_table2')
            ->deletePrimaryKey();
        $e = null;
        try {
            $table->getMigrationQueries();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf('umi\dbal\exception\RuntimeException', $e);
        $this->assertEquals('Sqlite driver does not support primary key deleting.', $e->getMessage());
        $driver->reset();

        $table = $driver->getTable('test_table2')
            ->deleteColumn('test2');
        $e = null;
        try {
            $table->getMigrationQueries();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf('umi\dbal\exception\RuntimeException', $e);
        $this->assertEquals('Sqlite driver does not support columns deleting.', $e->getMessage());
        $driver->reset();

        $table = $driver->getTable('test_table2');
        $table->getColumn('test2')
            ->setType(IColumnScheme::TYPE_INT);
        $e = null;
        try {
            $table->getMigrationQueries();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf('umi\dbal\exception\RuntimeException', $e);
        $this->assertEquals('Sqlite driver does not support columns modification.', $e->getMessage());
        $driver->reset();

        $table = $driver->getTable('test_table1');
        $table->setPrimaryKey('test1');
        $e = null;
        try {
            $table->getMigrationQueries();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf('umi\dbal\exception\RuntimeException', $e);
        $this->assertEquals('Sqlite driver does not support primary key adding.', $e->getMessage());
        $driver->reset();

        $table = $driver->getTable('test_table1');
        $table->addConstraint('fk0', 'test1', 'test_table2', 'test3');
        $e = null;
        try {
            $table->getMigrationQueries();
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf('umi\dbal\exception\RuntimeException', $e);
        $this->assertEquals('Sqlite driver does not support foreign keys adding.', $e->getMessage());
        $driver->reset();

        $driver->dropTable('test_table1');
        $driver->dropTable('test_table2');
    }

    public function testGenerateAlterQuery()
    {
        $dbDriver = $this->getSqliteServer()
            ->getDbDriver();
        $dbDriver->modify(
            'CREATE TABLE IF NOT EXISTS `test_query_table` (`id` INTEGER PRIMARY KEY, `field1` TEXT, `field2` TEXT)'
        );
        $table = $dbDriver->getTable('test_query_table');
        $table->addColumn('field', IColumnScheme::TYPE_REAL);
        $table->addIndex('test_index')
            ->addColumn('field1');

        $expectedResult = array(
            'ALTER TABLE `test_query_table` ADD COLUMN `field` REAL',
            'CREATE INDEX `test_index` ON `test_query_table` ( `field1` )'
        );
        $this->assertEquals($expectedResult, $table->getMigrationQueries(), 'Wrong queries built');

        $dbDriver->modify('DROP TABLE test_query_table');
    }

    public function testGenerateDropQuery()
    {
        $dbDriver = $this->getSqliteServer()
            ->getDbDriver();
        $dbDriver->modify(
            'CREATE TABLE IF NOT EXISTS `temp_test_table` (`id` INTEGER PRIMARY KEY, `field1` TEXT, `field2` TEXT)'
        );
        $table = $dbDriver->getTable('temp_test_table');
        $dbDriver->deleteTable('temp_test_table');
        $expectedResult = array("DROP TABLE IF EXISTS `temp_test_table`");

        $this->assertEquals($expectedResult, $table->getMigrationQueries(), 'Wrong query built');

        $dbDriver->modify('DROP TABLE `temp_test_table`');
    }
}
