<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\cache\unit;

use umi\cache\Cache;
use umi\cache\engine\Db;
use umi\dbal\cluster\IConnection;
use umi\dbal\driver\IColumnScheme;
use utest\cache\mock\CacheTestFixture;
use utest\cache\mock\Component;
use utest\TestCase;

/**
 * Тестирование cache-frontend'a
 * @package
 */
class CacheTest extends TestCase
{

    private $tableName = 'test_cache';
    /**
     * @var IConnection
     */
    private $connection;
    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * @var Db $storage
     */
    private $storage;

    protected function setUpFixtures()
    {

        $this->connection = $this->getDbServer();

        $options = [
            'table'    => [
                'tableName'        => $this->tableName,
                'keyColumnName'    => 'key',
                'valueColumnName'  => 'cacheValue',
                'expireColumnName' => 'cacheExpiration'
            ],
            'serverId' => $this->connection->getId()
        ];

        $this->storage = new Db($options);
        $this->resolveOptionalDependencies($this->storage);

        $driver = $this->connection->getDbDriver();
        $table = $driver->addTable($this->tableName);
        $table->addColumn('key', IColumnScheme::TYPE_VARCHAR, [IColumnScheme::OPTION_COMMENT => 'Cache unique key']);
        $table->addColumn('cacheValue', IColumnScheme::TYPE_BLOB, [IColumnScheme::OPTION_COMMENT => 'Cache value']);
        $table->addColumn(
            'cacheExpiration',
            IColumnScheme::TYPE_INT,
            [IColumnScheme::OPTION_COMMENT => 'Cache expire timestamp', IColumnScheme::OPTION_UNSIGNED => true]
        );

        $table->setPrimaryKey('key');
        $table->addIndex('expire')
            ->addColumn('cacheExpiration');
        $driver->applyMigrations();

        $this->cache = new Cache($this->storage);

    }

    protected function tearDownFixtures()
    {
        $this->connection->getDbDriver()
            ->dropTable($this->tableName);
    }

    public function testCacheSimple()
    {
        /**
         * @var Cache $cache
         */
        $cache = $this->cache;

        // invalid cache
        $this->assertNull($cache->get('invalid_key'));
        // int
        $cache->set('test_int', 100);
        $this->assertSame(100, $cache->get('test_int'));
        // float
        $cache->set('test_float', 100.5005);
        $this->assertSame(100.5005, $cache->get('test_float'));
        // strings
        $cache->set('test_str', 'somestr');
        $this->assertSame('somestr', $cache->get('test_str'));
        // array
        $cache->set('test_array', array('boo', 1));
        $this->assertSame(array('boo', 1), $cache->get('test_array'));
        // bool false
        $cache->set('test_bool_false', false);
        $this->assertSame(false, $cache->get('test_bool_false'));
        // bool true
        $cache->set('test_bool_true', true);
        $this->assertSame(true, $cache->get('test_bool_true'));
        // object
        $object = new CacheTestFixture();
        $data = [uniqid(''), 3];
        $object->setData($data);
        $cache->set('test_object', $object);
        /**
         * @var CacheTestFixture $testObject1
         */
        $testObject1 = $cache->get('test_object');
        $this->assertInstanceOf('utest\cache\mock\CacheTestFixture', $testObject1);
        $this->assertEquals($data, $testObject1->getData());

        $testObject2 = $cache->get('test_object');
        $this->assertEquals($testObject1, $testObject2);
    }

    public function testCacheClosure()
    {
        /**
         * @var Cache $cache
         */
        $cache = $this->cache;

        $context = new Component();

        $callsCounter = 0;
        $value = $cache->algorithm(
            'test_closure',
            function () use (&$callsCounter, $context) {
                $callsCounter++;

                return $context->doSomething();
            },
            0
        );

        $this->assertSame('something', $value);
        $this->assertEquals(1, $callsCounter);

        $value = $cache->algorithm(
            'test_closure',
            function () use (&$callsCounter, $context) {
                $callsCounter++;

                return $context->doSomething();
            },
            0
        );

        $this->assertSame('something', $value);
        $this->assertEquals(1, $callsCounter);

    }

    public function testTaggedCache()
    {
        /**
         * @var Cache $cache
         */
        $cache = $this->cache;
        $cache->set('test_key', 'test_value');
        $this->assertSame('test_value', $cache->get('test_key'));
        $this->assertSame(
            null,
            $cache->get('test_key', ['tag1', 'tag2']),
            'Ожидается, что кеш невалидный, так как теги не были установлены'
        );

        $cache->set('test_key2', 'test_value2');
        $this->assertSame(
            'test_value2',
            $cache->get('test_key2', ['tag1', 'tag2']),
            'Ожидается, что кеш валидный, так как теги были установлены после предыдущей проверки'
        );

        $cache->invalidateTags(['tag1'], time() - 10);
        $cache->invalidateTags(['tag2'], time() - 10);
        $this->assertSame(
            'test_value',
            $cache->get('test_key', ['tag1', 'tag2']),
            'Ожидается, что кеш валидный, так как теги были выставлены раньше записи кеша'
        );

        $cache->set('test_key', 'new_test_value');
        $this->assertSame(
            'new_test_value',
            $cache->get('test_key', ['tag1', 'tag2']),
            'Ожидается, что кеш валидный, так как кеш был выставлен после выставления тегов'
        );

        $this->assertSame(
            null,
            $cache->get('test_key', ['tag1', 'tag2', 'tag3']),
            'Ожидается, что кеш невалидный, когда не было хотя бы одного тега'
        );

        $cache->invalidateTags(['tag1']);
        $this->assertSame(
            null,
            $cache->get('test_key', ['tag1', 'tag2']),
            'Ожидается, что кеш невалидный, когда хотя бы один тег уже не валидный'
        );

        $cache->invalidateTags(['tag2']);
        $this->assertSame(
            null,
            $cache->get('test_key', ['tag1', 'tag2']),
            'Ожидается, что кеш невалидный, когда все теги уже не валидны'
        );

    }

}

