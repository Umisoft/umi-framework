<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\driver\dialect;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use PDO;
use umi\dbal\builder\IDeleteBuilder;
use umi\dbal\builder\IExpressionGroup;
use umi\dbal\builder\IInsertBuilder;
use umi\dbal\builder\IQueryBuilder;
use umi\dbal\builder\ISelectBuilder;
use umi\dbal\builder\IUpdateBuilder;
use umi\dbal\driver\IDialect;
use umi\dbal\exception\IException;
use umi\dbal\exception\RuntimeException;

class MySqlDialect extends MySqlPlatform implements IDialect
{
    /**
     * {@inheritdoc}
     */
    public function getDisableKeysSQL($tableName)
    {
        $tableName = $this->quoteIdentifier($tableName);

        return 'ALTER TABLE ' . $tableName . ' DISABLE KEYS';
    }

    /**
     * {@inheritdoc}
     */
    public function getEnableKeysSQL($tableName)
    {
        $tableName = $this->quoteIdentifier($tableName);

        return 'ALTER TABLE ' . $tableName . ' ENABLE KEYS';
    }

    /**
     * {@inheritdoc}
     */
    public function getDisableForeignKeysSQL()
    {
        return 'SET foreign_key_checks = 0';
    }

    /**
     * {@inheritdoc}
     */
    public function getEnableForeignKeysSQL()
    {
        return 'SET foreign_key_checks = 1';
    }

