<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace utest\dbal;

use Doctrine\DBAL\Connection;
use utest\event\TEventSupport;
use utest\TestCase;

/**
 * Тест кейс для работы с БД
 */
abstract class DbalTestCase extends TestCase
{
    use TEventSupport;
    use TDbalSupport;

    /**
     * @var Connection $connection общее для всех тестов соединение с БД
     */
    protected $connection;
    /**
     * @var Connection $connection имя сервера БД, с которым устанавливается общее соединение
     */
    protected $usedServerId;
    /**
     * @var array $affectedTables таблицы, используемые в тесте, будут удалены в начале и конце каждого теста
     */
    protected $affectedTables = [];

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->registerEventTools();
        $this->registerDbalTools();

        if ($this->usedServerId === null) {
            $this->usedServerId = $this
                ->getDbCluster()
                ->getMaster()
                ->getId();
        }
        $this->connection = $this
            ->getDbCluster()
            ->getServer($this->usedServerId)
            ->getConnection();

        parent::setUp();
    }

    protected function tearDown()
    {
        foreach ($this->affectedTables as $tableName) {
            if ($this->connection
                ->getSchemaManager()
                ->tablesExist($tableName)
            ) {
                $this->connection
                    ->getSchemaManager()
                    ->dropTable($tableName);
            }
        }

        parent::tearDown();
    }
}
