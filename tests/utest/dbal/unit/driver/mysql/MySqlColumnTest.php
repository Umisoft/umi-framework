<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\driver\mysql;

use utest\TestCase;

/**
 * Тестирование колонки mysql драйвера
 * @package
 */
class MySqlColumnTest extends TestCase
{

    protected function setUpFixtures()
    {
        $this->getMysqlServer()
            ->getDbDriver()
            ->modify('DROP TABLE IF EXISTS `test_mysql_column`');
        $this->getMysqlServer()
            ->getDbDriver()
            ->modify(
            "CREATE TABLE `test_mysql_column` (
                            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `name` varchar(64) COMMENT 'Тестовый комментарий' DEFAULT NULL,
                            `is_multiple` tinyint(1) DEFAULT '0',
                            `text_val` mediumtext,
                            `float_val` double(5,2) unsigned zerofill DEFAULT NULL,
                            PRIMARY KEY (`id`)
                            )"
        );
    }

    protected function tearDownFixtures()
    {
        $this->getMysqlServer()
            ->getDbDriver()
            ->modify('DROP TABLE IF EXISTS `test_mysql_column`');
    }

    public function testColumnProperties()
    {

        $table = $this->getMysqlServer()
            ->getDbDriver()
            ->getTable('test_mysql_column');

        $column = $table->getColumn('id');
        $this->assertEquals('id', $column->getName(), 'Wrong table name');
        $this->assertEquals('int', $column->getInternalType(), 'Неверный внутренний тип');
        $this->assertEquals(10, $column->getLength(), 'Неверная длина');
        $this->assertNull($column->getDecimals(), 'Неверная информация о количестве знаков после запятой');
        $this->assertTrue($column->getIsUnsigned(), 'Неверный признак беззнаковости');
        $this->assertFalse($column->getIsZerofill(), 'Неверный признак заполнения нулями');
        $this->assertFalse($column->getIsNullable(), 'NotNullable column is nullable');
        $this->assertTrue($column->getIsPk(), 'Primary key column is not primary key');
        $this->assertTrue($column->getIsAutoIncrement(), 'AutoIncrement column has no increment');
        $this->assertNull($column->getDefaultValue(), 'Default value is not null');
        $this->assertNull($column->getCollation(), 'Collation for integer type');

        $column2 = $table->getColumn('name');
        $this->assertEquals('varchar', $column2->getInternalType(), 'Неверный внутренний тип');
        $this->assertEquals(64, $column2->getLength(), 'Неверная длина');
        $this->assertFalse($column2->getIsUnsigned(), 'Неверный признак беззнаковости');
        $this->assertFalse($column2->getIsZerofill(), 'Неверный признак заполнения нулями');
        $this->assertTrue($column2->getIsNullable(), 'Nullable column is not nullable');
        $this->assertFalse($column2->getIsPk(), 'Not Primary key column treated like primary key');
        $this->assertFalse($column2->getIsAutoIncrement(), 'Column without AutoIncrement has an increment');
        $this->assertEquals('Тестовый комментарий', $column2->getComment(), 'Wrong comment');
        $this->assertNotNull($column2->getCollation(), 'Collation for text column can not be empty');

        $column3 = $table->getColumn('is_multiple');
        $this->assertEquals(0, $column3->getDefaultValue(), 'Wrong default value');
        $this->assertTrue($column3->getIsNullable(), 'Nullable column is not nullable');

        $column4 = $table->getColumn('text_val');
        $this->assertEquals('text_val', $column4->getName());
        $this->assertEquals('mediumtext', $column4->getInternalType());
        $this->assertNull($column4->getLength());
        $this->assertNull($column4->getDecimals());
        $this->assertFalse($column4->getIsUnsigned());
        $this->assertFalse($column4->getIsZerofill());

        $column5 = $table->getColumn('float_val');
        $this->assertEquals('float_val', $column5->getName());
        $this->assertEquals('double', $column5->getInternalType());
        $this->assertEquals(5, $column5->getLength());
        $this->assertEquals(2, $column5->getDecimals());
        $this->assertTrue($column5->getIsUnsigned());
        $this->assertTrue($column5->getIsZerofill());

    }
}