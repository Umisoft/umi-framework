<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\driver\sqlite;

use PDO;
use umi\dbal\builder\IDeleteBuilder;
use umi\dbal\builder\IExpressionGroup;
use umi\dbal\builder\IInsertBuilder;
use umi\dbal\builder\ISelectBuilder;
use umi\dbal\builder\IUpdateBuilder;
use umi\dbal\driver\BaseDriver;
use umi\dbal\driver\IColumnScheme;
use umi\dbal\driver\IDbDriver;
use umi\dbal\exception\IException;
use umi\dbal\exception\RuntimeException;

/**
 * Драйвер SQLite.
 */
class SqliteDriver extends BaseDriver implements IDbDriver
{
    /**
     * @var array $columnTypes список опций типов колонок
     */
    public $columnTypes = [
        IColumnScheme::TYPE_INT       => [
            IColumnScheme::OPTION_TYPE => 'INTEGER'
        ],
        IColumnScheme::TYPE_DECIMAL   => [
            IColumnScheme::OPTION_TYPE => 'NUMERIC'
        ],
        IColumnScheme::TYPE_REAL      => [
            IColumnScheme::OPTION_TYPE => 'REAL'
        ],
        IColumnScheme::TYPE_BOOL      => [
            IColumnScheme::OPTION_TYPE => 'INTEGER',
        ],
        IColumnScheme::TYPE_SERIAL    => [
            IColumnScheme::OPTION_TYPE          => 'INTEGER',
            IColumnScheme::OPTION_AUTOINCREMENT => true,
            IColumnScheme::OPTION_PRIMARY_KEY   => true,
            IColumnScheme::OPTION_NULLABLE      => false
        ],
        IColumnScheme::TYPE_RELATION  => [
            IColumnScheme::OPTION_TYPE => 'INTEGER'
        ],
        IColumnScheme::TYPE_VARCHAR   => [
            IColumnScheme::OPTION_TYPE => 'TEXT'
        ],
        IColumnScheme::TYPE_CHAR      => [
            IColumnScheme::OPTION_TYPE => 'TEXT'
        ],
        IColumnScheme::TYPE_TEXT      => [
            IColumnScheme::OPTION_TYPE => 'TEXT'
        ],
        IColumnScheme::TYPE_BLOB      => [
            IColumnScheme::OPTION_TYPE => 'BLOB'
        ],
        IColumnScheme::TYPE_TIMESTAMP => [
            IColumnScheme::OPTION_TYPE => 'INTEGER'
        ],
        IColumnScheme::TYPE_DATE      => [
            IColumnScheme::OPTION_TYPE => 'TEXT'
        ],
        IColumnScheme::TYPE_TIME      => [
            IColumnScheme::OPTION_TYPE => 'TEXT'
        ],
        IColumnScheme::TYPE_DATETIME  => [
            IColumnScheme::OPTION_TYPE => 'TEXT'
        ]
    ];

    /**
     * {@inheritdoc}
     */
    public function getColumnInternalTypeBySize($internalType, $size)
    {
        return $internalType;
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        return in_array('sqlite', PDO::getAvailableDrivers());
    }

    /**
     * {@inheritdoc}
     */
    public function concat($string1, $string2)
    {
        return $string1 . ' || ' . $string2;
    }

