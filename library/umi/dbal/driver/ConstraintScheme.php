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
 * Схема внешнего ключа таблицы БД.
 */
class ConstraintScheme implements IConstraintScheme
{
    /**
     * @var string $name имя индекса
     */
    protected $name;
    /**
     * @var bool $isNew является ли схема индекса новой
     */
    protected $isNew = false;
    /**
     * @var bool $isDeleted является ли схема индекса удаленной
     */
    protected $isDeleted = false;
    /**
     * @var bool $isModified модифицирована ли схема индекса
     */
    protected $isModified = false;
    /**
     * @var ITableScheme $table таблица, к которой принадлежит индекс
     */
    protected $table;
    /**
     * @var string $columnName имя столбца, на которого действует ограничение внешнего ключа
     */
    protected $columnName;
    /**
     * @var string $referenceTableName имя связанной таблицы, значения которой являются ограничениями внешнего ключа
     */
    protected $referenceTableName;
    /**
     * @var string $referenceColumnName имя столбца связанной таблицы, значения которого являются ограничениями внешнего ключа
     */
    protected $referenceColumnName;
    /**
     * @var string $onDeleteAction действие при удалении строк из связанной таблицы
     */
    protected $onDeleteAction;
    /**
     * @var string $onUpdateAction действие при обновлении строк из связанной таблицы
     */
    protected $onUpdateAction;

    /**
     * Конструктор
     * @param string $name имя внешнего ключа
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
    public function getColumnName()
    {
        return $this->columnName;
    }

    /**
     * {@inheritdoc}
     */
    public function setColumnName($columnName)
    {
        if ($this->getColumnName() != $columnName) {
            $this->columnName = $columnName;
            $this->setIsModified(true);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenceTableName()
    {
        return $this->referenceTableName;
    }

    /**
     * {@inheritdoc}
     */
    public function setReferenceTableName($referenceTableName)
    {
        if ($this->getReferenceTableName() != $referenceTableName) {
            $this->referenceTableName = $referenceTableName;
            $this->setIsModified(true);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenceColumnName()
    {
        return $this->referenceColumnName;
    }

    /**
     * {@inheritdoc}
     */
    public function setReferenceColumnName($referenceColumnName)
    {
        if ($this->getReferenceColumnName() != $referenceColumnName) {
            $this->referenceColumnName = $referenceColumnName;
            $this->setIsModified(true);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOnDeleteAction()
    {
        return $this->onDeleteAction;
    }

    /**
     * {@inheritdoc}
     */
    public function setOnDeleteAction($onDeleteAction = null)
    {
        if ($this->getOnDeleteAction() != $onDeleteAction) {
            $this->onDeleteAction = $onDeleteAction;
            $this->setIsModified(true);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOnUpdateAction()
    {
        return $this->onUpdateAction;
    }

    /**
     * {@inheritdoc}
     */
    public function setOnUpdateAction($onUpdateAction = null)
    {
        if ($this->getOnUpdateAction() != $onUpdateAction) {
            $this->onUpdateAction = $onUpdateAction;
            $this->setIsModified(true);
        }

        return $this;
    }
}
