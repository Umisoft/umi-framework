<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\driver\sqlite;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\TableDiff;
use umi\dbal\driver\IDialect;
use utest\dbal\DbalTestCase;

/**
 * Тестирование sqlite драйвера
 * @package
 */
class SqliteTest extends DbalTestCase
{

    protected function setUpFixtures()
    {
        $dbDriver = $this
            ->getSqliteServer()
            ->getConnection();
        $dbDriver->exec('DROP TABLE IF EXISTS "temp_test_table"');
        $dbDriver->exec('CREATE TABLE "temp_test_table" ("id" INTEGER PRIMARY KEY, "field1" TEXT, "field2" TEXT)');
    }

    protected function tearDownFixtures()
    {
        $this
            ->getSqliteServer()
            ->getConnection()
            ->exec('DROP TABLE IF EXISTS "temp_test_table"');
    }

    public function testSqlite()
    {
        $dbDriver = $this
            ->getSqliteServer()
            ->getConnection();
        /** @var $dialect IDialect|AbstractPlatform */
        $dialect = $dbDriver->getDatabasePlatform();

        $listTables = array_map(
            function (Table $table) {
                return $table->getName();
            },
            $dbDriver
                ->getSchemaManager()
                ->listTables()
        );
        $this->assertContains('temp_test_table', $listTables, 'Не удалось получить имена таблиц');

        $sql = $dbDriver
            ->getDatabasePlatform()
            ->getTruncateTableSQL($dialect->quoteIdentifier('temp_test_table'));
        $this->assertEquals('DELETE FROM "temp_test_table"', $sql);
        $disableSql = $dialect->getDisableForeignKeysSQL();
        $this->assertEquals(0, $dbDriver->exec($disableSql), 'Запрос на отключения проверок внешних ключей не прошел');
        $enableSql = $dialect->getEnableForeignKeysSQL();
        $this->assertEquals(0, $dbDriver->exec($enableSql), 'Запрос на включение проверок внешних ключей не прошел');

        $e = null;
        try {
            $sql = $dialect->getDisableKeysSQL('temp_test_table');
            $dbDriver->exec($sql);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\dbal\exception\RuntimeException',
            $e,
            'Sqlite не поддерживает отключение индексации'
        );

        $e = null;
        try {
            $sql = $dialect->getEnableKeysSQL('temp_test_table');
            $dbDriver->exec($sql);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\dbal\exception\RuntimeException',
            $e,
            'Sqlite не поддерживает отключение индексации'
        );
    }

    /**
     * @param $table
     * @param $newTable
     * @internal param $dbDriver
     * @return TableDiff
     */
    protected function diff($table, $newTable)
    {
        $dbDriver = $this
            ->getMysqlServer()
            ->getConnection();
        $comparator = new Comparator();
        $actualResult = $dbDriver
            ->getDatabasePlatform()
            ->getAlterTableSQL($comparator->diffTable($table, $newTable));

        return $actualResult;
    }
}
