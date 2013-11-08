<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\driver\mysql;

use PDO;
use umi\dbal\builder\IDeleteBuilder;
use umi\dbal\builder\IExpressionGroup;
use umi\dbal\builder\IInsertBuilder;
use umi\dbal\builder\IQueryBuilder;
use umi\dbal\builder\ISelectBuilder;
use umi\dbal\builder\IUpdateBuilder;
use umi\dbal\driver\BaseDriver;
use umi\dbal\driver\IColumnScheme;
use umi\dbal\driver\IDbDriver;
use umi\dbal\exception\IException;
use umi\dbal\exception\RangeException;
use umi\dbal\exception\RuntimeException;

/**
 * Драйвер MySQL баз данных.
 * Реализует логику работы с MySQL-базой данных.
 */
class MySqlDriver extends BaseDriver implements IDbDriver
{
    /**
     * @var array $columnTypes список опций типов колонок
     */
    public $columnTypes = [
        IColumnScheme::TYPE_INT       => [
            IColumnScheme::OPTION_TYPE => 'int',
            IColumnScheme::OPTION_SIZE => IColumnScheme::SIZE_NUMERIC_NORMAL
        ],
        IColumnScheme::TYPE_DECIMAL   => [
            IColumnScheme::OPTION_TYPE => 'decimal'
        ],
        IColumnScheme::TYPE_REAL      => [
            IColumnScheme::OPTION_TYPE => 'float',
            IColumnScheme::OPTION_SIZE => IColumnScheme::SIZE_NUMERIC_NORMAL
        ],
        IColumnScheme::TYPE_BOOL      => [
            IColumnScheme::OPTION_TYPE => 'tinyint',
            IColumnScheme::OPTION_SIZE => IColumnScheme::SIZE_NUMERIC_TINY
        ],
        IColumnScheme::TYPE_SERIAL    => [
            IColumnScheme::OPTION_TYPE          => 'bigint',
            IColumnScheme::OPTION_UNSIGNED      => true,
            IColumnScheme::OPTION_AUTOINCREMENT => true,
            IColumnScheme::OPTION_PRIMARY_KEY   => true,
            IColumnScheme::OPTION_NULLABLE      => false,
            IColumnScheme::OPTION_SIZE          => IColumnScheme::SIZE_NUMERIC_BIG,
            IColumnScheme::OPTION_LENGTH        => 20,
            IColumnScheme::OPTION_ZEROFILL      => false
        ],
        IColumnScheme::TYPE_RELATION  => [
            IColumnScheme::OPTION_TYPE     => 'bigint',
            IColumnScheme::OPTION_UNSIGNED => true,
            IColumnScheme::OPTION_SIZE     => IColumnScheme::SIZE_NUMERIC_BIG,
            IColumnScheme::OPTION_LENGTH   => 20,
            IColumnScheme::OPTION_ZEROFILL => false
        ],
        IColumnScheme::TYPE_VARCHAR   => [
            IColumnScheme::OPTION_TYPE   => 'varchar',
            IColumnScheme::OPTION_LENGTH => 255
        ],
        IColumnScheme::TYPE_CHAR      => [
            IColumnScheme::OPTION_TYPE   => 'char',
            IColumnScheme::OPTION_LENGTH => 255
        ],
        IColumnScheme::TYPE_TEXT      => [
            IColumnScheme::OPTION_TYPE => 'text',
            IColumnScheme::OPTION_SIZE => IColumnScheme::SIZE_TEXT_NORMAL
        ],
        IColumnScheme::TYPE_BLOB      => [
            IColumnScheme::OPTION_TYPE => 'blob',
            IColumnScheme::OPTION_SIZE => IColumnScheme::SIZE_TEXT_NORMAL
        ],
        IColumnScheme::TYPE_TIMESTAMP => [
            IColumnScheme::OPTION_TYPE => 'timestamp'
        ],
        IColumnScheme::TYPE_DATE      => [
            IColumnScheme::OPTION_TYPE => 'date'
        ],
        IColumnScheme::TYPE_TIME      => [
            IColumnScheme::OPTION_TYPE => 'time'
        ],
        IColumnScheme::TYPE_DATETIME  => [
            IColumnScheme::OPTION_TYPE => 'datetime'
        ]
    ];