    /**
     * {@inheritdoc}
     */
    public function buildSelectQuery(ISelectBuilder $query)
    {

        $orderBy = $this->buildOrderByPart($query);

        $limitSql = '';
        if (null != ($limit = $query->getLimit())) {
            $limitSql = "\nLIMIT " . $limit . ' OFFSET ' . $query->getOffset();
        }

        $result = $this->buildSelectQueryBody($query)
            . $orderBy
            . $limitSql;

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function buildUpdateQuery(IUpdateBuilder $query)
    {
        $ignoreSql = $query->getIsIgnore() ? ' OR IGNORE' : '';
        $whatSql = $this->sanitizeTableName($query->getTableName());
        $whereSql = $this->buildWherePart($query);
        $setSql = $this->buildSetPart($query->getValues());

        $result = 'UPDATE' . $ignoreSql . ' ' . $whatSql
            . $setSql
            . $whereSql;

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function buildInsertQuery(IInsertBuilder $query)
    {
        if (!is_null($query->getOnDuplicateKeyValues())) {
            return $this->buildInsertUpdateQueries($query);
        }

        $ignoreSql = $query->getIsIgnore() ? ' OR IGNORE' : '';
        $whatSql = $this->sanitizeTableName($query->getTableName());
        $setSql = $this->buildValuesPart($query->getValues());

        $result = 'INSERT' . $ignoreSql . ' INTO ' . $whatSql . $setSql;

        return $result;
    }

    /**
     * Эмулирует INSERT ON DUPLICATE KEY UPDATE
     * @internal
     * @param IInsertBuilder $query insert-запрос
     * @return string
     */
    public function buildInsertUpdateQueries(IInsertBuilder $query)
    {
        $values = $query->getValues();
        $whatSql = $this->sanitizeTableName($query->getTableName());
        $valuesSql = $this->buildValuesPart($values);

        $result = 'INSERT OR IGNORE INTO ' . $whatSql . $valuesSql . ";\n";

        $whereConditions = [];
        $columns = $query->getOnDuplicateKeyColumns();
        foreach ($values as $columnName => $placeholder) {
            if (in_array($columnName, $columns)) {
                $columnName = $this->sanitizeColumnName($columnName);
                $placeholder = $this->protectExpressionValue($placeholder);
                $whereConditions[] = $columnName . ' = ' . $placeholder;
            }
        }
        $whereSql = "\nWHERE " . (count($whereConditions) ? implode(' AND ', $whereConditions) : '1');
        $setSql = $this->buildSetPart($query->getOnDuplicateKeyValues());
        $result .= 'UPDATE ' . $whatSql . $setSql . $whereSql . ';';

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function buildDeleteQuery(IDeleteBuilder $query)
    {
        $fromSql = $this->sanitizeTableName($query->getTableName());
        $whereSql = $this->buildWherePart($query);

        $result = 'DELETE FROM ' . $fromSql . $whereSql;

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function buildDisableKeysQuery($tableName)
    {
        throw new RuntimeException($this->translate(
            'Sqlite driver does not support \'alter table ... disable keys\' queries.'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildEnableKeysQuery($tableName)
    {
        throw new RuntimeException($this->translate(
            'Sqlite driver does not support \'alter table ... enable keys\' queries.'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildDisableForeignKeysQuery()
    {
        return 'PRAGMA foreign_keys = OFF';
    }

    /**
     * {@inheritdoc}
     */
    public function buildEnableForeignKeysQuery()
    {
        return 'PRAGMA foreign_keys = ON';
    }

    /**
     * {@inheritdoc}
     */
    public function buildSelectFoundRowsQuery(ISelectBuilder $query)
    {
        return 'SELECT count(*) FROM (' . $this->buildSelectQueryBody($query) . ') AS mainQuery';
    }

    /**
     * {@inheritdoc}
     */
    protected function initPDOInstance(PDO $pdo)
    {
        $pdo->exec($this->buildEnableForeignKeysQuery());
    }

    /**
     * Строит sql-запрос на выборку данных без LIMIT и ORDER BY
     * @param ISelectBuilder $query запрос
     * @throws IException если не удалось построить запрос
     * @return string
     */
    protected function buildSelectQueryBody($query)
    {
        $distinctSql = $query->getDistinct() ? ' DISTINCT' : '';
        $whatSql = $this->buildSelectWhatPart($query);
        $fromSql = $this->buildSelectFromPart($query);
        $whereSql = $this->buildWherePart($query);
        $groupBySql = $this->buildSelectGroupByPart($query);
        $havingSql = $this->buildSelectHavingPart($query);
        $joinSql = $this->buildSelectJoinPart($query);

        $result = 'SELECT' . $distinctSql . ' ' . $whatSql
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
            $tables = $this->select('SELECT DISTINCT tbl_name FROM sqlite_master WHERE tbl_name <> "sqlite_sequence"');
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
            $result[] = $this->sanitizeColumnName($column);
        }

        return "\nGROUP BY " . implode(", ", $result);
    }

    /**
     * Если выражение не плейсхолдер,
     * оно считается именем колонки и экранируется.
     * @param mixed $expression
     * @return mixed
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
        foreach ($values as $columnName => $placeholder) {
            $result[] = $this->sanitizeColumnName($columnName) . ' = ' . $placeholder;
        }

        return "\nSET " . implode(', ', $result);
    }

    /**
     * Строит часть INSERT запроса
     * @internal
     * @param array $values вида array('columnName' => ':placeholder')
     * @return string
     */
    private function buildValuesPart($values)
    {
        $columnNames = [];
        $placeholders = [];
        foreach ($values as $columnName => $placeholder) {
            $columnNames[] = $this->sanitizeColumnName($columnName);
            $placeholders[] = $placeholder;
        }

        return "\n( " . implode(', ', $columnNames) . ' ) VALUES ( ' . implode(', ', $placeholders) . ' )';
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