    /**
     * {@inheritdoc}
     */
    public function buildSelectQuery(ISelectBuilder $query)
    {

        $distinctSql = $query->getDistinct() ? ' DISTINCT' : '';
        $noCache = $query->getCacheDisabled() ? ' SQL_NO_CACHE' : '';
        $calcFoundRows = $query->getUseCalcFoundRows() ? 'SQL_CALC_FOUND_ROWS ' : '';

        $orderBy = $this->buildOrderByPart($query);

        $limitSql = '';
        if (null != ($limit = $query->getLimit())) {
            $limitSql = "\nLIMIT " . $limit . ' OFFSET ' . $query->getOffset();
        }

        $result = 'SELECT' . $noCache . $distinctSql . ' '
            . $calcFoundRows
            . $this->buildSelectQueryBody($query)
            . $orderBy
            . $limitSql;

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function buildUpdateQuery(IUpdateBuilder $query)
    {
        $ignoreSql = $query->getIsIgnore() ? ' IGNORE' : '';
        $whatSql = $this->quoteIdentifier($query->getTableName());
        $whereSql = $this->buildWherePart($query);
        $orderBy = $this->buildOrderByPart($query);
        $setSql = "\nSET " . $this->buildSetPart($query->getValues());

        $limitSql = '';
        if (null != ($limit = $query->getLimit())) {
            $limitSql = "\nLIMIT " . $limit;
        }

        return 'UPDATE' . $ignoreSql . ' ' . $whatSql
        . $setSql
        . $whereSql
        . $orderBy
        . $limitSql;
    }

    /**
     * {@inheritdoc}
     */
    public function buildInsertQuery(IInsertBuilder $query)
    {
        $ignoreSql = $query->getIsIgnore() ? ' IGNORE' : '';
        $whatSql = $this->quoteIdentifier($query->getTableName());
        $setSql = "\nSET " . $this->buildSetPart($query->getValues());

        $onDuplicateKeyValues = $query->getOnDuplicateKeyValues();
        $onDuplicateKeySql = '';

        if (!empty($onDuplicateKeyValues) && !$query->getIsIgnore()) {
            $onDuplicateKeySql = "\nON DUPLICATE KEY UPDATE " . $this->buildSetPart($onDuplicateKeyValues);
        }

        return 'INSERT' . $ignoreSql . ' INTO ' . $whatSql
        . $setSql
        . $onDuplicateKeySql;
    }

    /**
     * {@inheritdoc}
     */
    public function buildDeleteQuery(IDeleteBuilder $query)
    {
        $fromSql = $this->quoteIdentifier($query->getTableName());
        $whereSql = $this->buildWherePart($query);
        $orderBy = $this->buildOrderByPart($query);

        $limitSql = '';
        if (null != ($limit = $query->getLimit())) {
            $limitSql = "\nLIMIT " . $limit;
        }

        return 'DELETE FROM ' . $fromSql
            . $whereSql
            . $orderBy
            . $limitSql;
    }

    /**
     * Строит ORDER BY часть запроса
     * @internal
     * @param ISelectBuilder|IDeleteBuilder|IUpdateBuilder $query
     * @return string
     */
    private function buildOrderByPart($query)
    {
        $conditions = $query->getOrderConditions();
        if (!count($conditions)) {
            return '';
        }

        $result = [];
        foreach ($conditions as $column => $direction) {
            $result[] = $this->quoteIdentifier($column) . ' ' . strtoupper($direction);
        }

        return "\nORDER BY " . implode(", ", $result);
    }

    /**
     * Строит sql-запрос на выборку данных без LIMIT и ORDER BY
     * @param ISelectBuilder $query запрос
     * @throws IException если не удалось построить запрос
     * @return string
     */
    protected function buildSelectQueryBody($query)
    {
        $whatSql = $this->buildSelectWhatPart($query);
        $fromSql = $this->buildSelectFromPart($query);
        $whereSql = $this->buildWherePart($query);
        $groupBySql = $this->buildSelectGroupByPart($query);
        $havingSql = $this->buildSelectHavingPart($query);
        $joinSql = $this->buildSelectJoinPart($query);

        $result = $whatSql
            . $fromSql
            . $joinSql
            . $whereSql
            . $groupBySql
            . $havingSql;

        return $result;
    }

    /**
     * Строит WHAT часть запроса (SELECT WHAT)
     * @internal
     * @param ISelectBuilder $query
     * @return string
     */
    private function buildSelectWhatPart(ISelectBuilder $query)
    {
        $columns = $query->getSelectColumns();
        if (!count($columns)) {
            return '*';
        }

        $result = [];
        foreach ($columns as $column) {
            if (is_array($column)) {
                list($name, $alias) = $column;
                $name = $this->protectExpressionValue($name);
                $result[] = $name . ($alias ? ' AS ' . $this->quoteIdentifier($alias) : '');
            }
        }

        return implode(", ", $result);
    }

    /**
     * Строит FROM часть запроса (SELECT FROM ...)
     * @internal
     * @param ISelectBuilder $query
     * @return string
     */
    private function buildSelectFromPart(ISelectBuilder $query)
    {
        $tables = $query->getTables();
        if (!count($tables)) {
            return '';
        }

        $result = [];
        foreach ($tables as $table) {
            if (is_array($table)) {
                list($name, $alias) = $table;
                $name = $this->quoteIdentifier($name);
                $result[] = $name . ($alias ? ' AS ' . $this->quoteIdentifier($alias) : '');
            }
        }

        return "\nFROM " . implode(", ", $result);
    }

    /**
     * Строит JOIN часть запроса (SELECT FROM JOIN...)
     * @internal
     * @param ISelectBuilder $query
     * @return string
     */
    private function buildSelectJoinPart(ISelectBuilder $query)
    {
        $joins = $query->getJoins();
        if (!count($joins)) {
            return '';
        }

        $result = '';

        foreach ($joins as $join) {
            list($name, $alias) = $join->getTable();
            $result .= "\n\t" . $join->getType() . ' JOIN ';
            $result .= $this->quoteIdentifier($name) . ($alias ? ' AS ' . $this->quoteIdentifier($alias) : '');
            $joinConditions = [];
            foreach ($join->getConditions() as $condition) {
                list($leftColumn, $operator, $rightColumn) = $condition;
                $joinConditions[] = $this->quoteIdentifier($leftColumn)
                    . ' ' . $operator . ' ' . $this->quoteIdentifier($rightColumn);
            }

            if (count($joinConditions) === 1) {
                $result .= ' ON ' . $joinConditions[0];
            } elseif (count($joinConditions) > 1) {
                $result .= ' ON (' . implode(' AND ', $joinConditions) . ')';
            }
        }

        return $result;
    }

    /**
     * Строит GROUP BY часть запроса
     * @internal
     * @param ISelectBuilder $query
     * @return string
     */
    private function buildSelectGroupByPart(ISelectBuilder $query)
    {
        $conditions = $query->getGroupByConditions();
        if (!count($conditions)) {
            return '';
        }

        $result = [];
        foreach ($conditions as $column => $direction) {
            $direction = strtoupper($direction);
            if ($direction == IQueryBuilder::ORDER_ASC) {
                $result[] = $this->quoteIdentifier($column);
            } else {
                $result[] = $this->quoteIdentifier($column) . ' ' . $direction;
            }
        }

        return "\nGROUP BY " . implode(", ", $result);
    }

    /**
     * Строит WHERE часть запроса
     * @internal
     * @param ISelectBuilder|IUpdateBuilder|IDeleteBuilder $query
     * @return string
     */
    private function buildWherePart($query)
    {
        if (!$exprGroup = $query->getWhereExpressionGroup()) {
            return '';
        }

        return "\nWHERE " . $this->buildExpressionGroup($exprGroup);
    }

    /**
     * Строит SET часть запроса
     * @internal
     * @param array $values вида array('columnName' => ':placeholder')
     * @return string
     */
    private function buildSetPart($values)
    {
        $result = [];
        foreach ($values as $columnName => $value) {
            $value = $this->protectExpressionValue($value);
            $result[] = $this->quoteIdentifier($columnName) . ' = ' . $value;
        }

        return implode(', ', $result);
    }

    /**
     * Строит WHERE часть запроса
     * @internal
     * @param ISelectBuilder $query
     * @return string
     */
    private function buildSelectHavingPart(ISelectBuilder $query)
    {
        if (!$exprGroup = $query->getHavingExpressionGroup()) {
            return '';
        }

        return "\nHAVING " . $this->buildExpressionGroup($exprGroup);
    }

    /**
     * Если выражение не плейсхолдер,
     * оно считается именем колонки и экранируется.
     * @param mixed $expression
     * @return string
     */
    private function protectExpressionValue($expression)
    {
        if (strpos($expression, ':') === 0) {
            return $expression;
        }

        return $this->quoteIdentifier($expression);
    }

    /**
     * {@inheritdoc}
     */
    public function buildSelectFoundRowsQuery(ISelectBuilder $query)
    {
        if ($query->getExecuted() && $query->getUseCalcFoundRows()) {
            return 'SELECT FOUND_ROWS()';
        }

        $distinctSql = $query->getDistinct() ? ' DISTINCT' : '';

        return 'SELECT count(*) FROM (SELECT' . $distinctSql . ' ' . $this->buildSelectQueryBody(
            $query
        ) . ') AS `mainQuery`';
    }

    /**
     * Строит запрос для группы выражений
     * @param IExpressionGroup $exprGroup
     * @return string
     */
    private function buildExpressionGroup(IExpressionGroup $exprGroup)
    {
        $result = [];
        foreach ($exprGroup->getExpressions() as $expression) {
            list ($leftCond, $operator, $rightCond) = $expression;
            $leftCond = $this->protectExpressionValue($leftCond);
            $rightCond = $this->protectExpressionValue($rightCond);
            $result[] = $leftCond . ' ' . $operator . ' ' . $rightCond;
        }

        foreach ($exprGroup->getGroups() as $subGroup) {
            $result[] = '(' . $this->buildExpressionGroup($subGroup) . ')';
        }

        if (!count($result)) {
            return '1'; // WHERE 1, if no expressions
        }

        return implode(' ' . $exprGroup->getMode() . ' ', $result);
    }

    /**
     * {@inheritdoc}
     */
    public function buildTruncateQuery($tableName, $cascade = false)
    {
        return $this->getTruncateTableSQL($tableName, $cascade);
    }

    /**
     * {@inheritdoc}
     */
    public function buildDropQuery($tableName, $ifExists = true)
    {
        return $this->getDropTableSQL($tableName, $ifExists);
    }

    /**
     * {@inheritdoc}
     */
    public function buildDisableKeysQuery($tableName)
    {
        $tableName = $this->quoteIdentifier($tableName);

        return 'ALTER TABLE ' . $tableName . ' DISABLE KEYS';
    }

    /**
     * {@inheritdoc}
     */
    public function buildEnableKeysQuery($tableName)
    {
        $tableName = $this->quoteIdentifier($tableName);

        return 'ALTER TABLE ' . $tableName . ' ENABLE KEYS';
    }

    /**
     * {@inheritdoc}
     */
    public function buildDisableForeignKeysQuery()
    {
        return 'SET foreign_key_checks = 0';
    }

    /**
     * {@inheritdoc}
     */
    public function buildEnableForeignKeysQuery()
    {
        return 'SET foreign_key_checks = 1';
    }

    /**
     * {@inheritdoc}
     * @param \Doctrine\DBAL\Connection $connection
     *
     * @throws \umi\dbal\exception\RuntimeException
     */
    public function initConnection(Connection $connection)
    {
        /** @var $pdo PDO */
        $pdo = $connection->getWrappedConnection();
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $params = $connection->getParams();
        if (!isset($params['charset'])) {
            throw new RuntimeException("No connection charset specified");
        }
        $pdo->exec('SET NAMES ' . $pdo->quote($params['charset']));
    }
}
