<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\cache\unit\toolbox;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use umi\cache\toolbox\CacheTools;
use utest\cache\CacheTestCase;
use utest\cache\mock\MockCacheAware;

class CacheToolsTest extends CacheTestCase
{
    /**
     * @var Connection $connection
     */
    private $connection;

    private $tableName = 'test_cache';

    protected function setUpFixtures()
    {
        $this->connection = $this->getDbServer()->getConnection();
    }

    protected function tearDownFixtures()
    {
        $this->connection->getSchemaManager()
            ->dropTable($this->tableName);
    }

    public function testCacheInjection()
    {
        $this->getTestToolkit()->setSettings(
            [
                CacheTools::NAME => [
                    'type' => CacheTools::TYPE_DB,
                    'options' => [
                        'table'    => [
                            'tableName'        => $this->tableName,
                            'keyColumnName'    => 'key',
                            'valueColumnName'  => 'cacheValue',
                            'expireColumnName' => 'cacheExpiration'
                        ],
                        'serverId' => $this->getDbServer()->getId()
                    ]
                ]
            ]
        );

        $this->setupDatabase($this->tableName);

        $cachingService = new MockCacheAware();
        $this->resolveOptionalDependencies($cachingService);

        $callsCounter = 0;
        $value = $cachingService->get(
            'test_closure',
            function () use (&$callsCounter) {
                $callsCounter++;

                return $callsCounter;
            }
        );
        $this->assertEquals(
            1,
            $value,
            'Ожидается, что, если значение еще не было закешировано, выполнится функция, '
            . 'вычисляющая значение для кеша'
        );

        $newValue = $cachingService->get(
            'test_closure',
            function () use (&$callsCounter) {
                $callsCounter++;

                return $callsCounter;
            }
        );
        $this->assertEquals(
            1,
            $newValue,
            'Ожидается, что, если значение было закешировано, функция, вычисляющая значение для кеша, '
            . 'не выполнится'
        );

    }

    public function testCacheNoInjection()
    {

        $this->getTestToolkit()->setSettings(
            [
                CacheTools::NAME => [
                    'type' => null
                ]
            ]
        );

        $cachingService = new MockCacheAware();
        $this->resolveOptionalDependencies($cachingService);

        $callsCounter = 0;
        $value = $cachingService->get(
            'test_closure',
            function () use (&$callsCounter) {
                $callsCounter++;

                return $callsCounter;
            }
        );
        $this->assertEquals(
            1,
            $value,
            'Ожидается, что, если кеш не был внедрен, функция, вычисляющая значение для кеша, выполнится всегда'
        );

        $newValue = $cachingService->get(
            'test_closure',
            function () use (&$callsCounter) {
                $callsCounter++;

                return $callsCounter;
            }
        );
        $this->assertEquals(
            2,
            $newValue,
            'Ожидается, что, если кеш не был внедрен, функция, вычисляющая значение для кеша, выполнится всегда'
        );
    }
}
