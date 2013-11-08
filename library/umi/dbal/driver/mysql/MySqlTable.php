<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\driver\mysql;

use umi\dbal\driver\BaseTableScheme;
use umi\dbal\driver\IColumnScheme;
use umi\dbal\driver\IConstraintScheme;
use umi\dbal\driver\IIndexScheme;
use umi\dbal\driver\ITableScheme;
use umi\dbal\exception\RuntimeException;

/**
 * Таблица MySQL базы данных.
 * Инкапсулирует логику работы с MySQL-таблицой
 */
class MySqlTable extends BaseTableScheme implements ITableScheme
{
    /**
     * @var IIndexScheme[] $indexes индексы таблицы
     */
    private $indexes;
    /**
     * @var IIndexScheme $primaryKey информация о Primary Key
     */
    private $primaryKey;
    /**
     * @var IConstraintScheme[] $constraints внешние ключи таблицы
     */
    private $constraints;

    /**
     * {@inheritdoc}
     */
    protected function loadColumns()
    {
        $result = [];
        $sql = 'SHOW FULL COLUMNS FROM ' . $this->dbDriver->sanitizeTableName($this->tableName);
        $queryResult = $this->dbDriver->select($sql)
            ->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($queryResult as $info) {

            $options = [
                IColumnScheme::OPTION_NULLABLE => strtolower($info['Null']) != 'no',
                IColumnScheme::OPTION_DEFAULT_VALUE => $info['Default'],
                IColumnScheme::OPTION_COMMENT => $info['Comment'],
                IColumnScheme::OPTION_PRIMARY_KEY => strtolower($info['Key']) == 'pri',
                IColumnScheme::OPTION_AUTOINCREMENT => strtolower($info['Extra']) == 'auto_increment',
                IColumnScheme::OPTION_COLLATION => $info['Collation']
            ];

            $type = $info['Type'];

            if (preg_match(
                "/(BIT|TINYINT|SMALLINT|MEDIUMINT|INT|INTEGER|BIGINT|REAL|DOUBLE|FLOAT|DECIMAL|NUMERIC|CHAR|VARCHAR|BINARY|VARBINARY)\((\d+)\)\s*(unsigned)*\s*(zerofill)*/i",
                $type,
                $typeInfo
            )
            ) {
                $internalType = $typeInfo[1];
                $options[IColumnScheme::OPTION_LENGTH] = $typeInfo[2];
                if (isset($typeInfo[3])) {
                    $options[IColumnScheme::OPTION_UNSIGNED] = true;
                }
                if (isset($typeInfo[4])) {
                    $options[IColumnScheme::OPTION_ZEROFILL] = true;
                }
            } elseif (preg_match(
                "/(REAL|DOUBLE|FLOAT|DECIMAL|NUMERIC)\((\d+),(\d+)\)\s*(unsigned)*\s*(zerofill)*/i",
                $type,
                $typeInfo
            )
            ) {
                $internalType = $typeInfo[1];
                $options[IColumnScheme::OPTION_LENGTH] = $typeInfo[2];
                $options[IColumnScheme::OPTION_DECIMALS] = $typeInfo[3];
                if (isset($typeInfo[4])) {
                    $options[IColumnScheme::OPTION_UNSIGNED] = true;
                }
                if (isset($typeInfo[5])) {
                    $options[IColumnScheme::OPTION_ZEROFILL] = true;
                }
            } elseif (preg_match(
                "/(TINYINT|SMALLINT|MEDIUMINT|INT|INTEGER|BIGINT|REAL|DOUBLE|FLOAT|DECIMAL|NUMERIC)\s*(unsigned)*\s*(zerofill)*/i",
                $type,
                $typeInfo
            )
            ) {
                $internalType = $typeInfo[1];
                if (isset($typeInfo[2])) {
                    $options[IColumnScheme::OPTION_UNSIGNED] = true;
                }
                if (isset($typeInfo[3])) {
                    $options[IColumnScheme::OPTION_ZEROFILL] = true;
                }
            } else {
                $internalType = $type;
            }

            $column = $this->createColumnSchemeInstance($info['Field'], $internalType, $options);

            $column->setIsModified(false);
            $result[$info['Field']] = $column;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function loadIndexes()
    {
        return $this->getAllKeys();
    }

    /**
     * {@inheritdoc}
     */
    protected function loadConstraints()
    {
        $sql = 'SHOW CREATE TABLE ' . $this->dbDriver->sanitizeTableName($this->tableName);
        $queryResult = $this->dbDriver->select($sql)
            ->fetch(\PDO::FETCH_NUM);
        $tableInfo = $queryResult[1];
        $this->constraints = [];

        if (preg_match_all(
            '/CONSTRAINT\s+`(.+)`\s+FOREIGN\s+KEY\s+\(`(.+)`\)\s+REFERENCES\s+`(.+)`\s+\(`(.+)`\).*/i',
            $tableInfo,
            $foreignKeys
        )
        ) {
            foreach ($foreignKeys[0] as $i => $foreignKey) {
                preg_match('/ON\s+DELETE\s+(CASCADE|SET\s+NULL|RESTRICT|NO\s+ACTION)/i', $foreignKey, $onDelete);
                $onDeleteAction = isset($onDelete) && isset($onDelete[1]) ? $onDelete[1] : null;

                preg_match('/ON\s+UPDATE\s+(CASCADE|SET\s+NULL|RESTRICT|NO\s+ACTION)/i', $foreignKey, $onUpdate);
                $onUpdateAction = isset($onUpdate) && isset($onUpdate[1]) ? $onUpdate[1] : null;

                $constraint = $this->createConstraintSchemeInstance($foreignKeys[1][$i])
                    ->setColumnName($foreignKeys[2][$i])
                    ->setReferenceTableName($foreignKeys[3][$i])
                    ->setReferenceColumnName($foreignKeys[4][$i])
                    ->setOnDeleteAction($onDeleteAction)
                    ->setOnUpdateAction($onUpdateAction)
                    ->setIsModified(false);

                $this->constraints[$foreignKeys[1][$i]] = $constraint;
            }
        }

        return $this->constraints;
    }

    /**
     * {@inheritdoc}
     */
    protected function loadPrimaryKey()
    {
        $this->getAllKeys();

        return $this->primaryKey;
    }

    /**
     * {@inheritdoc}
     */
    protected function extract()
    {
        $sql = 'SHOW CREATE TABLE ' . $this->dbDriver->sanitizeTableName($this->tableName);
        $queryResult = $this->dbDriver->select($sql)
            ->fetch(\PDO::FETCH_NUM);
        $tableInfo = $queryResult[1];

        if (preg_match('/COLLATE=([^\s]+)/is', $tableInfo, $collate)) {
            $this->collation = $collate[1];
        }

        if (preg_match('/DEFAULT\s+CHARSET=([^\s]+)/is', $tableInfo, $charset)) {
            $this->charset = $charset[1];
        }

        if (preg_match('/ENGINE=([^\s]+)/is', $tableInfo, $engine)) {
            $this->engine = $engine[1];
        }

        if (preg_match('/COMMENT=\'(.+)\'/is', $tableInfo, $comment)) {
            $this->comment = $comment[1];
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function generateCreateQueries()
    {
        $result = [];
        $sql = "CREATE TABLE " . $this->dbDriver->sanitizeTableName($this->tableName) . " (\n";
        if (!count($this->getNewColumns())) {
            throw new RuntimeException($this->translate(
                'Cannot create table "{table}" without columns.',
                ['table' => $this->tableName]
            ));
        }
        $changes = [];
        foreach ($this->getNewColumns() as $column) {
            $changes[] = "\t" . $this->generateColumnQuery($column);
        }
        $primaryKey = $this->getPrimaryKey();
        if ($primaryKey && $primaryKey->getIsNew()) {
            $changes[] = "\t" . $this->generateIndexQuery($primaryKey);
        }
        foreach ($this->getNewIndexes() as $index) {
            $changes[] = "\t" . $this->generateIndexQuery($index);
        }
        foreach ($this->getNewConstraints() as $constraint) {
            $changes[] = "\t" . $this->generateConstraintQuery($constraint);
        }
        $sql .= implode(",\n", $changes) . "\n)";

        if ($this->engine) {
            $sql .= ' ENGINE=' . $this->engine;
        }
        if ($this->charset) {
            $sql .= ' DEFAULT CHARSET=' . $this->charset;
        }
        if ($this->collation) {
            $sql .= ' COLLATE=' . $this->collation;
        }
        if ($this->comment) {
            $sql .= ' COMMENT=\'' . $this->comment . '\'';
        }
        $result[] = $sql;

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function generateAlterQueries()
    {
        $result = [];

        $sql = 'ALTER TABLE ' . $this->dbDriver->sanitizeTableName($this->tableName) . "\n";
        $changes = [];

        $changes = array_merge($changes, $this->generateDropConstraintsQuery());
        $changes = array_merge($changes, $this->generateDropIndexesQuery());
        $changes = array_merge($changes, $this->generateDropColumnsQuery());
        $changes = array_merge($changes, $this->generateAddColumnsQuery());
        $changes = array_merge($changes, $this->generateModifyColumnsQuery());
        $changes = array_merge($changes, $this->generateAddIndexesQuery());
        $changes = array_merge($changes, $this->generateAddConstraintsQuery());

        if ($this->engine) {
            $changes[] = 'ENGINE=' . $this->engine;
        }
        if ($this->charset) {
            $changes[] = 'DEFAULT CHARACTER SET=' . $this->charset;
        }
        if ($this->collation) {
            $changes[] = 'COLLATE ' . $this->collation;
        }
        if ($this->comment) {
            $changes[] = 'COMMENT=\'' . $this->comment . '\'';
        }

        $result[] = $sql . implode(",\n", $changes);

        if (count($this->getModifiedConstraints())) {
            $constraintChanges = [];
            foreach ($this->getModifiedConstraints() as $constraint) {
                $constraintChanges[] = "\tADD " . $this->generateConstraintQuery($constraint);
            }
            $result[] = $sql . implode(",\n", $constraintChanges);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function generateDropQueries()
    {
        $result = [];
        $result[] = $this->dbDriver->buildDropQuery($this->tableName);

        return $result;
    }

    /**
     * Возвращает информацию о всех индексах таблицы включая primary key.
     * @return IIndexScheme[]
     */
    private function getAllKeys()
    {
        if (is_array($this->indexes)) {
            return $this->indexes;
        }

        $this->indexes = [];
        $sql = 'SHOW INDEX FROM ' . $this->dbDriver->sanitizeTableName($this->tableName);
        $queryResult = $this->dbDriver->select($sql)
            ->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($queryResult as $nextIndex) {
            $keyName = $nextIndex['Key_name'];
            if (strtoupper($keyName) == 'PRIMARY') {
                if (is_null($this->primaryKey)) {
                    $index = $this->primaryKey = $this->createIndexSchemeInstance($keyName);
                } else {
                    $index = $this->primaryKey;
                }
            } else {
                if (!isset($this->indexes[$keyName])) {
                    $this->indexes[$keyName] = $this->createIndexSchemeInstance($keyName);
                    $index = $this->indexes[$keyName];
                } else {
                    $index = $this->indexes[$keyName];
                }
            }
            $index->setIsUnique(!$nextIndex['Non_unique']);
            $index->setType($nextIndex['Index_type']);
            $index->addColumn($nextIndex['Column_name'], $nextIndex['Sub_part']);
            $index->setIsModified(false);

        }

        return $this->indexes;
    }

    /**
     * Геренирует часть sql-запроса для столбца
     * @param IColumnScheme $column
     * @return string
     */
    private function generateColumnQuery(IColumnScheme $column)
    {
        $query = $this->dbDriver->sanitizeColumnName($column->getName()) . ' ' . $column->getInternalType();
        if (null != ($size = $column->getLength())) {
            $query .= '(' . $size;
            if (null != ($decimals = $column->getDecimals())) {
                $query .= ',' . $decimals;
            }
            $query .= ')';
        }
        if ($column->getIsUnsigned()) {
            $query .= ' UNSIGNED';
        }
        if ($column->getIsZerofill()) {
            $query .= ' ZEROFILL';
        }
        if (null != ($collation = $column->getCollation())) {
            $query .= ' COLLATE ' . $collation;
        }
        if (!$column->getIsNullable()) {
            $query .= ' NOT NULL';
        }
        if (!is_null($column->getDefaultValue())) {
            $query .= ' DEFAULT \'' . $column->getDefaultValue() . '\'';
        }
        if ($column->getIsAutoIncrement()) {
            $query .= ' AUTO_INCREMENT';
        }
        if (null != ($comment = $column->getComment())) {
            $query .= ' COMMENT \'' . $comment . '\'';
        }

        return $query;
    }

    /**
     * Геренирует части sql-запроса для удаления внешних ключей
     * @return array
     */
    private function generateDropConstraintsQuery()
    {
        $result = [];
        /**
         * @var IConstraintScheme $constraint
         */
        foreach ($this->getDeletedConstraints() as $constraint) {
            $result[] = "\tDROP FOREIGN KEY " . $this->dbDriver->sanitizeTableName($constraint->getName());
        }
        foreach ($this->getModifiedConstraints() as $constraint) {
            $result[] = "\tDROP FOREIGN KEY " . $this->dbDriver->sanitizeTableName($constraint->getName());
        }

        return $result;
    }

    /**
     * Геренирует части sql-запроса для добавления внешних ключей
     * @return array
     */
    private function generateAddConstraintsQuery()
    {
        $result = [];
        foreach ($this->getNewConstraints() as $constraint) {
            $result[] = "\tADD " . $this->generateConstraintQuery($constraint);
        }

        return $result;
    }

    /**
     * Геренирует части sql-запроса для удаления индексов
     * @return array
     */
    private function generateDropIndexesQuery()
    {
        $result = [];
        $primaryKey = $this->getPrimaryKey();
        foreach ($this->getDeletedIndexes() as $index) {
            /**
             * @var IIndexScheme $index
             */
            $result[] = "\tDROP KEY " . $this->dbDriver->sanitizeTableName($index->getName());
        }
        foreach ($this->getModifiedIndexes() as $index) {
            $result[] = "\tDROP KEY " . $this->dbDriver->sanitizeTableName($index->getName());
        }
        if ($primaryKey && ($primaryKey->getIsDeleted() || $primaryKey->getIsModified()) && !$primaryKey->getIsNew()) {
            $result[] = "\tDROP PRIMARY KEY";
        }

        return $result;
    }

    /**
     * Геренирует части sql-запроса для добавления индексов
     * @return array
     */
    private function generateAddIndexesQuery()
    {
        $result = [];
        $primaryKey = $this->getPrimaryKey();
        if ($primaryKey && ($primaryKey->getIsNew() || $primaryKey->getIsModified())) {
            $result[] = "\tADD " . $this->generateIndexQuery($primaryKey);
        }
        foreach ($this->getNewIndexes() as $index) {
            $result[] = "\tADD " . $this->generateIndexQuery($index);
        }
        foreach ($this->getModifiedIndexes() as $index) {
            $result[] = "\tADD " . $this->generateIndexQuery($index);
        }

        return $result;
    }

    /**
     * Геренирует части sql-запроса для изменения колонок
     * @return array
     */
    private function generateModifyColumnsQuery()
    {
        $result = [];
        foreach ($this->getModifiedColumns() as $column) {
            $result[] = "\tMODIFY " . $this->generateColumnQuery($column);
        }

        return $result;
    }

    /**
     * Геренирует части sql-запроса для удаления колонок
     * @return array
     */
    private function generateDropColumnsQuery()
    {
        $result = [];
        foreach ($this->getDeletedColumns() as $column) {
            /**
             * @var IColumnScheme $column
             */
            $result[] = "\tDROP " . $this->dbDriver->sanitizeColumnName($column->getName());
        }

        return $result;
    }

    /**
     * Геренирует части sql-запроса для добавления колонок
     * @return array
     */
    private function generateAddColumnsQuery()
    {
        $result = [];
        foreach ($this->getNewColumns() as $column) {
            $result[] = "\tADD " . $this->generateColumnQuery($column);
        }

        return $result;
    }

    /**
     * Генерирует часть sql-запроса для индекса
     * @param IIndexScheme $index
     * @return string
     */
    private function generateIndexQuery($index)
    {
        $query = '';
        if ($index->getIsUnique() && strtoupper($index->getName()) != 'PRIMARY') {
            $query .= 'UNIQUE ';
        }
        if (strtoupper($index->getType()) == 'FULLTEXT') {
            $query .= 'FULLTEXT ';
        }
        if (strtoupper($index->getName()) != 'PRIMARY') {
            $query .= 'KEY ' . $this->dbDriver->sanitizeTableName($index->getName()) . ' (';
        } else {
            $query .= 'PRIMARY KEY (';
        }
        $columns = $index->getColumns();
        $indexColumns = [];
        foreach ($columns as $columnInfo) {
            $columnName = $this->dbDriver->sanitizeColumnName($columnInfo['name']);
            if ($columnInfo['length']) {
                $columnName .= '(' . $columnInfo['length'] . ')';
            }
            $indexColumns[] = $columnName;
        }
        $query .= implode(',', $indexColumns) . ')';

        return $query;
    }

    /**
     * Генерирует часть sql-запроса для внешнего ключа
     * @param IConstraintScheme $constraint
     * @return string
     */
    private function generateConstraintQuery(IConstraintScheme $constraint)
    {
        $query = 'CONSTRAINT ';
        $query .= $this->dbDriver->sanitizeTableName(
                $constraint->getName()
            ) . ' FOREIGN KEY (' . $this->dbDriver->sanitizeColumnName($constraint->getColumnName()) . ')';
        $query .= ' REFERENCES ' . $this->dbDriver->sanitizeTableName(
                $constraint->getReferenceTableName()
            ) . ' (' . $this->dbDriver->sanitizeColumnName($constraint->getReferenceColumnName()) . ')';
        if ($constraint->getOnDeleteAction()) {
            $query .= ' ON DELETE ' . $constraint->getOnDeleteAction();
        }
        if ($constraint->getOnUpdateAction()) {
            $query .= ' ON UPDATE ' . $constraint->getOnUpdateAction();
        }

        return $query;
    }

}
