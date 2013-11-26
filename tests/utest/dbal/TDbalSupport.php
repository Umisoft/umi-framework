<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace utest\dbal;

use umi\dbal\cluster\IDbCluster;
use umi\dbal\cluster\server\IMasterServer;
use umi\toolkit\IToolkit;

/**
 * Трейт для регистрации тулбокса БД.
 */
trait TDbalSupport
{
    /**
     * @var string $_defaultServerId идентификатор мастер-сервера БД для тестирования по умолчанию
     */
    private $_defaultServerId = 'sqliteMaster';
    /**
     * @var string $_sqliteServerId идентификатор мастер-сервера для тестов, использующих sqlite
     */
    private $_sqliteServerId  = 'sqliteMaster';
    /**
     * @var string $_mysqlServerId идентификатор мастер-сервера для для тестов, использующих mysql
     */
    private $_mysqlServerId   = 'mysqlMaster';

    /**
     * Получить тестовый тулкит
     * @throws \RuntimeException
     * @return IToolkit
     */
    abstract protected function getTestToolkit();

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
        return $this->getDbCluster()
            ->getServer($this->_defaultServerId);
    }

    /**
     * Возвращает мастер-сервер для тестов, использующих mysql
     * @throws \RuntimeException
     * @return IMasterServer
     */
    protected function getMysqlServer()
    {
        return $this->getDbCluster()
            ->getServer($this->_mysqlServerId);
    }

    /**
     * Возвращает мастер-сервер для тестов, использующих sqlite
     * @throws \RuntimeException
     * @return IMasterServer
     */
    protected function getSqliteServer()
    {
        return $this->getDbCluster()
            ->getServer($this->_sqliteServerId);
    }


}
 