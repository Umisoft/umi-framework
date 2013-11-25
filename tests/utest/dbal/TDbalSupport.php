<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace utest\dbal;

use umi\config\entity\IConfig;
use umi\dbal\cluster\IDbCluster;
use umi\dbal\cluster\server\IMasterServer;
use umi\toolkit\IToolkit;

/**
 * Трейт для регистрации тулбокса БД.
 */
trait TDbalSupport
{
    /**
     * Получить тестовый тулкит
     * @throws \RuntimeException
     * @return IToolkit
     */
    abstract protected function getTestToolkit();

    /**
     * Возвращает конфигурацию для тестов
     * @return IConfig
     */
    abstract protected function config();

    protected function registerDbalTools()
    {
        $this->getTestToolkit()->registerToolbox(
            require(LIBRARY_PATH . '/dbal/toolbox/config.php')
        );

        if (!file_exists(TESTS_CONFIGURATION . '/local/db.php')) {
            throw new \RuntimeException('Db configuration file "' . TESTS_CONFIGURATION . '/local/db.php' . '" does not exist.' );
        }

        $this->getTestToolkit()->setSettings(
          include(TESTS_CONFIGURATION . '/local/db.php')
        );
    }

    /**
     * Возвращает кластер баз данных
     * @return IDbCluster
     */
    protected function getDbCluster()
    {
        /**
         * @var IDbCluster $dbCluster
         */
        $dbCluster = $this->getTestToolkit()
            ->getService('umi\dbal\cluster\IDbCluster');

        return $dbCluster;
    }

    /**
     * Возвращает мастер-сервер по умолчанию
     * @throws \RuntimeException
     * @return IMasterServer
     */
    protected function getDbServer()
    {
        if (!$this->config()
            ->has('defaultServer')
        ) {
            throw new \RuntimeException("Invalid default server id.");
        }

        $serverId = $this->config()
            ->get('defaultServer');

        return $this->getDbCluster()
            ->getServer($serverId);
    }

    /**
     * Возвращает мастер-сервер для тестов, использующих mysql
     * @throws \RuntimeException
     * @return IMasterServer
     */
    protected function getMysqlServer()
    {
        if (!$this->config()
            ->has('mysqlServer')
        ) {
            throw new \RuntimeException("Invalid mysql server id.");
        }

        $serverId = $this->config()
            ->get('mysqlServer');

        return $this->getDbCluster()
            ->getServer($serverId);
    }

    /**
     * Возвращает мастер-сервер для тестов, использующих sqlite
     * @throws \RuntimeException
     * @return IMasterServer
     */
    protected function getSqliteServer()
    {
        if (!$this->config()
            ->has('mysqlServer')
        ) {
            throw new \RuntimeException("Invalid SQLite server id.");
        }

        $serverId = $this->config()
            ->get('sqliteServer');

        return $this->getDbCluster()
            ->getServer($serverId);
    }


}
 