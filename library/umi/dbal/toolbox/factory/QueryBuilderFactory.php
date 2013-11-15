<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\toolbox\factory;

use umi\dbal\builder\IExpressionGroup;
use umi\dbal\builder\IQueryBuilder;
use umi\dbal\builder\IQueryBuilderFactory;
use umi\dbal\driver\IDbDriver;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика для построителей запросов.
 */
class QueryBuilderFactory implements IQueryBuilderFactory, IFactory
{

    use TFactory;

    /**
     * @var string $insertBuilderClass класс построителя INSERT-запросов
     */
    public $insertBuilderClass = 'umi\dbal\builder\InsertBuilder';
    /**
     * @var string $selectBuilderClass класс построителя SELECT-запросов
     */
    public $selectBuilderClass = 'umi\dbal\builder\SelectBuilder';
    /**
     * @var string $updateBuilderClass класс построителя UPDATE-запросов
     */
    public $updateBuilderClass = 'umi\dbal\builder\UpdateBuilder';
    /**
     * @var string $deleteBuilderClass класс построителя DELETE-запросов
     */
    public $deleteBuilderClass = 'umi\dbal\builder\DeleteBuilder';
    /**
     * @var string $joinBuilderClass класс построителя JOIN-секции запроса
     */
    public $joinBuilderClass = 'umi\dbal\builder\JoinBuilder';
    /**
     * @var string $deleteBuilderClass класс построителя DELETE-запросов
     */
    public $queryResultClass = 'umi\dbal\builder\QueryResult';
    /**
     * @var string $expressionGroupClass имя класса группы выражений
     */
    public $expressionGroupClass = 'umi\dbal\builder\ExpressionGroup';

    /**
     * {@inheritdoc}
     */
    public function createInsertBuilder(IDbDriver $driver)
    {
        return $this->getPrototype(
                $this->insertBuilderClass,
                ['umi\dbal\builder\IInsertBuilder']
            )
            ->createInstance([$driver, $this]);
    }

    /**
     * {@inheritdoc}
     */
    public function createDeleteBuilder(IDbDriver $driver)
    {
        return $this->getPrototype(
                $this->deleteBuilderClass,
                ['umi\dbal\builder\IDeleteBuilder']
            )
            ->createInstance([$driver, $this]);
    }

    /**
     * {@inheritdoc}
     */
    public function createUpdateBuilder(IDbDriver $driver)
    {
        return $this->getPrototype(
                $this->updateBuilderClass,
                ['umi\dbal\builder\IUpdateBuilder']
            )
            ->createInstance([$driver, $this]);
    }

    /**
     * {@inheritdoc}
     */
    public function createSelectBuilder(IDbDriver $driver)
    {
        return $this->getPrototype(
                $this->selectBuilderClass,
                ['umi\dbal\builder\ISelectBuilder']
            )
            ->createInstance([$driver, $this]);
    }

    /**
     * {@inheritdoc}
     */
    public function createJoinBuilder($table, $type)
    {
        return $this->getPrototype(
                $this->joinBuilderClass,
                ['umi\dbal\builder\IJoinBuilder']
            )
            ->createInstance([$table, $type]);
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryResult(IQueryBuilder $query, array $resultVariables)
    {
        return $this->getPrototype(
                $this->queryResultClass,
                ['umi\dbal\builder\IQueryResult']
            )
            ->createInstance([$query, $resultVariables]);
    }

    /**
     * {@inheritdoc}
     */
    public function createExpressionGroup($mode = IExpressionGroup::MODE_AND, IExpressionGroup $parentGroup = null)
    {
        return $this->getPrototype(
                $this->expressionGroupClass,
                ['umi\dbal\builder\IExpressionGroup']
            )
            ->createInstance([$mode, $parentGroup]);
    }
}
