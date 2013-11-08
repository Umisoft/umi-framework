<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\driver;

/**
 * Схема индекса таблицы БД.
 */
class IndexScheme implements IIndexScheme
{
    /**
     * @var string $name имя индекса
     */
    protected $name;
    /**
     * @var bool $isUnique уникальность индекса
     */
    protected $isUnique = false;
    /**
     * @var bool $isNew является ли схема индекса новой
     */
    protected $isNew = false;
    /**
     * @var bool $isDeleted является ли схема индекса удаленной
     */
    protected $isDeleted = false;
    /**
     * @var array $columns массив полей индекса вида array($columnName => array('name' => $columnName, 'length' => $length), ...)
     */
    protected $columns = [];
    /**
     * @var bool $isModified модифицирована ли схема индекса
     */
    protected $isModified = false;
    /**
     * @var string $type тип индекса
     */
    protected $type;
    /**
     * @var ITableScheme $table таблица, к которой принадлежит индекс
     */
    protected $table;

    /**
     * Конструктор.
     * @param string $name имя индекса
     * @param ITableScheme $table таблица
     */
    public function __construct($name, ITableScheme $table)
    {
        $this->name = $name;
        $this->table = $table;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsDeleted($isDeleted = true)
    {
        $this->isDeleted = (bool) $isDeleted;
        if ($this->isDeleted) {
            $this->table->setIsModified(true);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsNew()
    {
        return $this->isNew;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsNew($isNew = true)
    {
        $this->isNew = (bool) $isNew;
        if ($this->isNew) {
            $this->table->setIsModified(true);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsUnique()
    {
        return $this->isUnique;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsUnique($isUnique = true)
    {
        if ($this->getIsUnique() != (bool) $isUnique) {
            $this->isUnique = (bool) $isUnique;
            $this->setIsModified(true);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsModified()
    {
        return $this->isModified;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsModified($isModified = true)
    {
        $this->isModified = (bool) $isModified;
        if ($this->isModified) {
            $this->table->setIsModified($this->isModified);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addColumn($name, $length = null)
    {

        $length = is_numeric($length) ? (int) $length : null;

        if (!isset($this->columns[$name])) {
            $this->columns[$name] = array(
                'name'   => $name,
                'length' => $length
            );
            $this->setIsModified(true);
        } elseif ($this->columns[$name]['length'] != $length) {
            $this->columns[$name]['length'] = $length;
            $this->setIsModified(true);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteColumn($name)
    {
        if (isset($this->columns[$name])) {
            unset($this->columns[$name]);
            $this->setIsModified(true);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        if (strtoupper($this->getType()) != strtoupper($type)) {
            $this->type = $type;
            $this->setIsModified(true);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }
}
