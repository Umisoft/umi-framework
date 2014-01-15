<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\metadata;

use umi\dbal\cluster\IDbCluster;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\orm\exception\UnexpectedValueException;

/**
 * Источник данных для коллекции объектов.
 * Позволяет делать низкоуровневые операции над таблицой БД,
 * которая является источником данных коллекции.
 */
class CollectionDataSource implements ICollectionDataSource, ILocalizable
{

    use TLocalizable;

    /**
     * @var IDbCluster $dbCluster кластер БД
     */
    protected $dbCluster;
    /**
     * @var string $masterServerId идентификатор мастер сервера
     */
    protected $masterServerId;
    /**
     * @var string $slaveServerId идентификатор слейв сервера
     */
    protected $slaveServerId;
    /**
     * @var string $sourceName имя источника
     */
    protected $sourceName;

    /**
     * Конструктор.
     * @param array $config конфигурация
     * @param IDbCluster $dbCluster кластер БД
     * @throws UnexpectedValueException при неверно заданной конфигурации
     */
    public function __construct(array $config, IDbCluster $dbCluster)
    {
        if (!isset($config['sourceName']) || !is_string($config['sourceName'])) {
            throw new UnexpectedValueException($this->translate(
                'Collection data source configuration should contain source name and name should be a string.'
            ));
        }
        $this->sourceName = $config['sourceName'];
        if (isset($config['masterServerId'])) {
            $this->masterServerId = $config['masterServerId'];
        }
        if (isset($config['slaveServerId'])) {
            $this->slaveServerId = $config['slaveServerId'];
        }
        $this->dbCluster = $dbCluster;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        return $this->getMasterServer()
            ->getConnection();
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceName()
    {
        return $this->sourceName;
    }

    /**
     * {@inheritdoc}
     */
    public function getMasterServer()
    {
        if (is_null($this->masterServerId)) {
            return $this->dbCluster->getMaster();
        }

        return $this->dbCluster->getServer($this->masterServerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getMasterServerId()
    {
        return $this->masterServerId;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlaveServer()
    {
        if (is_null($this->slaveServerId)) {
            return $this->dbCluster->getSlave();
        }

        return $this->dbCluster->getServer($this->slaveServerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getSlaveServerId()
    {
        return $this->slaveServerId;
    }

    /**
     * {@inheritdoc}
     */
    public function select($columns = [])
    {
        return $this
            ->getSlaveServer()
            ->select($columns)
            ->from($this->sourceName);
    }

    /**
     * {@inheritdoc}
     */
    public function insert($isIgnore = false)
    {
        return $this->getMasterServer()
            ->insert($this->sourceName, $isIgnore);
    }

    /**
     * {@inheritdoc}
     */
    public function update($isIgnore = false)
    {
        return $this->getMasterServer()
            ->update($this->sourceName, $isIgnore);
    }

    /**
     * {@inheritdoc}
     */
    public function delete()
    {
        return $this->getMasterServer()
            ->delete($this->sourceName);
    }
}
