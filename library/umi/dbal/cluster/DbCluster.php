<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\cluster;

use umi\dbal\cluster\server\IMasterServer;
use umi\dbal\cluster\server\IServer;
use umi\dbal\cluster\server\ISlaveServer;
use umi\dbal\builder\ISelectBuilder;
use umi\dbal\exception\NonexistentEntityException;
use umi\dbal\exception\RuntimeException;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Компонент для работы с БД.
 */
class DbCluster implements IDbCluster, ILocalizable
{

    use TLocalizable;

    /**
     * @var IServer[] $servers список серверов
     */
    private $servers = [];
    /**
     * @var IMasterServer $currentMaster текущий master-сервер
     */
    private $currentMaster;
    /**
     * @var ISlaveServer $currentSlave текущий slave-сервер
     */
    private $currentSlave;

    /**
     * {@inheritdoc}
     */
    public function addServer(IServer $server)
    {
        $this->servers[$server->getId()] = $server;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentMaster(IMasterServer $server)
    {
        $this->currentMaster = $server;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentSlave(ISlaveServer $server)
    {
        $this->currentSlave = $server;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getServer($serverId)
    {
        if (!isset($this->servers[$serverId])) {
            throw new NonexistentEntityException($this->translate(
                'Database server "{server}" not found.',
                ['server' => $serverId]
            ));
        }

        return $this->servers[$serverId];
    }

    /**
     * {@inheritdoc}
     */
    public function getSlave()
    {
        if (!$this->currentSlave) {
            $this->detectServers();
        }

        return $this->currentSlave;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaster()
    {
        if (!$this->currentMaster) {
            $this->detectServers();
        }

        return $this->currentMaster;
    }

    /**
     * {@inheritdoc}
     */
    public function select()
    {
        /**
         * @var ISelectBuilder $select
         */
        $select = $this
            ->getSlave()
            ->select();
        $select->setColumns(func_get_args());

        return $select;
    }

    /**
     * {@inheritdoc}
     */
    public function insert($tableName, $isIgnore = false)
    {
        return $this
            ->getMaster()
            ->insert($tableName, $isIgnore);
    }

    /**
     * {@inheritdoc}
     */
    public function update($tableName, $isIgnore = false)
    {
        return $this
            ->getMaster()
            ->update($tableName, $isIgnore);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($tableName)
    {
        return $this
            ->getMaster()
            ->delete($tableName);
    }

    /**
     * {@inheritdoc}
     */
    public function selectInternal($sql, array $params = [])
    {
        return $this
            ->getSlave()
            ->selectInternal($sql, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function modifyInternal($sql, array $params = null)
    {
        return $this
            ->getMaster()
            ->modifyInternal($sql, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        return $this
            ->getMaster()
            ->getConnection();
    }

    /**
     * Запрашивает у балансировщика slave и мастер сервер для работы
     */
    protected function detectServers()
    {
        // TODO: сделать балансировщик

        foreach ($this->servers as $serverId => $server) {
            if ($server instanceof IMasterServer) {
                $this->currentMaster = $this->getServer($serverId);
            } elseif ($server instanceof ISlaveServer) {
                $this->currentSlave = $this->getServer($serverId);
            }
        }
        if (!$this->currentMaster) {
            throw new RuntimeException($this->translate(
                'Cannot detect master server for db connection.'
            ));
        }
        if (!$this->currentSlave) {
            $this->currentSlave = $this->currentMaster;
        }
    }
}
