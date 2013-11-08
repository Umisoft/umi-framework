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
 * Тестирование внешних ключей mysql драйвера
 */
class MySqlConstraintsTest extends TestCase
{

    protected function setUpFixtures()
    {

        $dbDriver = $this->getMysqlServer()
            ->getDbDriver();
        $dbDriver->modify('DROP TABLE IF EXISTS `temp_test_table`');
        $dbDriver->modify('DROP TABLE IF EXISTS `temp_test_related_table`');
        $dbDriver->modify(
            'CREATE TABLE `temp_test_related_table` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `is_default` int(10)NOT NULL,
                PRIMARY KEY (`id`),
                KEY `is_default` (`is_default`)
            ) ENGINE=innoDB DEFAULT CHARSET=utf8'
        );

        $dbDriver->modify(
            'CREATE TABLE `temp_test_table` (
                `is_default` int(10) NOT NULL,
                `default_lang_id` int(10) unsigned DEFAULT NULL,
                KEY `Domain to default language relation_FK` (`default_lang_id`),
                KEY `is_default` (`is_default`),
                CONSTRAINT `FK_test_1` FOREIGN KEY (`default_lang_id`) REFERENCES `temp_test_related_table` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
                CONSTRAINT `FK_test_3` FOREIGN KEY (`is_default`) REFERENCES `temp_test_related_table` (`is_default`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8'
        );

    }

    protected function tearDownFixtures()
    {

        $dbDriver = $this->getMysqlServer()
            ->getDbDriver();
        $dbDriver->modify('DROP TABLE IF EXISTS `temp_test_table`');
        $dbDriver->modify('DROP TABLE IF EXISTS `temp_test_related_table`');
    }

    public function testTableConstraints()
    {

        $table = $this->getMysqlServer()
            ->getDbDriver()
            ->getTable('temp_test_table');

        $constraints = $table->getConstraints();
        $this->assertEquals($constraints, $table->getConstraints(), 'Ожидается, что внешние ключи загружаются 1 раз');
        $this->assertCount(2, $constraints, 'Неверное количество внешних ключей в таблице');

        $e = null;
        try {
            $table->getConstraint('missed_constraint');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf('umi\dbal\exception\NonexistentEntityException', $e);

        $e = null;
        try {
            $table->deleteConstraint('missed_constraint');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf('umi\dbal\exception\NonexistentEntityException', $e);

        $constraint1 = $table->getConstraint('FK_test_1');
        $this->assertInstanceOf('umi\dbal\driver\IConstraintScheme', $constraint1);

        $this->assertEquals('FK_test_1', $constraint1->getName());
        $this->assertEquals('default_lang_id', $constraint1->getColumnName());
        $this->assertEquals('temp_test_related_table', $constraint1->getReferenceTableName());
        $this->assertEquals('id', $constraint1->getReferenceColumnName());
        $this->assertEquals('SET NULL', $constraint1->getOnDeleteAction());
        $this->assertEquals('CASCADE', $constraint1->getOnUpdateAction());
        $this->assertFalse($constraint1->getIsDeleted());
        $this->assertFalse($constraint1->getIsNew());
        $this->assertFalse($constraint1->getIsModified());

        $this->assertInstanceOf('umi\dbal\driver\ITableScheme', $table->deleteConstraint('FK_test_3'));

        $this->assertEquals(
            'FK_test_3',
            $table->getConstraint('FK_test_3')
                ->getName()
        );
        $this->assertEquals(
            'is_default',
            $table->getConstraint('FK_test_3')
                ->getColumnName()
        );
        $this->assertEquals(
            'temp_test_related_table',
            $table->getConstraint('FK_test_3')
                ->getReferenceTableName()
        );
        $this->assertEquals(
            'is_default',
            $table->getConstraint('FK_test_3')
                ->getReferenceColumnName()
        );
        $this->assertNull(
            $table->getConstraint('FK_test_3')
                ->getOnDeleteAction()
        );
        $this->assertNull(
            $table->getConstraint('FK_test_3')
                ->getOnUpdateAction()
        );
        $this->assertTrue(
            $table->getConstraint('FK_test_3')
                ->getIsDeleted()
        );
        $this->assertFalse(
            $table->getConstraint('FK_test_3')
                ->getIsNew()
        );
        $this->assertFalse(
            $table->getConstraint('FK_test_3')
                ->getIsModified()
        );

        $table->addConstraint(
            'FK_test_2',
            'is_default',
            'temp_test_related_table',
            'is_default',
            'NO ACTION',
            'NO ACTION'
        );
        $constraint = $table->getConstraint('FK_test_2');

        $this->assertInstanceOf('umi\dbal\driver\IConstraintScheme', $constraint);

        $this->assertEquals('FK_test_2', $constraint->getName());
        $this->assertEquals('is_default', $constraint->getColumnName());
        $this->assertEquals('temp_test_related_table', $constraint->getReferenceTableName());
        $this->assertEquals('is_default', $constraint->getReferenceColumnName());
        $this->assertEquals('NO ACTION', $constraint->getOnDeleteAction());
        $this->assertEquals('NO ACTION', $constraint->getOnUpdateAction());
        $this->assertTrue($constraint->getIsNew());
        $this->assertFalse($constraint->getIsDeleted());
        $this->assertTrue($constraint->getIsModified());

    }

}