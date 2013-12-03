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
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Schema\Table;
use PDO;
use umi\dbal\builder\IDeleteBuilder;
use umi\dbal\builder\IExpressionGroup;
use umi\dbal\builder\IInsertBuilder;
use umi\dbal\builder\ISelectBuilder;
use umi\dbal\builder\IUpdateBuilder;
use umi\dbal\driver\IDialect;
use umi\dbal\exception\IException;
use umi\dbal\exception\RuntimeException;
use umi\i18n\TLocalizable;

class SqliteDialect extends SqlitePlatform implements IDialect
{
    use TLocalizable;

    private $fkSupported = false;

    public function supportsForeignKeyConstraints()
    {
        return $this->fkSupported;
    }

    /**
     * {@inheritdoc}
     */
    public function buildSelectQuery(ISelectBuilder $query)
    {

        $orderBy = $this->buildOrderByPart($query);

        $limitSql = '';
        if (null != ($limit = $query->getLimit())) {
            $limitSql = $this->buildLimitPart($limit, $query);
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
        $whatSql = $this->quoteIdentifier($query->getTableName());
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
        $whatSql = $this->quoteIdentifier($query->getTableName());
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
    protected function buildInsertUpdateQueries(IInsertBuilder $query)
    {
        $values = $query->getValues();
        $whatSql = $this->quoteIdentifier($query->getTableName());
        $valuesSql = $this->buildValuesPart($values);

        $result = 'INSERT OR IGNORE INTO ' . $whatSql . $valuesSql . ";\n";

        $whereConditions = [];
        $columns = $query->getOnDuplicateKeyColumns();
        foreach ($values as $columnName => $placeholder) {
            if (in_array($columnName, $columns)) {
                $columnName = $this->quoteIdentifier($columnName);
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
        $fromSql = $this->quoteIdentifier($query->getTableName());
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
     * Возвращает запрос на удаление таблицы.
     * По сравнению с Doctrine платформой, поддерживает флаг ifExists
     *
     * @param string|Table $table Имя таблицы
     * @param bool $ifExists Добавить к запросу проверку на существование
     *
     * @throws \InvalidArgumentException
     * @return string
     */
    public function getDropTableSQL($table, $ifExists = true)
    {
        //todo! add check to other dialects or refer to parent::getDropTableSQL()
        if ($table instanceof Table) {
            $table = $table->getQuotedName($this);
        } elseif (!is_string($table)) {
            throw new \InvalidArgumentException(
                'getDropTableSQL() expects $table parameter to be string or \Doctrine\DBAL\Schema\Table.'
            );
        }
        return 'DROP TABLE ' . ($ifExists ? 'IF EXISTS ' : '') . $table;
    }

    /**
     * {@inheritdoc}
     */
    public function buildSelectFoundRowsQuery(ISelectBuilder $query)
    {
        return 'SELECT count(*) FROM (' . $this->buildSelectQueryBody($query) . ') AS mainQuery';
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
            $result[] = $this->quoteIdentifier($column);
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

        return $this->quoteIdentifier($expression);
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
            $result[] = $this->quoteIdentifier($column) . ' ' . strtoupper($direction);
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
            $result[] = $this->quoteIdentifier($columnName) . ' = ' . $placeholder;
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
            $columnNames[] = $this->quoteIdentifier($columnName);
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

    /**
     * Строит и возвращает sql-запрос для отключения индексов в отдельной таблице
     * @param string $tableName
     * @return string
     */
    public function getDisableKeysSQL($tableName)
    {
        return $this->buildDisableKeysQuery($tableName);
    }

    /**
     * Строит и возвращает sql-запрос для включения индексов в отдельной таблице
     * @param string $tableName
     * @return string
     */
    public function getEnableKeysSQL($tableName)
    {
        return $this->buildEnableKeysQuery($tableName);
    }

    /**
     * {@inheritdoc}
     */
    public function getDisableForeignKeysSQL()
    {
        return $this->buildDisableForeignKeysQuery();
    }

    /**
     * {@inheritdoc}
     */
    public function getEnableForeignKeysSQL()
    {
        return $this->buildEnableForeignKeysQuery();
    }

    /**
     * {@inheritdoc}
     */
    public function buildTruncateQuery($tableName, $cascade = false)
    {
        $this->getTruncateTableSQL($tableName, $cascade = false);
    }

    /**
     * {@inheritdoc}
     */
    public function buildDropQuery($tableName, $ifExists = true)
    {
        $this->getDropTableSQL($tableName, $ifExists = true);
    }

    /**
     * {@inheritdoc}
     */
    public function initPDOInstance(Connection $connection, PDO $pdo)
    {
        // find out, if driver version allows FK Constraints
        $this->fkSupported = version_compare('3.6.19', $pdo->getAttribute(PDO::ATTR_SERVER_VERSION)) < 0;
        $pdo->exec($this->buildEnableForeignKeysQuery());
    }

    /**
     * @param int $limit
     * @param ISelectBuilder $query
     * @return string
     */
    protected function buildLimitPart($limit, ISelectBuilder $query)
    {
        $limitSql = "\nLIMIT " . $limit . ' OFFSET ' . $query->getOffset();

        return $limitSql;
    }
}
