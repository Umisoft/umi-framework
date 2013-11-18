<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\builder;

use umi\dbal\exception\RuntimeException;

/**
 * Построитель Select-запросов.
 */
class SelectBuilder extends BaseQueryBuilder implements ISelectBuilder
{
    /**
     * @var array $selectColumns список столбцов для выборки в формате array(array('columnName', 'alias'), ...))
     */
    protected $selectColumns = [];
    /**
     * @var array $tables список таблиц для выборки в формате array(array('tableName', 'alias'), ...))
     */
    protected $tables = [];
    /**
     * @var IJoinBuilder[] $joins список JOIN-условий
     */
    protected $joins = [];
    /**
     * @var IJoinBuilder $latestJoin последний созданный JOIN
     */
    protected $latestJoin;
    /**
     * @var bool $distinct SELECT DISTINCT
     */
    protected $distinct = false;
    /**
     * @var int $limit ограничение на количество затрагиваемых строк
     */
    protected $limit;
    /**
     * @var int $offset смещение выборки
     */
    protected $offset = 0;
    /**
     * @var array $groupByConditions список GROUP BY - условий
     */
    protected $groupByConditions = [];
    /**
     * @var IExpressionGroup $whereExpressionGroup группа условий WHERE
     */
    protected $whereExpressionGroup;
    /**
     * @var IExpressionGroup $havingExpressionGroup группа условий HAVING
     */
    protected $havingExpressionGroup;
    /**
     * @var bool $useCalcFoundRows использовать в запросе c ограничениями по выборке параметр,
     * позволяющий получить общее количество строк
     */
    protected $useCalcFoundRows = false;
    /**
     * @var bool $noCache запрещать серверу БД использовать кэш для запроса
     */
    protected $noCache = false;

    /**
     * {@inheritdoc}
     */
    public function select()
    {
        return $this->setColumns(func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function setColumns(array $columns)
    {
        $this->selectColumns = [];

        $columnsCount = count($columns);
        for ($i = 0; $i < $columnsCount; $i++) {
            if (is_string($columns[$i])) {
                $this->selectColumns[] = $this->parseAlias($columns[$i]);
            } elseif (is_array($columns[$i]) && count($columns[$i])) {
                $name = strval($columns[$i][0]);
                $alias = isset($columns[$i][1]) ? strval($columns[$i][1]) : null;
                $this->selectColumns[] = array($name, $alias);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function disableCache()
    {
        $this->noCache = true;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDisabled()
    {
        return $this->noCache;
    }

    /**
     * {@inheritdoc}
     */
    public function distinct($enabled = true)
    {
        $this->distinct = (bool) $enabled;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function from()
    {
        $this->tables = [];
        $tables = func_get_args();

        if (!$tablesCount = count($tables)) {
            throw new RuntimeException($this->translate(
                'Cannot select from tables. Table names required.'
            ));
        }

        for ($i = 0; $i < $tablesCount; $i++) {
            if (is_string($tables[$i])) {
                $this->tables[] = $this->parseAlias($tables[$i]);
            } elseif (is_array($tables[$i]) && count($tables[$i])) {
                $name = strval($tables[$i][0]);
                $alias = isset($tables[$i][1]) ? strval($tables[$i][1]) : null;
                $this->tables[] = array($name, $alias);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function where($mode = IExpressionGroup::MODE_AND)
    {
        if (!$this->whereExpressionGroup) {
            $this->currentExpressionGroup = null;
            $this->begin($mode);
            $this->whereExpressionGroup = $this->currentExpressionGroup;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function having($mode = IExpressionGroup::MODE_AND)
    {
        if (!$this->havingExpressionGroup) {
            $this->currentExpressionGroup = null;
            $this->begin($mode);
            $this->havingExpressionGroup = $this->currentExpressionGroup;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function join($table, $type = 'INNER')
    {
        /**
         * @var IJoinBuilder $join
         */
        $join = $this->queryBuilderFactory->createJoinBuilder($table, $type);
        list($name, $alias) = $join->getTable();
        if (!$alias) {
            $alias = $name;
        }

        $this->joins[$alias] = $this->latestJoin = $join;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function leftJoin($table)
    {
        return $this->join($table, 'LEFT');
    }

    /**
     * {@inheritdoc}
     */
    public function innerJoin($table)
    {
        return $this->join($table, 'INNER');
    }

    /**
     * {@inheritdoc}
     */
    public function on($leftColumn, $operator, $rightColumn)
    {
        if ($this->latestJoin) {
            $this->latestJoin->on($leftColumn, $operator, $rightColumn);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function limit($limit, $offset = null, $useCalcFoundRows = false)
    {
        $this->limit = $limit;
        if ($offset) {
            $this->offset = $offset;
        }
        $this->useCalcFoundRows = $useCalcFoundRows;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function offset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function groupBy($column, $direction = IQueryBuilder::ORDER_ASC)
    {
        if ($direction == IQueryBuilder::ORDER_ASC || $direction == IQueryBuilder::ORDER_DESC) {
            $this->groupByConditions[$column] = $direction;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectColumns()
    {
        return $this->selectColumns;
    }

    /**
     * {@inheritdoc}
     */
    public function getTables()
    {
        return $this->tables;
    }

    /**
     * {@inheritdoc}
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * {@inheritdoc}
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * {@inheritdoc}
     */
    public function getUseCalcFoundRows()
    {
        return $this->useCalcFoundRows;
    }

    /**
     * {@inheritdoc}
     */
    public function getDistinct()
    {
        return $this->distinct;
    }

    /**
     * {@inheritdoc}
     */
    public function getJoins()
    {
        return $this->joins;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupByConditions()
    {
        return $this->groupByConditions;
    }

    /**
     * {@inheritdoc}
     */
    public function getWhereExpressionGroup()
    {
        return $this->whereExpressionGroup;
    }

    /**
     * {@inheritdoc}
     */
    public function getHavingExpressionGroup()
    {
        return $this->havingExpressionGroup;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal()
    {
        $sql = $this->dialect->buildSelectFoundRowsQuery($this);
        $sql = $this->prepareArrayPlaceholders($sql);
        $sql = $this->prepareExpressionPlaceholders($sql);
        $preparedStatement = $this->connection->prepare($sql);
        $this->bind($preparedStatement, $sql);
        $preparedStatement->execute();

        $result = $preparedStatement->fetch(\PDO::FETCH_NUM);
        $preparedStatement->closeCursor();

        return !empty($result) ? $result[0] : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return parent::execute();
    }

    /**
     * {@inheritdoc}
     */
    protected function build()
    {
        $queryBuilder = $this->dialect;

        return $queryBuilder->buildSelectQuery($this);
    }
}
