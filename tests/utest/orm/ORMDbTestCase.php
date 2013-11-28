<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm;

use Closure;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Logging\DebugStack;
use umi\dbal\cluster\server\IMasterServer;
use umi\dbal\driver\IDialect;
use umi\orm\collection\ICollectionManager;
use umi\orm\manager\IObjectManager;
use umi\orm\metadata\IMetadataManager;
use umi\orm\persister\IObjectPersister;
use utest\TestCase;

/**
 * Тест кейс для ORM c использованием подключения к БД
 */
abstract class ORMDbTestCase extends TestCase
{

    const SYSTEM_HIERARCHY = 'system_hierarchy';
    const SYSTEM_MENU = 'system_menu';
    const USERS_USER = 'users_user';
    const USERS_GROUP = 'users_group';
    const USERS_PROFILE = 'users_profile';
    const BLOGS_BLOG = 'blogs_blog';
    const BLOGS_POST = 'blogs_post';
    const BLOGS_SUBSCRIBER = 'blogs_blog_subscribers';
    const GUIDES_CITY = 'guides_city';
    const GUIDES_COUNTRY = 'guides_country';

    /**
     * @var null идентификатор сервера БД, который будет использован для всего тест кейса
     * Если null, будет выбран сервер по умолчанию.
     */
    protected $usedDbServerId = null;
    /**
     * @var Connection $usedConnection
     */
    protected $usedConnection = null;
    /**
     * @var IObjectManager $objectsManager
     */
    protected $objectManager;
    /**
     * @var IMetadataManager $metadataManager
     */
    protected $metadataManager;
    /**
     * @var ICollectionManager $collectionManager
     */
    protected $collectionManager;
    /**
     * @var IObjectPersister $objectPersister
     */
    protected $objectPersister;
    /**
     * @var string $modelsDirectory директория с метаданными моделей
     */
    protected $metadataDirectory;
    /**
     * @var array $affectedTables список таблиц, которые необходимо удалить
     */
    private $affectedTables = [];

    /**
     * Возвращает список коллекций, для которых необходимо создать структуру БД
     * @return array в формате array('collection1', 'collection2', ...)
     */
    abstract protected function getCollections();

    public function setUp()
    {
        $this
            ->getTestToolkit()
            ->registerToolboxes([
                require(LIBRARY_PATH . '/event/toolbox/config.php'),
                require(LIBRARY_PATH . '/validation/toolbox/config.php'),
                require(LIBRARY_PATH . '/i18n/toolbox/config.php'),
                require(LIBRARY_PATH . '/orm/toolbox/config.php')
            ]);

        $cluster = $this->getDbCluster();
        if (!is_null($this->usedDbServerId)) {
            /**
             * @var IMasterServer $usedMaster
             */
            $usedMaster = $cluster->getServer($this->usedDbServerId);
            $cluster->setCurrentMaster($usedMaster);
            $cluster->setCurrentSlave($usedMaster);
        }

        $collections = $this->getCollections();

        $connection = $cluster->getConnection();

        /** @var $dialect IDialect */
        $dialect = $connection->getDatabasePlatform();

        /**
         * @var IObjectManager $objectManager
         */
        $objectManager = $this
            ->getTestToolkit()
            ->getService('umi\orm\manager\IObjectManager');
        $this->objectManager = $objectManager;

        /**
         * @var IMetadataManager $metadataManager
         */
        $metadataManager = $this
            ->getTestToolkit()
            ->getService('umi\orm\metadata\IMetadataManager');
        $this->metadataManager = $metadataManager;

        /**
         * @var ICollectionManager $collectionManager
         */
        $collectionManager = $this
            ->getTestToolkit()
            ->getService('umi\orm\collection\ICollectionManager');
        $this->collectionManager = $collectionManager;

        /**
         * @var IObjectPersister $objectPersister
         */
        $objectPersister = $this
            ->getTestToolkit()
            ->getService('umi\orm\persister\IObjectPersister');
        $this->objectPersister = $objectPersister;

        // start building db tables
        $connection->exec($dialect->getDisableForeignKeysSQL());

        foreach ($collections as $collectionName) {
            $metadata = $this->metadataManager->getMetadata($collectionName);
            $tableName = $metadata
                ->getCollectionDataSource()
                ->getSourceName();
            $connection
                ->getSchemaManager()
                ->dropTable($tableName);
            $this->affectedTables[] = $tableName;

            $dataSource = $metadata->getCollectionDataSource();

            /**
             * @var Closure $setup
             */
            $setup = include(__DIR__ . '/mock/collections/setup/' . $collectionName . '.setup.php');

            $setup($dataSource);
        }

        $connection->exec($dialect->getEnableForeignKeysSQL());

        $this->usedConnection = $connection;
        $this->usedConnection->getConfiguration()->setSQLLogger(new DebugStack());

        parent::setUp();
        $this->resetQueries();
    }

