<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\cluster\server;

use Doctrine\DBAL\DriverManager;
use umi\dbal\driver\dialect\SqliteDialect;
use umi\dbal\toolbox\factory\ServerFactory;
use umi\dbal\toolbox\factory\QueryBuilderFactory;
use utest\dbal\DbalTestCase;

/**
 * Тест фабрики построителей запросов
 *
 */
class ServerFactoryTest extends DbalTestCase
{

    public function testQueryBuilderFactory()
    {

        $queryBuilderFactory = new QueryBuilderFactory();
        $serverFactory = new ServerFactory($queryBuilderFactory);

        $this->resolveOptionalDependencies($queryBuilderFactory);
        $this->resolveOptionalDependencies($serverFactory);

        $driver = DriverManager::getConnection(['driver' => 'pdo_sqlite']);

        $e = null;
        try {
            $serverFactory->create('wrongType', $driver, new SqliteDialect(), 'wrongType');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\dbal\exception\RuntimeException',
            $e,
            'Ожидается исключение, если создается сервер с неизвестным типом'
        );

        $this->assertInstanceOf(
            'umi\dbal\cluster\server\IMasterServer',
            $serverFactory->create('sqlite', $driver, new SqliteDialect()),
            'Ожидается, что IServerFactory::create() по умолчанию вернет IMasterServer'
        );
        $this->assertInstanceOf(
            'umi\dbal\cluster\server\IMasterServer',
            $serverFactory->create('sqlite', $driver, new SqliteDialect(), 'master'),
            'Ожидается, что IServerFactory::create() вернет сервер заданного типа'
        );
        $this->assertInstanceOf(
            'umi\dbal\cluster\server\ISlaveServer',
            $serverFactory->create('sqlite', $driver, new SqliteDialect(), 'slave'),
            'Ожидается, что IServerFactory::create() вернет сервер заданного типа'
        );
        $this->assertInstanceOf(
            'umi\dbal\cluster\server\IShardServer',
            $serverFactory->create('sqlite', $driver, new SqliteDialect(), 'shard'),
            'Ожидается, что IServerFactory::create() вернет сервер заданного типа'
        );
    }
}
