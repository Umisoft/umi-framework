<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\cache\unit\engine;

use Doctrine\DBAL\Types\Type;
use umi\cache\engine\Db;
use utest\cache\CacheTestCase;

/**
 * Тест хранения кеша в бд
 */
class DbTest extends CacheTestCase
{
    /**
     * @var Db $storage
     */
    private $storage;

    private $tableName = 'test_cache_storage';

    protected function setUpFixtures()
    {
        $server = $this->getDefaultDbServer();
        $options = [
            'table'    => [
                'tableName'        => $this->tableName,
                'keyColumnName'    => 'key',
                'valueColumnName'  => 'cacheValue',
                'expireColumnName' => 'cacheExpiration'
            ],
            'serverId' => $server->getId()
        ];

        $this->setupCacheDatabase($this->tableName, $server);

        $this->storage = new Db($options);
        $this->resolveOptionalDependencies($this->storage);
    }

    protected function tearDownFixtures()
    {
        $this
            ->getDefaultDbServer()
            ->getConnection()
            ->getSchemaManager()
            ->dropTable($this->tableName);
    }

    public function testGetServer()
    {
        $this->markTestIncomplete();

        $defaultServer = $this
            ->getDbCluster()
            ->getMaster();
        $nonDefaultServer = $this
            ->getMysqlServer()
            ->getId() == $defaultServer->getId()
            ? $this->getSqliteServer()
            : $this->getMysqlServer();

        $this->setupCacheDatabase($this->tableName, $nonDefaultServer);

        $tableConfig = [
            'tableName'        => $this->tableName,
            'keyColumnName'    => 'key',
            'valueColumnName'  => 'cacheValue',
            'expireColumnName' => 'cacheExpiration'
        ];
        $dbMysql = new Db([
            'table'    => $tableConfig,
            'serverId' => $this
                ->getMysqlServer()
                ->getId()
        ]);
        $this->resolveOptionalDependencies($dbMysql);
        $dbMysql->set('first', 'mysqlMaster');

        $dbSqlite = new Db([
            'table'    => $tableConfig,
            'serverId' => $this
                ->getSqliteServer()
                ->getId()
        ]);
        $this->resolveOptionalDependencies($dbSqlite);
        $dbSqlite->set('first', 'sqliteMaster');

        $dbDefault = new Db([
            'table' => $tableConfig,
        ]);
        $this->resolveOptionalDependencies($dbDefault);
        $dbDefault->set('second', $defaultServer->getId());

        $recordsNonDefault = $nonDefaultServer
            ->select(['key', 'cacheValue'])
            ->from($this->tableName)
            ->execute()
            ->fetchAll();

        $this->assertEquals(
            [
                ['key' => 'first', 'cacheValue' => $nonDefaultServer->getId()]
            ],
            $recordsNonDefault
        );

        $recordsDefault = $defaultServer
            ->select(['key', 'cacheValue'])
            ->from($this->tableName)
            ->execute()
            ->fetchAll();
        $this->assertEquals(
            [
                ['key' => 'first', 'cacheValue' => $defaultServer->getId()],
                ['key' => 'second', 'cacheValue' => $defaultServer->getId()]
            ],
            $recordsDefault
        );

        $nonDefaultServer
            ->getConnection()
            ->getSchemaManager()
            ->dropTable($this->tableName);
    }

    public function testStorage()
    {
        $this->assertFalse($this->storage->get('testKey'), 'Значение уже есть в кеше');

        $this->assertTrue($this->storage->set('testKey', 'testValue', 3), 'Не удалось сохранить значение в кеш');
        $this->assertEquals('testValue', $this->storage->get('testKey'), 'В кеше хранится неверное значение');

        $this->assertTrue(
            $this->storage->set('testKey', 'newTestValue', 3),
            'Не удалось переопределить значение в кеше'
        );
        $this->assertFalse(
            $this->storage->add('testKey', 'newNewTestValue', 3),
            'Удалось переопределить значение в кеше'
        );
        $this->assertEquals('newTestValue', $this->storage->get('testKey'), 'В кеш добавилось неверное значение');

        $this->assertTrue($this->storage->add('newTestKey', 'testValue', 3), 'Не удалось добавить значение в кеш');
        $this->assertEquals('testValue', $this->storage->get('newTestKey'), 'В кеш добавилось неверное значение');

        $update = $this
            ->getDefaultDbServer()
            ->update('test_cache_storage');
        $update
            ->set('cacheExpiration', ':expire')
            ->where()
            ->expr('key', '=', ':id');
        $update
            ->bindInt(':expire', time() - 1000)
            ->bindString(':id', 'testKey');
        $update->execute();

        $this->assertFalse($this->storage->get('testKey'), 'Время кеша должно было истечь');

        $this->storage->set('testKey', 'newTestValue', 3);
        $this->assertTrue($this->storage->remove('testKey'), 'Не удалось удалить значение из кеша');
        $this->assertFalse($this->storage->get('testKey'), 'Значение в кеше существует после удаления');

        $this->storage->set('testKey1', 'testValue1', 3);
        $this->storage->set('testKey2', 'testValue2');
        $this->storage->set('testKey3', 'testValue3', 3);

        $update = $this
            ->getDefaultDbServer()
            ->update('test_cache_storage');
        $update
            ->set('cacheExpiration', ':expire')
            ->where()
            ->expr('key', '=', ':id');
        $update
            ->bindInt(':expire', time() - 1000)
            ->bindString(':id', 'testKey3');
        $update->execute();

        $expectedResult = [
            'testKey1' => 'testValue1',
            'testKey2' => 'testValue2',
            'testKey3' => false,
            'testKey4' => false
        ];
        $this->assertEquals(
            $expectedResult,
            $this->storage->getList(['testKey1', 'testKey2', 'testKey3', 'testKey4']),
            'Неверное значение для массива ключей'
        );

        $this->assertTrue($this->storage->clear(), 'Не удалось очистить кеш');
        $this->assertEquals(
            ['testKey1' => false, 'testKey2' => false],
            $this->storage->getList(['testKey1', 'testKey2']),
            'Неверное значение для массива ключей после очистки кеша'
        );
    }
}