    protected function tearDown()
    {
        $connection = $this
            ->getDbCluster()
            ->getConnection();
        /** @var $dialect IDialect */
        $dialect = $connection->getDatabasePlatform();
        $connection->exec($dialect->getDisableForeignKeysSQL());
        foreach ($this->affectedTables as $tableName) {
            $connection
                ->getSchemaManager()
                ->dropTable($tableName);
        }
        $connection->exec($dialect->getEnableForeignKeysSQL());

        parent::tearDown();
    }

    //todo incapsulate queries getters to new DebugStack decorator

    /**
     * Логированные запросы, выполненные через $this->usedConnection
     * @param bool $withValues Подставлять ли реальные значения в логированные запросы
     * @return array
     */
    protected function getQueries($withValues = false)
    {
        return array_values(
            array_map(
                function ($a) use ($withValues) {
                    if ($withValues && isset($a['params']) && is_array($a['params'])) {
                        // make correct NULLs
                        array_walk(
                            $a['params'],
                            function (&$param) {
                                if ($param === null) {
                                    $param = 'NULL';
                                }
                            }
                        );
                        return strtr($a['sql'], $a['params']);
                    } else {
                        return $a['sql'];
                    }
                },
                $this->sqlLogger()->queries
            )
        );
    }

    /**
     * Logged queries as type-params pairs
     * @param bool $withParams Whether to append logged params in each result
     *
     * @return array [ ['select', [':foo'=>'foo', ':bar'=>121 ...]] ]
     */
    protected function getQueryTypesWithParams($withParams = true)
    {
        $queries = [];
        $kwRe = '/^\s*[`"]?(select|update|insert|delete|drop|truncate|start|rollback|commit)[`"]?\b/i';
        foreach ($this->sqlLogger()->queries as $a) {
            $matches = [];
            $query = [];
            if (preg_match($kwRe, $a['sql'], $matches)) {
                $query[0] = strtolower($matches[1]);
                if ($withParams) {
                    $query = [
                        strtolower($matches[1]),
                        isset($a['params']) && is_array($a['params']) ? $a['params'] : []
                    ];
                } else {
                    $query = strtolower($matches[1]);
                }
                $queries[] = $query;
            } else {
                $queries[] = false;
            }
        }

        return $queries;
    }

    /**
     * Логированные запросы, выполненные через $this->usedConnection с ограничением по типу
     * @param string $type select|update|insert|delete
     *
     * @return string[]
     */
    protected function getOnlyQueries($type)
    {
        return array_filter(
            $this->getQueries(),
            function ($q) use ($type) {
                return preg_match('/^'.$type.'\s+/i', $q);
            }
        );
    }

    /**
     * Сбросить лог запросов
     */
    public function resetQueries()
    {
        $this->sqlLogger()->queries = [];
    }

    /**
     * @return DebugStack
     */
    public function sqlLogger()
    {
        return $this->usedConnection
            ->getConfiguration()
            ->getSQLLogger();
    }
}
