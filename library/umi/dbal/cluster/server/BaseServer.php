<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\cluster\server;

use Doctrine\DBAL\Connection;
use umi\dbal\builder\IDeleteBuilder;
use umi\dbal\builder\IInsertBuilder;
use umi\dbal\builder\IQueryBuilderFactory;
use umi\dbal\builder\ISelectBuilder;
use umi\dbal\builder\IUpdateBuilder;
use umi\dbal\driver\IDialect;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Базовый компонент сервера в кластере.
 */
abstract class BaseServer implements IServer, ILocalizable
{

    use TLocalizable;

    /**
     * @var string $serverId идентификатор сервера
     */
    protected $serverId;
    /**
     * @var Connection $connection драйвер БД для сервера
     */
    protected $connection;
    /**
     * @var IInsertBuilder $insertBuilderPrototype построитель INSERT-запросов
     */
    protected $insertBuilderPrototype;
    /**
     * @var ISelectBuilder $selectBuilderPrototype построитель SELECT-запросов
     */
    protected $selectBuilderPrototype;
    /**
     * @var IUpdateBuilder $updateBuilderPrototype построитель UPDATE-запросов
     */
    protected $updateBuilderPrototype;
    /**
     * @var IDeleteBuilder $deleteBuilderPrototype построитель DELETE-запросов
     */
    protected $deleteBuilderPrototype;

    /**
     * Конструктор
     * @param string $serverId идентификатор сервера
     * @param \Doctrine\DBAL\Connection $connection используемый драйвер БД
     * @param IDialect $dialect
     * @param IQueryBuilderFactory $queryBuilderFactory фабрика построителей запросов
     */
    public function __construct(
        $serverId,
        Connection $connection,
        IDialect $dialect,
        IQueryBuilderFactory $queryBuilderFactory
    )
    {

        $this->connection = $connection;
        $this->serverId = $serverId;

        $this->insertBuilderPrototype = $queryBuilderFactory->createInsertBuilder($connection, $dialect);
        $this->deleteBuilderPrototype = $queryBuilderFactory->createDeleteBuilder($connection, $dialect);
        $this->updateBuilderPrototype = $queryBuilderFactory->createUpdateBuilder($connection, $dialect);
        $this->selectBuilderPrototype = $queryBuilderFactory->createSelectBuilder($connection, $dialect);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->serverId;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * {@inheritdoc}
     */
    public function selectInternal($sql, array $params = [])
    {
        return $this
            ->getConnection()
            ->executeQuery($sql, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function modifyInternal($sql, array $params = null)
    {
        return $this
            ->getConnection()
            ->exec($sql, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function select()
    {
        $builder = clone $this->selectBuilderPrototype;
        $builder->setColumns(func_get_args());

        return $builder;
    }

    /**
     * {@inheritdoc}
     */
    public function insert($tableName, $isIgnore = false)
    {
        $builder = clone $this->insertBuilderPrototype;

        return $builder->insert($tableName, $isIgnore);
    }

    /**
     * {@inheritdoc}
     */
    public function update($tableName, $isIgnore = false)
    {
        $builder = clone $this->updateBuilderPrototype;

        return $builder->update($tableName, $isIgnore);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($tableName)
    {
        $builder = clone $this->deleteBuilderPrototype;

        return $builder->from($tableName);
    }
}
