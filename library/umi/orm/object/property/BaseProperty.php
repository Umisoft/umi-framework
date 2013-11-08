<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\object\property;

use umi\filter\IFilterAware;
use umi\filter\TFilterAware;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\orm\exception\InvalidArgumentException;
use umi\orm\metadata\field\IField;
use umi\orm\object\IObject;

/**
 * Базовый класс свойства
 */
abstract class BaseProperty implements IProperty, ILocalizable, IFilterAware
{

    use TLocalizable;
    use TFilterAware;

    /**
     * @var IObject $object владелец свойства
     */
    protected $object;
    /**
     * @var IField $field поле типа данных
     */
    protected $field;
    /**
     * @var bool $isLoaded загружено ли значение свойства
     */
    protected $isLoaded = false;
    /**
     * @var bool $isModified статус модифицированности значения свойства
     */
    protected $isModified = false;
    /**
     * @var mixed $dbValue значение свойства в БД
     */
    protected $dbValue;
    /**
     * @var mixed $previousDbValue прежнее значение свойства в БД
     */
    protected $previousDbValue;
    /**
     * @var mixed $value значение свойства
     */
    protected $value;
    /**
     * @var mixed $oldValue прежнее значение свойства
     */
    protected $previousValue;
    /**
     * @var bool $isValuePrepared флаг, указывающий на то что значение свойство было подготовлено
     */
    protected $isValuePrepared = false;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->field->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getFullName()
    {
        return $this->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsLoaded()
    {
        return $this->isLoaded;
    }

    /**
     * {@inheritdoc}
     */
    public function setInitialValue($dbValue)
    {
        $this->dbValue = $this->previousDbValue = $dbValue;
        $this->value = $this->previousValue = null;
        $this->isLoaded = true;
        $this->isModified = false;
        $this->isValuePrepared = false;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDbValue()
    {
        if (!$this->getIsLoaded()) {
            $this->object->fullyLoad();
        }

        return $this->dbValue;
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviousDbValue()
    {
        return $this->previousDbValue;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultValue()
    {
        if (!$this->getIsReadOnly()) {
            $propertyValue = $this->field->preparePropertyValue($this->object, $this->field->getDefaultValue());
            $this->setValue($propertyValue);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        $value = $this->applyFieldFilters($value);

        if (!is_null($value)) {
            $isValid = false;
            $validationException = null;

            try {
                $isValid = $this->field->validateInputPropertyValue($value);
            } catch (InvalidArgumentException $validationException) {
            }

            if (!$isValid) {
                throw new InvalidArgumentException($this->translate(
                    'Cannot set value for property "{name}". Wrong value type.',
                    ['name' => $this->getName()]
                ), 0, $validationException);
            }
        }

        if ($this->getValue() !== $value) {
            $this->dbValue = $this->field->prepareDbValue($this->object, $value);
            $this->value = $value;
            $this->isModified = true;
            $this->object->setIsModified();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        if (!$this->isValuePrepared) {
            $this->value = $this->previousValue = $this->field->preparePropertyValue(
                $this->object,
                $this->getDbValue()
            );
            $this->isValuePrepared = true;
        }

        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviousValue()
    {
        if (!$this->isValuePrepared) {
            $this->getValue();
        }

        return $this->previousValue;
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessor()
    {
        return $this->field->getAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function getMutator()
    {
        return $this->field->getMutator();
    }

    /**
     * {@inheritdoc}
     */
    public function getIsReadOnly()
    {
        return $this->field->getIsReadOnly();
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
    public function setIsConsistent()
    {
        $this->isModified = false;
        $this->previousDbValue = $this->dbValue;
        $this->previousValue = $this->value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function rollback()
    {
        if ($this->getIsModified()) {
            $this->dbValue = $this->previousDbValue;
            $this->value = $this->previousValue;
            $this->isModified = false;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsValuePrepared()
    {
        return $this->isValuePrepared;
    }

    /**
     * Применяет фильтры поля к значению свойства
     * @param mixed $propertyValue
     * @return mixed
     */
    protected function applyFieldFilters($propertyValue)
    {
        $filterConfig = $this->field->getFilters();
        if (count($filterConfig)) {
            $filterCollection = $this->createFilterCollection($filterConfig);
            $propertyValue = $filterCollection->filter($propertyValue);
        }

        return $propertyValue;
    }

}
