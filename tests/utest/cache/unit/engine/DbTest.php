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
use utest\cache\CacheTestCase;

/**
 * Тест хранения кеша в бд
 */
class DbTest extends CacheTestCase
{
    /**
     * @var Db $storage
     */
    protected $defaultStorage;

    protected $tableName = 'test_cache_storage';

    protected function setUpFixtures()
    {
        $server = $this->getDefaultDbServer();

        $this->setupCacheDatabase($this->tableName, $server);

        $options = [
            'table'    => $this->tableConfig($this->tableName),
            'serverId' => $server->getId()
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
    }

    /**
     * Конфигурация таблицы БД для кеша
     * @param string $tableName
     *
     * @return array
     */
    protected function tableConfig($tableName)
    {
        return [
            'tableName'        => $tableName,
            'keyColumnName'    => 'key',
            'valueColumnName'  => 'cacheValue',
            'expireColumnName' => 'cacheExpiration'
        ];
    }

    public function testStorage()
    {
        $this->assertFalse($this->defaultStorage->get('testKey'), 'Значение уже есть в кеше');

        $this->assertTrue($this->defaultStorage->set('testKey', 'testValue', 3), 'Не удалось сохранить значение в кеш');
        $this->assertEquals('testValue', $this->defaultStorage->get('testKey'), 'В кеше хранится неверное значение');

        $this->assertTrue(
            $this->defaultStorage->set('testKey', 'newTestValue', 3),
            'Не удалось переопределить значение в кеше'
        );
        $this->assertFalse(
            $this->defaultStorage->add('testKey', 'newNewTestValue', 3),
            'Удалось переопределить значение в кеше'
        );
        $this->assertEquals(
            'newTestValue',
            $this->defaultStorage->get('testKey'),
            'В кеш добавилось неверное значение'
        );

        $this->assertTrue(
            $this->defaultStorage->add('newTestKey', 'testValue', 3),
            'Не удалось добавить значение в кеш'
        );
        $this->assertEquals(
            'testValue',
            $this->defaultStorage->get('newTestKey'),
            'В кеш добавилось неверное значение'
        );

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

        $this->assertFalse($this->defaultStorage->get('testKey'), 'Время кеша должно было истечь');

        $this->defaultStorage->set('testKey', 'newTestValue', 3);
        $this->assertTrue($this->defaultStorage->remove('testKey'), 'Не удалось удалить значение из кеша');
        $this->assertFalse($this->defaultStorage->get('testKey'), 'Значение в кеше существует после удаления');

        $this->defaultStorage->set('testKey1', 'testValue1', 3);
        $this->defaultStorage->set('testKey2', 'testValue2');
        $this->defaultStorage->set('testKey3', 'testValue3', 3);

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
            $this->defaultStorage->getList(['testKey1', 'testKey2', 'testKey3', 'testKey4']),
            'Неверное значение для массива ключей'
        );

        $this->assertTrue($this->defaultStorage->clear(), 'Не удалось очистить кеш');
        $this->assertEquals(
            ['testKey1' => false, 'testKey2' => false],
            $this->defaultStorage->getList(['testKey1', 'testKey2']),
            'Неверное значение для массива ключей после очистки кеша'
        );
    }
}