    /**
     * {@inheritdoc}
     */
    public function getColumnInternalTypeBySize($internalType, $size)
    {
        $size = (int) $size;
        switch ($internalType) {
            case 'tinyint':
            case 'smallint':
            case 'mediumint':
            case 'int':
            case 'bigint':
            case 'integer':
            {
                switch (true) {
                    case ($size <= IColumnScheme::SIZE_NUMERIC_TINY):
                    {
                        return 'tinyint';
                    }
                    case ($size <= IColumnScheme::SIZE_NUMERIC_SMALL):
                    {
                        return 'smallint';
                    }
                    case ($size <= IColumnScheme::SIZE_NUMERIC_MEDIUM):
                    {
                        return 'mediumint';
                    }
                    case ($size <= IColumnScheme::SIZE_NUMERIC_NORMAL):
                    {
                        return 'int';
                    }
                    case ($size <= IColumnScheme::SIZE_NUMERIC_BIG):
                    {
                        return 'bigint';
                    }
                    default:
                        {
                        throw new RangeException($this->translate(
                            'Size {size} is out of range for numeric column types.',
                            ['size' => $size]
                        ));
                        }
                }
            }
                break;
            case 'tinytext':
            case 'text':
            case 'mediumtext':
            case 'longtext':
            {
                switch (true) {
                    case ($size <= IColumnScheme::SIZE_TEXT_TINY):
                    {
                        return 'tinytext';
                    }
                    case ($size <= IColumnScheme::SIZE_TEXT_NORMAL):
                    {
                        return 'text';
                    }
                    case ($size <= IColumnScheme::SIZE_TEXT_MEDIUM):
                    {
                        return 'mediumtext';
                    }
                    case ($size <= IColumnScheme::SIZE_TEXT_LONG):
                    {
                        return 'longtext';
                    }
                    default:
                        {
                        throw new RangeException($this->translate(
                            'Size {size} is out of range for text column types.',
                            ['size' => $size]
                        ));
                        }
                }
            }
                break;
            case 'tinyblob':
            case 'blob':
            case 'mediumblob':
            case 'longblob':
            {
                switch (true) {
                    case ($size <= IColumnScheme::SIZE_TEXT_TINY):
                    {
                        return 'tinyblob';
                    }
                    case ($size <= IColumnScheme::SIZE_TEXT_NORMAL):
                    {
                        return 'blob';
                    }
                    case ($size <= IColumnScheme::SIZE_TEXT_MEDIUM):
                    {
                        return 'mediumblob';
                    }
                    case ($size <= IColumnScheme::SIZE_TEXT_LONG):
                    {
                        return 'longblob';
                    }
                    default:
                        {
                        throw new RangeException($this->translate(
                            'Size {size} is out of range for blob column types.',
                            ['size' => $size]
                        ));
                        }
                }
            }
                break;
            case 'float':
            case 'double':
            {
                switch (true) {
                    case ($size <= IColumnScheme::SIZE_NUMERIC_NORMAL):
                    {
                        return 'float';
                    }
                    case ($size <= IColumnScheme::SIZE_NUMERIC_BIG):
                    {
                        return 'double';
                    }
                    default:
                        {
                        throw new RangeException($this->translate(
                            'Size {size} is out of range for real column types.',
                            ['size' => $size]
                        ));
                        }
                }
            }
                break;
            default:
                {
                return $internalType;
                }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        return in_array('mysql', PDO::getAvailableDrivers());
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
        $whatSql = $this->sanitizeTableName($query->getTableName());
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
        $whatSql = $this->sanitizeTableName($query->getTableName());
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
        $fromSql = $this->sanitizeTableName($query->getTableName());
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
     * {@inheritdoc}
     */
    public function buildDisableKeysQuery($tableName)
    {
        $tableName = $this->sanitizeTableName($tableName);

        return 'ALTER TABLE ' . $tableName . ' DISABLE KEYS';
    }

    /**
     * {@inheritdoc}
     */
    public function buildEnableKeysQuery($tableName)
    {
        $tableName = $this->sanitizeTableName($tableName);

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
     * {@inheritdoc}
     */
    protected function initPDOInstance(PDO $pdo)
    {
        //$pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $pdo->exec('SET NAMES ' . $pdo->quote($this->charset));
    }

    /**
     * {@inheritdoc}
     */
    protected function loadTableScheme($name)
    {
        $table = $this->createTableSchemeInstance($name);
        try {
            $table->reload(); // load columns
        } catch (\Exception $e) {
            throw new RuntimeException($this->translate(
                'Cannot load table "{table}" for "{dsn}".',
                ['table' => $name, 'dsn' => $this->dsn]
            ), 0, $e);
        }

        return $table;
    }

    /**
     * {@inheritdoc}
     */
    protected function loadTableNames()
    {
        $result = [];
        try {
            $tables = $this->select('SHOW TABLES');
        } catch (\Exception $e) {
            throw new RuntimeException($this->translate(
                'Cannot load tables for "{dsn}".',
                ['dsn' => $this->dsn]
            ), 0, $e);
        }

        while (null != ($row = $tables->fetchColumn())) {
            $result[] = $row;
        }

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
                $result[] = $name . ($alias ? ' AS ' . $this->sanitizeColumnName($alias) : '');
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
                $name = $this->sanitizeTableName($name);
                $result[] = $name . ($alias ? ' AS ' . $this->sanitizeTableName($alias) : '');
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
            $result .= $this->sanitizeTableName($name) . ($alias ? ' AS ' . $this->sanitizeTableName($alias) : '');
            $joinConditions = [];
            foreach ($join->getConditions() as $condition) {
                list($leftColumn, $operator, $rightColumn) = $condition;
                $joinConditions[] = $this->sanitizeColumnName(
                        $leftColumn
                    ) . ' ' . $operator . ' ' . $this->sanitizeColumnName($rightColumn);
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
                $result[] = $this->sanitizeColumnName($column);
            } else {
                $result[] = $this->sanitizeColumnName($column) . ' ' . $direction;
            }
        }

        return "\nGROUP BY " . implode(", ", $result);
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

        return $this->sanitizeColumnName($expression);
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
            $result[] = $this->sanitizeColumnName($column) . ' ' . strtoupper($direction);
        }

        return "\nORDER BY " . implode(", ", $result);
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
            $result[] = $this->sanitizeColumnName($columnName) . ' = ' . $value;
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
}
