<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace utest\cache;

use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use umi\dbal\cluster\server\IServer;
use utest\dbal\TDbalSupport;
use utest\event\TEventSupport;
use utest\TestCase;

/**
 * Тест кейс для кеширования
 */
abstract class CacheTestCase extends TestCase
{
    use TCacheSupport;
    use TEventSupport;
    use TDbalSupport;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->registerEventTools();
        $this->registerDbalTools();
        $this->registerCacheTools();

        parent::setUp();
    }

    /**
     * Пересоздает таблицу кеша в базе данных
     * @param string $tableName имя таблицы кеша
     * @param IServer $server сервер базы данных, в которой будет создана таблица
     */
    protected function setupCacheDatabase($tableName, $server = null)
    {
        $connection = is_null($server) ? $this
            ->getDefaultDbServer()
            ->getConnection() : $server->getConnection();

        if ($connection
            ->getSchemaManager()
            ->tablesExist($tableName)
        ) {
            $connection
                ->getSchemaManager()
                ->dropTable($tableName);
        }

        $table = new Table($tableName);

        $table->addColumn('key', Type::STRING, ['comment' => 'Cache unique key']);
        $table->addColumn('cacheValue', Type::BLOB, ['comment' => 'Cache value']);
        $table->addColumn(
            'cacheExpiration',
            Type::INTEGER,
            ['comment' => 'Cache expire timestamp', 'unsigned' => true]
        );
        $table->setPrimaryKey(['key']);
        $table->addIndex(['cacheExpiration'], 'expire');

        $connection
            ->getSchemaManager()
            ->createTable($table);

    }
}
