<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\driver\sqlite;

use utest\TestCase;

/**
 * Тестирование колонки sqlite драйвера
 * @package
 */
class SqliteColumnTest extends TestCase
{

    protected function setUpFixtures()
    {

        $this->getSqliteServer()
            ->getDbDriver()
            ->modify('DROP TABLE IF EXISTS `test_sqlite_column`');
    }

    protected function tearDownFixtures()
    {
        $this->getSqliteServer()
            ->getDbDriver()
            ->modify('DROP TABLE IF EXISTS `test_sqlite_column`');

    }

    public function testColumnProperties()
    {
        $dbDriver = $this->getSqliteServer()
            ->getDbDriver();
        $dbDriver->modify(
            'CREATE TABLE test_sqlite_column (id INTEGER PRIMARY KEY AUTOINCREMENT, t TEXT, nu NUMERIC NOT NULL DEFAULT 2345, no BLOB, re REAL)'
        );

        $table = $dbDriver->getTable('test_sqlite_column');

        $column = $table->getColumn('id');
        $this->assertEquals('id', $column->getName(), 'Wrong table name');
        $this->assertTrue($column->getIsNullable(), 'Primary key is not nullable');
        $this->assertTrue($column->getIsPk(), 'Primary key column is not primary key');
        $this->assertTrue($column->getIsAutoIncrement(), 'AutoIncrement column has no increment');
        $this->assertNull($column->getDefaultValue(), 'Default value is not null');

        $column2 = $table->getColumn('t');
        $this->assertTrue($column2->getIsNullable(), 'Nullable column is not nullable');
        $this->assertFalse($column2->getIsPk(), 'Not Primary key column treated like primary key');
        $this->assertFalse($column2->getIsAutoIncrement(), 'Column without AutoIncrement has an increment');

        $column3 = $table->getColumn('nu');
        $this->assertFalse($column3->getIsNullable(), 'NotNullable column is nullable');
    }

    public function testColumnDefaultValues()
    {

        $dbDriver = $this->getSqliteServer()
            ->getDbDriver();
        $dbDriver->modify(
            'CREATE TABLE test_sqlite_column (column1 TEXT DEFAULT \'test\', column2 TEXT DEFAULT "test", column3 TEXT DEFAULT test, column4 TEXT DEFAULT \'"test"\')'
        );

        $table = $dbDriver->getTable('test_sqlite_column');

        $this->assertEquals(
            'test',
            $table->getColumn('column1')
                ->getDefaultValue()
        );
        $this->assertEquals(
            'test',
            $table->getColumn('column2')
                ->getDefaultValue()
        );
        $this->assertEquals(
            'test',
            $table->getColumn('column3')
                ->getDefaultValue()
        );
        $this->assertEquals(
            '"test"',
            $table->getColumn('column4')
                ->getDefaultValue()
        );

    }

    public function testColumnDefaultValues2()
    {

        $dbDriver = $this->getSqliteServer()
            ->getDbDriver();
        $dbDriver->modify(
            "CREATE TABLE test_sqlite_column (column1 TEXT DEFAULT \"test\", column2 TEXT DEFAULT 'test', column3 TEXT DEFAULT test, column4 TEXT DEFAULT \"'test'\")"
        );
        $table = $dbDriver->getTable('test_sqlite_column');
        $this->assertEquals(
            'test',
            $table->getColumn('column1')
                ->getDefaultValue()
        );
        $this->assertEquals(
            'test',
            $table->getColumn('column2')
                ->getDefaultValue()
        );
        $this->assertEquals(
            'test',
            $table->getColumn('column3')
                ->getDefaultValue()
        );
        $this->assertEquals(
            "'test'",
            $table->getColumn('column4')
                ->getDefaultValue()
        );
    }
}