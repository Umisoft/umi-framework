<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\cluster\server;

use umi\dbal\builder\IDeleteBuilder;
use umi\dbal\builder\IInsertBuilder;
use umi\dbal\builder\IQueryBuilderFactory;
use umi\dbal\builder\ISelectBuilder;
use umi\dbal\builder\IUpdateBuilder;
use umi\dbal\driver\IDbDriver;
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
     * @var IDbDriver $dbDriver драйвер БД для сервера
     */
    protected $dbDriver;
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
     * @param IDbDriver $dbDriver используемый драйвер БД
     * @param IQueryBuilderFactory $queryBuilderFactory фабрика построителей запросов
     */
    public function __construct($serverId, IDbDriver $dbDriver, IQueryBuilderFactory $queryBuilderFactory)
    {

        $this->dbDriver = $dbDriver;
        $this->serverId = $serverId;

        $this->insertBuilderPrototype = $queryBuilderFactory->createInsertBuilder($dbDriver);
        $this->deleteBuilderPrototype = $queryBuilderFactory->createDeleteBuilder($dbDriver);
        $this->updateBuilderPrototype = $queryBuilderFactory->createUpdateBuilder($dbDriver);
        $this->selectBuilderPrototype = $queryBuilderFactory->createSelectBuilder($dbDriver);
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
    public function getDbDriver()
    {
        return $this->dbDriver;
    }

    /**
     * {@inheritdoc}
     */
    public function selectInternal($sql, array $params = null)
    {
        return $this->getDbDriver()
            ->select($sql, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function modifyInternal($sql, array $params = null)
    {
        return $this->getDbDriver()
            ->modify($sql, $params);
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
