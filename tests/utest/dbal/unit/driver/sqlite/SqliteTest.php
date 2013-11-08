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
 * Тестирование sqlite драйвера
 * @package
 */
class SqliteTest extends TestCase
{

    protected function setUpFixtures()
    {
        $dbDriver = $this->getSqliteServer()
            ->getDbDriver();
        $dbDriver->modify('DROP TABLE IF EXISTS `temp_test_table`');
        $dbDriver->modify(
            'CREATE TABLE `temp_test_table` (`id` INTEGER PRIMARY KEY, `field1` TEXT, `field2` TEXT)'
        );
    }

    protected function tearDownFixtures()
    {
        $this->getSqliteServer()
            ->getDbDriver()
            ->modify('DROP TABLE IF EXISTS `temp_test_table`');
    }

    public function testSqlite()
    {

        $dbDriver = $this->getSqliteServer()
            ->getDbDriver();
        $this->assertContains('temp_test_table', $dbDriver->getTableNames(), 'Неудалось получить имена таблиц');

        $table = $dbDriver->getTable('temp_test_table');
        $this->assertInstanceOf('umi\dbal\driver\sqlite\SqliteTable', $table);

        $sql = $dbDriver->buildTruncateQuery('temp_test_table');
        $this->assertEquals('DELETE FROM `temp_test_table`', $sql);

        $dbDriver = $this->getSqliteServer()
            ->getDbDriver();
        $this->assertTrue(
            $dbDriver->disableForeignKeysCheck(),
            'Запрос на отключения проверок внешних ключей не прошел'
        );
        $this->assertTrue(
            $dbDriver->enableForeignKeysCheck(),
            'Запрос на включение проверок внешних ключей не прошел'
        );

        $e = null;
        try {
            $dbDriver->disableKeys('temp_test_table');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\dbal\exception\RuntimeException',
            $e,
            'Sqlite не поддерживает отключение индексации'
        );

        $e = null;
        try {
            $dbDriver->enableKeys('temp_test_table');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\dbal\exception\RuntimeException',
            $e,
            'Sqlite не поддерживает отключение индексации'
        );
    }
}
