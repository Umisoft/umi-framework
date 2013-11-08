<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\driver\sqlite;

use umi\dbal\driver\BaseTableScheme;
use umi\dbal\driver\IColumnScheme;
use umi\dbal\driver\IConstraintScheme;
use umi\dbal\driver\IIndexScheme;
use umi\dbal\driver\ITableScheme;
use umi\dbal\exception\RuntimeException;

/**
 * Таблица SQLite.
 * Инкапсулирует логику работы с Sqlite-таблицой.
 */
class SqliteTable extends BaseTableScheme implements ITableScheme
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
        $sql = 'PRAGMA table_info(' . $this->dbDriver->sanitizeTableName($this->tableName) . ')';
        $queryResult = $this->dbDriver->select($sql)
            ->fetchAll(\PDO::FETCH_ASSOC);
        if (!count($queryResult)) {
            throw new RuntimeException($this->translate(
                'Cannot load columns for table "{table}".',
                ['table' => $this->tableName]
            ));
        }
        foreach ($queryResult as $info) {

            $defaultValue = $info['dflt_value'];
            if (!is_null($defaultValue)) {
                $firstSymbol = substr($defaultValue, 0, 1);
                if ($firstSymbol == '"' || $firstSymbol == "'") {
                    $defaultValue = substr($defaultValue, 1, -1);
                }
            }

            $options = [
                IColumnScheme::OPTION_NULLABLE => !$info['notnull'],
                IColumnScheme::OPTION_DEFAULT_VALUE => $defaultValue,
                IColumnScheme::OPTION_AUTOINCREMENT => $info['pk'],
                IColumnScheme::OPTION_PRIMARY_KEY => $info['pk']
            ];

            $column = $this->createColumnSchemeInstance($info['name'], $info['type'], $options);
            $column->setIsNew(false);
            $column->setIsModified(false);

            $result[$info['name']] = $column;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function extract()
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function generateCreateQueries()
    {
        $result = [];
        $sql = "CREATE TABLE " . $this->dbDriver->sanitizeTableName($this->tableName) . " (\n";
        $newColumns = $this->getNewColumns();

        if (!count($newColumns)) {
            throw new RuntimeException($this->translate(
                'Cannot create table "{table}" without columns.',
                ['table' => $this->tableName]
            ));
        }

        $changes = [];
        foreach ($newColumns as $column) {
            $changes[] = "\t" . $this->generateColumnQuery($column);
        }

        foreach ($this->getNewConstraints() as $constraint) {
            $changes[] = "\t" . $this->generateConstraintQuery($constraint);
        }

        $result[] = $sql . implode(",\n", $changes) . "\n)";

        foreach ($this->getNewIndexes() as $index) {
            $result[] = $this->generateIndexCreateQuery($index);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function generateAlterQueries()
    {
        $result = [];

        $primaryKey = $this->getPrimaryKey();

        foreach ($this->getNewColumns() as $column) {
            $result[] = 'ALTER TABLE ' . $this->dbDriver->sanitizeTableName(
                    $this->tableName
                ) . ' ADD COLUMN ' . $this->generateColumnQuery($column);
        }
        foreach ($this->getNewIndexes() as $index) {
            $result[] = $this->generateIndexCreateQuery($index);
        }

        if (count($this->getDeletedConstraints())) {
            throw new RuntimeException($this->translate(
                'Sqlite driver does not support foreign keys deleting.'
            ));
        }
        if (count($this->getDeletedIndexes())) {
            throw new RuntimeException($this->translate(
                'Sqlite driver does not support keys deleting.'
            ));
        }
        if (count($this->getModifiedIndexes())) {
            throw new RuntimeException($this->translate(
                'Sqlite driver does not support keys modification.'
            ));
        }
        if (count($this->getModifiedConstraints())) {
            throw new RuntimeException($this->translate(
                'Sqlite driver does not support foreign keys modification.'
            ));
        }
        if ($primaryKey && $primaryKey->getIsDeleted()) {
            throw new RuntimeException($this->translate(
                'Sqlite driver does not support primary key deleting.'
            ));
        }
        if (count($this->getDeletedColumns())) {
            throw new RuntimeException($this->translate(
                'Sqlite driver does not support columns deleting.'
            ));
        }
        if (count($this->getModifiedColumns())) {
            throw new RuntimeException($this->translate(
                'Sqlite driver does not support columns modification.'
            ));
        }
        if ($primaryKey && $primaryKey->getIsNew()) {
            throw new RuntimeException($this->translate(
                'Sqlite driver does not support primary key adding.'
            ));
        }
        if (count($this->getNewConstraints())) {
            throw new RuntimeException($this->translate(
                'Sqlite driver does not support foreign keys adding.'
            ));
        }

        return $result;
    }

    /**
     * Генерирует запрос на удаление таблицы.
     * @return array тексты запросов
     */
    protected function generateDropQueries()
    {
        $result = [];
        $result[] = 'DROP TABLE IF EXISTS ' . $this->dbDriver->sanitizeTableName($this->tableName);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function loadIndexes()
    {
        $sql = 'PRAGMA index_list(' . $this->dbDriver->sanitizeTableName($this->tableName) . ')';
        $queryResult = $this->dbDriver->select($sql)
            ->fetchAll(\PDO::FETCH_ASSOC);
        $this->indexes = [];
        foreach ($queryResult as $indexInfo) {
            $indexName = $indexInfo['name'];
            $index = $this->createIndexSchemeInstance($indexName);
            $index->setIsUnique($indexInfo['unique']);

            $sql = 'PRAGMA index_info(' . $indexName . ')';
            $columns = $this->dbDriver->select($sql)
                ->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($columns as $column) {
                $index->addColumn($column['name']);
            }
            $index->setIsModified(false);
            $this->indexes[$indexName] = $index;
        }

        return $this->indexes;
    }

    /**
     * {@inheritdoc}
     */
    protected function loadConstraints()
    {
        $sql = 'PRAGMA foreign_key_list(' . $this->dbDriver->sanitizeTableName($this->tableName) . ')';
        $queryResult = $this->dbDriver->select($sql)
            ->fetchAll(\PDO::FETCH_ASSOC);
        $this->constraints = [];
        foreach ($queryResult as $foreignKey) {
            $constraint = $this->createConstraintSchemeInstance('fk' . $foreignKey['id'])
                ->setColumnName($foreignKey['from'])
                ->setReferenceTableName($foreignKey['table'])
                ->setReferenceColumnName($foreignKey['to'])
                ->setOnDeleteAction($foreignKey['on_delete'])
                ->setOnUpdateAction($foreignKey['on_update'])
                ->setIsModified(false);

            $this->constraints['fk' . $foreignKey['id']] = $constraint;
        }

        return $this->constraints;
    }

    /**
     * Загружает информацию о первичном ключе таблицы.
     * Должен быть реализован в конкретном драйвере
     * @return IIndexScheme
     */
    protected function loadPrimaryKey()
    {
        foreach ($this->getColumns() as $column) {
            if ($column->getIsPk()) {
                $this->primaryKey = $this->createIndexSchemeInstance('PRIMARY');
                $this->primaryKey->setIsUnique(true);
                $this->primaryKey->addColumn($column->getName());
                break;
            }
        }

        return $this->primaryKey;
    }

    /**
     * Геренирует часть sql-запроса для столбца
     * @param IColumnScheme $column
     * @return string
     */
    private function generateColumnQuery($column)
    {
        $query = $this->dbDriver->sanitizeColumnName($column->getName()) . ' ' . $column->getInternalType();

        if ($column->getIsPk()) {
            $query .= ' PRIMARY KEY';
        }
        if ($column->getIsAutoIncrement()) {
            $query .= ' AUTOINCREMENT';
        }
        if (!$column->getIsNullable()) {
            $query .= ' NOT NULL';
        }
        if (!is_null($column->getDefaultValue())) {
            $query .= ' DEFAULT \'' . $column->getDefaultValue() . '\'';
        }
        if (null != ($collation = $column->getCollation())) {
            $query .= ' COLLATE ' . $collation;
        }

        return $query;
    }

    /**
     * Генерирует sql-запрос для создания индекса
     * @param IIndexScheme $index
     * @return string
     */
    private function generateIndexCreateQuery($index)
    {
        $query = 'CREATE';

        if ($index->getIsUnique()) {
            $query .= ' UNIQUE';
        }

        $query .= ' INDEX ' . $this->dbDriver->sanitizeTableName($index->getName());
        $query .= ' ON ' . $this->dbDriver->sanitizeTableName($this->tableName);

        $columns = $index->getColumns();
        $indexColumns = [];
        foreach ($columns as $columnInfo) {
            $indexColumns[] = $this->dbDriver->sanitizeColumnName($columnInfo['name']);;
        }
        $query .= ' ( ' . implode(', ', $indexColumns) . ' )';

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
