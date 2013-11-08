<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\builder;

use umi\dbal\driver\IDbDriver;
use umi\dbal\exception\RuntimeException;

/**
 * Построитель Insert-запросов.
 */
class InsertBuilder extends BaseQueryBuilder implements IInsertBuilder
{
    /**
     * @var string $tableName имя таблицы для вставки
     */
    protected $tableName;
    /**
     * @var bool $isIgnore игнорировать duplicate-key конфликты
     */
    protected $isIgnore = false;
    /**
     * @var array $values список устанавливаемых значений стобцов
     */
    protected $values = [];
    /**
     * @var array $onDuplicateKeyValues список устанавливаемых значений стобцов для ON DUPLICATE KEY секции
     */
    protected $onDuplicateKeyValues;
    /**
     * @var array $onDuplicateKeyColumns список столбцов, значения которых должны быть уникальными
     */
    protected $onDuplicateKeyColumns = [];
    /**
     * @var bool $onDuplicateKeyMode режим генерации ON DUPLICATE KEY секции.
     */
    protected $onDuplicateKeyMode = false;

    /**
     * {@inheritdoc}
     */
    public function insert($tableName, $isIgnore = false)
    {
        $this->tableName = $tableName;
        $this->isIgnore = $isIgnore;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function onDuplicateKey($columnName, $_ = null)
    {
        $this->onDuplicateKeyMode = true;
        if (!is_null($_)) {
            $this->onDuplicateKeyColumns = func_get_args();
        } else {
            $this->onDuplicateKeyColumns = array($columnName);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function set($columnName, $placeholder = null)
    {
        if (is_null($placeholder)) {
            $placeholder = ':' . $columnName;
        }
        if ($this->onDuplicateKeyMode) {
            $this->onDuplicateKeyValues[$columnName] = $placeholder;
        } else {
            $this->values[$columnName] = $placeholder;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPlaceholders($columnName, $_ = null)
    {
        foreach (func_get_args() as $column) {
            $this->set($column);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTableName()
    {
        if (empty($this->tableName)) {
            throw new RuntimeException($this->translate(
                'Cannot insert into table. Table name required.'
            ));
        }

        return $this->tableName;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsIgnore()
    {
        return $this->isIgnore;
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        if (empty($this->values)) {
            throw new RuntimeException($this->translate(
                'Cannot insert into table "{table}". Value for at least one column required.',
                array('table' => $this->tableName)
            ));
        }

        return $this->values;
    }

    /**
     * {@inheritdoc}
     */
    public function getOnDuplicateKeyValues()
    {
        return $this->onDuplicateKeyValues;
    }

    /**
     * {@inheritdoc}
     */
    public function getOnDuplicateKeyColumns()
    {
        return $this->onDuplicateKeyColumns;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $sql = $this->getSql();

        $parts = explode(";\n", $sql);

        if (count($parts) != 2) {
            return parent::execute();
        }

        $result = null;
        foreach ($parts as $part) {
            $this->preparedStatement = $this->dbDriver->prepareStatement($part, $this);
            $result = parent::execute();
            if ($result->count() > 0) {
                break;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function build(IDbDriver $driver)
    {
        return $driver->buildInsertQuery($this);
    }

}
