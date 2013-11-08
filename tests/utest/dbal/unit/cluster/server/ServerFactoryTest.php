<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\dbal\unit\cluster\server;

use umi\dbal\driver\sqlite\SqliteDriver;
use umi\dbal\toolbox\factory\QueryBuilderFactory;
use umi\dbal\toolbox\factory\ServerFactory;
use umi\dbal\toolbox\factory\TableFactory;
use utest\TestCase;

/**
 * Тест фабрики построителей запросов
 *
 */
class ServerFactoryTest extends TestCase
{

    public function testQueryBuilderFactory()
    {

        $queryBuilderFactory = new QueryBuilderFactory();
        $this->resolveOptionalDependencies($queryBuilderFactory);

        $serverFactory = new ServerFactory($queryBuilderFactory);
        $this->resolveOptionalDependencies($serverFactory);

        $sqliteTableFactory = new TableFactory();
        $sqliteTableFactory->columnSchemeClass = 'umi\dbal\driver\ColumnScheme';
        $sqliteTableFactory->constraintSchemeClass = 'umi\dbal\driver\ConstraintScheme';
        $sqliteTableFactory->tableSchemeClass = 'umi\dbal\driver\sqlite\SqliteTable';
        $sqliteTableFactory->indexSchemeClass = 'umi\dbal\driver\sqlite\SqliteIndex';

        $driver = new SqliteDriver($sqliteTableFactory);

        $e = null;
        try {
            $serverFactory->create('wrongType', $driver, 'wrongType');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\dbal\exception\RuntimeException',
            $e,
            'Ожидается исключение, если создается сервер с неизвестным типом'
        );

        $this->assertInstanceOf(
            'umi\dbal\cluster\server\IMasterServer',
            $serverFactory->create('sqlite', $driver),
            'Ожидается, что IServerFactory::create() по умолчанию вернет IMasterServer'
        );
        $this->assertInstanceOf(
            'umi\dbal\cluster\server\IMasterServer',
            $serverFactory->create('sqlite', $driver, 'master'),
            'Ожидается, что IServerFactory::create() вернет сервер заданного типа'
        );
        $this->assertInstanceOf(
            'umi\dbal\cluster\server\ISlaveServer',
            $serverFactory->create('sqlite', $driver, 'slave'),
            'Ожидается, что IServerFactory::create() вернет сервер заданного типа'
        );
        $this->assertInstanceOf(
            'umi\dbal\cluster\server\IShardServer',
            $serverFactory->create('sqlite', $driver, 'shard'),
            'Ожидается, что IServerFactory::create() вернет сервер заданного типа'
        );
    }
}
