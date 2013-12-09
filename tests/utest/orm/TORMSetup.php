<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace utest\orm;

use umi\dbal\cluster\server\IMasterServer;
use umi\dbal\driver\IDialect;
use umi\orm\collection\ICollectionManager;
use umi\orm\manager\IObjectManager;
use umi\orm\metadata\IMetadataManager;
use umi\orm\persister\IObjectPersister;
use umi\orm\toolbox\ORMTools;
use utest\dbal\TDbalSupport;

/**
 * Трейт для создания таблиц ORM
 */
trait TORMSetup
{
    use TDbalSupport;

    /**
     * @var array $affectedTables список таблиц, которые необходимо удалить
     */
    private $_affectedTables = [];

    /**
     * Возвращает конфигурацию коллекций,
     * включающую имя директории с метаданными и инстукциями по созданию таблиц,
     * конфигурацию коллекций для CollectionManager
     * и флаг необходимости создания структуры БД
     * @return array в формате ['directory', ['collectionName'] => [], ..., 'createDB']
     */
    abstract protected function getCollectionConfig();

    protected function setUpORM($usedDbServerId = null)
    {

        list($directory, $collectionNames, $createDB) = $this->configureCollections();
        $this->configureCluster($usedDbServerId);
        if ($createDB) {
            $this->configureDatabase($directory, $collectionNames);
        }
    }

    protected function tearDownORM()
    {
        $dbDriver = $this->getDbCluster()
            ->getConnection();
        /** @var IDialect $dialect */
        $dialect = $dbDriver->getDatabasePlatform();
        $dbDriver->exec($dialect->getDisableForeignKeysSQL());

        foreach ($this->_affectedTables as $tableName) {
            $dbDriver->getSchemaManager()->dropTable($tableName);
        }

        $dbDriver->exec($dialect->getEnableForeignKeysSQL());
    }

    /**
     * @return IObjectManager
     */
    protected function getObjectManager()
    {
        return $this->getTestToolkit()->getService('umi\orm\manager\IObjectManager');
    }
    /**
     * @return IMetadataManager
     */
    protected function getMetadataManager()
    {
        return $this->getTestToolkit()->getService('umi\orm\metadata\IMetadataManager');
    }

    /**
     * @return ICollectionManager
     */
    protected function getCollectionManager()
    {
        return $this->getTestToolkit()->getService('umi\orm\collection\ICollectionManager');
    }

    /**
     * @return IObjectPersister
     */
    protected function getObjectPersister()
    {
        return $this->getTestToolkit()->getService('umi\orm\persister\IObjectPersister');
    }

    /**
     * @param string|null $usedDbServerId
     */
    private function configureCluster($usedDbServerId = null)
    {
        $cluster = $this->getDbCluster();
        if (!is_null($usedDbServerId)) {
            /**
             * @var IMasterServer $usedMaster
             */
            $usedMaster = $cluster->getServer($usedDbServerId);
            $cluster->setCurrentMaster($usedMaster);
            $cluster->setCurrentSlave($usedMaster);
        }
    }

    /**
     * @return array
     */
    private function configureCollections()
    {
        list ($directory, $collectionConfig, $createDB) = $this->getCollectionConfig();
        $collectionNames = array_keys($collectionConfig);

        $metadataConfig = [];
        foreach ($collectionNames as $collectionName) {
            /** @noinspection PhpIncludeInspection */
            $metadataConfig[$collectionName] = include($directory . '/metadata/' . $collectionName . '.config.php');
        }

        $this->getTestToolkit()->setSettings(
            [
                ORMTools::NAME => [
                    'collections' => $collectionConfig,
                    'metadata' => $metadataConfig
                ]
            ]
        );

        return [$directory, $collectionNames, $createDB];
    }

    private function configureDataBase($directory, array $collectionNames)
    {
        $cluster = $this->getDbCluster();
        $connection = $cluster->getConnection();
        /** @var IDialect $dialect */
        $dialect = $connection->getDatabasePlatform();

        $connection->exec($dialect->getDisableForeignKeysSQL());
        foreach ($collectionNames as $collectionName) {
            $metadata = $this->getMetadataManager()->getMetadata($collectionName);
            $tableName = $metadata->getCollectionDataSource()
                ->getSourceName();
            $connection->getSchemaManager()->dropTable($tableName);
            $this->_affectedTables[] = $tableName;
        }
        $connection->exec($dialect->getEnableForeignKeysSQL());

        static $migrations = [];
        $class = get_class($this);

        if (!isset($migrations[$class])) {
            $migrations[$class] = [];

            foreach ($collectionNames as $collectionName) {
                $metadata = $this->getMetadataManager()->getMetadata($collectionName);
                $dataSource = $metadata->getCollectionDataSource();

                $setupScriptFile = $directory . '/setup/' . $collectionName . '.setup.php';

                /** @var \Closure $setup */
                /** @noinspection PhpIncludeInspection */
                $setup = include($setupScriptFile);

                $sql = $setup($dataSource);

                $migrations[$class][$collectionName] = [
                    $dataSource->getMasterServer()
                        ->getId(),
                    $sql
                ];
            }
        }

        foreach ($migrations[$class] as $collectionMigrations) {
            $serverId = $collectionMigrations[0];
            $queries = $collectionMigrations[1];
            $server = $cluster->getServer($serverId);
            $server->getConnection()->exec($dialect->getDisableForeignKeysSQL());
            foreach ($queries as $query) {
                $server->modifyInternal($query);
            }
            $server->getConnection()->exec($dialect->getEnableForeignKeysSQL());
        }
    }
}
