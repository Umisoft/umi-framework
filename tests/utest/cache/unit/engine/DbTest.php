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
        $server = $this->getDbServer();
        $options = [
            'table'    => [
                'tableName'        => $this->tableName,
                'keyColumnName'    => 'key',
                'valueColumnName'  => 'cacheValue',
                'expireColumnName' => 'cacheExpiration'
            ],
            'serverId' => $server->getId()
        ];

        $this->setupDatabase($this->tableName);

        $this->storage = new Db($options);
        $this->resolveOptionalDependencies($this->storage);
    }

    protected function tearDownFixtures()
    {
        $this
            ->getDbServer()
            ->getConnection()
            ->getSchemaManager()
            ->dropTable($this->tableName);
    }

    public function testStorage()
    {
        $this->assertFalse($this->storage->get('testKey'), 'Значение уже есть в кеше');

        $this->assertTrue($this->storage->set('testKey', 'testValue', 1), 'Не удалось сохранить значение в кеш');
        $this->assertEquals('testValue', $this->storage->get('testKey'), 'В кеше хранится неверное значение');

        $this->assertTrue(
            $this->storage->set('testKey', 'newTestValue', 1),
            'Не удалось переопределить значение в кеше'
        );
        $this->assertFalse(
            $this->storage->add('testKey', 'newNewTestValue', 1),
            'Удалось переопределить значение в кеше'
        );
        $this->assertEquals('newTestValue', $this->storage->get('testKey'), 'В кеш добавилось неверное значение');

        $this->assertTrue($this->storage->add('newTestKey', 'testValue', 1), 'Не удалось добавить значение в кеш');
        $this->assertEquals('testValue', $this->storage->get('newTestKey'), 'В кеш добавилось неверное значение');

        $update = $this
            ->getDbServer()
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
            ->getDbServer()
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
