<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\driver\sqlite;

use utest\dbal\DbalTestCase;

/**
 * Тестирование внешних ключей sqlite драйвера
 */
class SqliteConstraintsTest extends DbalTestCase
{

    protected function setUpFixtures()
    {
        $dbDriver = $this->getSqliteServer()
            ->getDbDriver();

        $dbDriver->modify('DROP TABLE IF EXISTS `temp_test_table`');
        $dbDriver->modify('DROP TABLE IF EXISTS `temp_test_table2`');
        $dbDriver->modify('DROP TABLE IF EXISTS `temp_test_table3`');

        $dbDriver->modify('CREATE TABLE `temp_test_table` (`id` INTEGER PRIMARY KEY, `field1` TEXT)');
        $dbDriver->modify('CREATE TABLE `temp_test_table3` (`id` INTEGER PRIMARY KEY, `field3` TEXT)');
        $dbDriver->modify(
            'CREATE TABLE `temp_test_table2` (
                `id` INTEGER PRIMARY KEY,
                `field2` INTEGER,
                `field4` INTEGER,
                CONSTRAINT `fk1` FOREIGN KEY (`field2`) REFERENCES `temp_test_table` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
                CONSTRAINT `fk0` FOREIGN KEY (`field4`) REFERENCES `temp_test_table3` (`id`)
            )'
        );
    }

    protected function tearDownFixtures()
    {
        $dbDriver = $this->getSqliteServer()
            ->getDbDriver();

        $dbDriver->modify('DROP TABLE IF EXISTS `temp_test_table`');
        $dbDriver->modify('DROP TABLE IF EXISTS `temp_test_table2`');
        $dbDriver->modify('DROP TABLE IF EXISTS `temp_test_table3`');
    }

    public function testTableConstraints()
    {

        $table = $this->getSqliteServer()
            ->getDbDriver()
            ->getTable('temp_test_table2');

        $constraints = $table->getConstraints();
        $this->assertEquals($constraints, $table->getConstraints(), 'Ожидается, что внешние ключи загружаются 1 раз');
        $this->assertCount(2, $constraints, 'Неверное количество внешних ключей в таблице');

        $constraint1 = $table->getConstraint('fk1');
        $this->assertEquals('fk1', $constraint1->getName());
        $this->assertEquals('field2', $constraint1->getColumnName());
        $this->assertEquals('temp_test_table', $constraint1->getReferenceTableName());
        $this->assertEquals('id', $constraint1->getReferenceColumnName());
        $this->assertEquals('SET NULL', $constraint1->getOnDeleteAction());
        $this->assertEquals('CASCADE', $constraint1->getOnUpdateAction());
        $this->assertFalse($constraint1->getIsDeleted());
        $this->assertFalse($constraint1->getIsNew());
        $this->assertFalse($constraint1->getIsModified());

        $constraint2 = $table->getConstraint('fk0');
        $this->assertEquals('fk0', $constraint2->getName());
        $this->assertEquals('field4', $constraint2->getColumnName());
        $this->assertEquals('temp_test_table3', $constraint2->getReferenceTableName());
        $this->assertEquals('id', $constraint2->getReferenceColumnName());
        $this->assertEquals(
            'NO ACTION',
            $constraint2->getOnDeleteAction(),
            'В sqlite по умолчанию действия индекса - NO ACTION'
        );
        $this->assertEquals(
            'NO ACTION',
            $constraint2->getOnUpdateAction(),
            'В sqlite по умолчанию действия индекса - NO ACTION'
        );

    }
}