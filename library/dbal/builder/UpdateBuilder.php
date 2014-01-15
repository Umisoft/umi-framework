<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\builder;

use umi\dbal\exception\RuntimeException;

/**
 * Построитель Update-запросов.
 */
class UpdateBuilder extends BaseQueryBuilder implements IUpdateBuilder
{
    /**
     * @var string $tableName имя таблицы для обновления
     */
    protected $tableName;
    /**
     * @var bool $isIgnore игнорировать duplicate-key конфликты
     */
    protected $isIgnore = false;
    /**
     * @var array $values список устанавливаемых значений столбцов
     */
    protected $values = [];
    /**
     * @var IExpressionGroup $whereExpressionGroup группа условий WHERE
     */
    protected $whereExpressionGroup;
    /**
     * @var int $limit Ограничение на количество затрагиваемых строк
     */
    protected $limit;

    /**
     * {@inheritdoc}
     */
    public function update($tableName, $isIgnore = false)
    {
        $this->tableName = $tableName;
        $this->isIgnore = $isIgnore;

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
        $this->values[$columnName] = $placeholder;

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
    public function limit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTableName()
    {
        if (empty($this->tableName)) {
            throw new RuntimeException($this->translate(
                'Cannot update table. Table name required.'
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
        if (!$this->getUpdatePossible()) {
            throw new RuntimeException($this->translate(
                'Cannot update table "{table}". Value for at least one column required.',
                ['table' => $this->tableName]
            ));
        }

        return $this->values;
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
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatePossible()
    {
        return !empty($this->values);
    }

    /**
     * {@inheritdoc}
     */
    protected function build()
    {
        return $this->dialect->buildUpdateQuery($this);
    }
}
