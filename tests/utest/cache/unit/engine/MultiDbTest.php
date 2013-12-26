<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\cache\unit\engine;

use umi\cache\engine\Db;
use umi\dbal\cluster\server\IServer;

/**
 * Тесты, предполагающие наличие в окружении более, чем одного сервера БД
 */
class MultiDbTest extends DbTest
{
    /**
     * @var IServer $nonDefaultServer
     */
    private $nonDefaultServer;
    /**
     * @var Db $mysqlStorage
     */
    private $mysqlStorage;
    /**
     * @var Db $sqliteStorage
     */
    private $sqliteStorage;

    protected function setUpFixtures()
    {
        $defaultServer = $this->getDefaultDbServer();
        $mysqlServer = $this->getMysqlServer();
        $sqliteServer = $this->getSqliteServer();

        $this->nonDefaultServer = $mysqlServer === $defaultServer ? $sqliteServer : $mysqlServer;

        $this->setupCacheDatabase($this->tableName, $this->getDefaultDbServer());
        $this->setupCacheDatabase($this->tableName, $this->nonDefaultServer);

        $options = [
            'table'=>$this->tableConfig($this->tableName),
            'serverId' => $mysqlServer->getId()
        ];
        $this->mysqlStorage = new Db($options);
        $this->resolveOptionalDependencies($this->mysqlStorage);

        $options = [
            'table'=>$this->tableConfig($this->tableName),
            'serverId' => $sqliteServer->getId()
        ];
        $this->sqliteStorage = new Db($options);
        $this->resolveOptionalDependencies($this->sqliteStorage);

        $options = [
            'table'=>$this->tableConfig($this->tableName),
        ];
        $this->defaultStorage = new Db($options);
        $this->resolveOptionalDependencies($this->defaultStorage);

    }

    protected function tearDownFixtures()
    {
        $this
            ->getDefaultDbServer()
            ->getConnection()
            ->getSchemaManager()
            ->dropTable($this->tableName);
        $this
            ->nonDefaultServer
            ->getConnection()
            ->getSchemaManager()
            ->dropTable($this->tableName);
    }

    public function testGetServer()
    {
        $this->mysqlStorage->set('first', $this->getMysqlServer()->getId());
        $this->sqliteStorage->set('first', $this->getSqliteServer()->getId());
        $this->defaultStorage->set('second', $this->getDefaultDbServer()->getId());

        $recordsNonDefault = $this->nonDefaultServer
            ->select(['key', 'cacheValue'])
            ->from($this->tableName)
            ->execute()
            ->fetchAll();

        $this->assertEquals(
            [
                ['key' => 'first', 'cacheValue' => $this->nonDefaultServer->getId()]
            ],
            $recordsNonDefault,
            'В сервере, не назначенном по умолчанию, должна быть только одна запись'
        );

        $recordsDefault = $this->getDefaultDbServer()
            ->select(['key', 'cacheValue'])
            ->from($this->tableName)
            ->execute()
            ->fetchAll();

        $this->assertEquals(
            [
                ['key' => 'first', 'cacheValue' => $this->getDefaultDbServer()->getId()],
                ['key' => 'second', 'cacheValue' => $this->getDefaultDbServer()->getId()]
            ],
            $recordsDefault,
            'В сервере по умолчанию должны быть 2 записи:'
            . ' одна, сохраненная «наугад» и одна явно сохраненная в сервер по умолчанию'
        );
    }
}
