<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm;

use Closure;
use umi\dbal\cluster\server\IMasterServer;
use umi\orm\collection\ICollectionManager;
use umi\orm\manager\IObjectManager;
use umi\orm\metadata\IMetadataManager;
use umi\orm\persister\IObjectPersister;
use umi\orm\toolbox\IORMTools;
use utest\TestCase;

/**
 * Тест кейс для моделей данных
 */
abstract class ORMTestCase extends TestCase
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
        $dbDriver = $cluster->getDbDriver();

        /**
         * @var IORMTools $ormTools
         */
        $ormTools = $this->getTestToolkit()
            ->getToolbox(IORMTools::ALIAS);
        $this->objectManager = $ormTools->getObjectManager();
        $this->metadataManager = $ormTools->getMetadataManager();
        $this->collectionManager = $ormTools->getCollectionManager();
        $this->objectPersister = $ormTools->getObjectPersister();

        foreach ($collections as $collectionName) {
            $metadata = $this->metadataManager->getMetadata($collectionName);
            $tableName = $metadata->getCollectionDataSource()
                ->getSourceName();
            $dbDriver->dropTable($tableName);
            $this->affectedTables[] = $tableName;
        }

        $migrations = [];
        foreach ($collections as $collectionName) {
            $metadata = $this->metadataManager->getMetadata($collectionName);
            $dataSource = $metadata->getCollectionDataSource();
            $tableName = $dataSource->getSourceName();

            /**
             * @var Closure $setup
             */
            $setup = include(__DIR__ . '/mock/collections/setup/' . $collectionName . '.setup.php');

            $setup($dataSource);

            $migrations[$collectionName] = [
                $dataSource->getMasterServer()
                    ->getId(),
                $dbDriver->getTable($tableName)
                    ->getMigrationQueries()
            ];
        }

        foreach ($migrations as $collectionMigrations) {
            $serverId = $collectionMigrations[0];
            $queries = $collectionMigrations[1];
            $server = $cluster->getServer($serverId);
            $server->getDbDriver()
                ->disableForeignKeysCheck();
            foreach ($queries as $query) {
                $server->modifyInternal($query);
            }
            $server->getDbDriver()
                ->enableForeignKeysCheck();
            $server->getDbDriver()
                ->reset();
        }

        parent::setUp();
    }

    protected function tearDown()
    {
        $dbDriver = $this->getDbCluster()
            ->getDbDriver();
        $dbDriver->disableForeignKeysCheck();
        foreach ($this->affectedTables as $tableName) {
            $dbDriver->deleteTable($tableName);
        }
        $dbDriver->applyMigrations();
        $dbDriver->enableForeignKeysCheck();

        parent::tearDown();
    }
}
