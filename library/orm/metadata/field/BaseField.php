<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\metadata\field;

use umi\dbal\builder\IInsertBuilder;
use umi\dbal\builder\IQueryBuilder;
use umi\dbal\builder\IUpdateBuilder;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;
use umi\orm\exception\UnexpectedValueException;
use umi\orm\object\IObject;
use umi\orm\object\property\IProperty;

/**
 * Поле типа данных.
 */
abstract class BaseField implements IField, ILocalizable
{

    use TLocalizable;

    /**
     * @var string $name имя поля
     */
    protected $name;
    /**
     * @var bool $isVisible флаг "видимое"
     */
    protected $isVisible = true;
    /**
     * @var bool $isReadOnly флаг "доступно только на чтение"
     */
    protected $isReadOnly = false;
    /**
     * @var mixed $defaultValue значение поля по умолчанию, которое сохраняется в БД
     */
    protected $defaultValue = null;
    /**
     * @var string $columnName имя столбца в таблице, связанного с полем
     */
    protected $columnName;
    /**
     * @var string $accessor имя getter'а для доступа к значению поля объекта
     */
    protected $accessor;
    /**
     * @var string $mutator имя setter'а для установки значения поля объекта
     */
    protected $mutator;
    /**
     * @var array $validators список валидаторов в формате [$validatorType => [$optionName => $value, ...], ...]
     */
    protected $validators = [];
    /**
     * @var array $filters список фильтров в формате [$filterType => [$optionName => $value, ...], ...]
     */
    protected $filters = [];

    /**
     * Конструктор.
     * @param string $name имя поля
     * @param array $config конфигурация
     * @throws UnexpectedValueException в случае некорректного конфига
     */
    public function __construct($name, array $config = [])
    {
        $this->name = $name;
        $this->applyCommonConfiguration($config);
        $this->applyConfiguration($config);
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
    public function getIsVisible()
    {
        return $this->isVisible;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsReadOnly()
    {
        return $this->isReadOnly;
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
    public function getAccessor()
    {
        return $this->accessor;
    }

    /**
     *{@inheritdoc}
     */
    public function getMutator()
    {
        return $this->mutator;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * {@inheritdoc}
     */
    public function getValidators()
    {
        return $this->validators;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * {@inheritdoc}
     */
    public function persistProperty(IObject $object, IProperty $property, IQueryBuilder $builder)
    {
        /**
         * @var IUpdateBuilder $builder
         */
        if ($builder instanceof IInsertBuilder || $builder instanceof IUpdateBuilder) {
            $builder->set($this->getColumnName());
            $builder->bindValue(':' . $this->getColumnName(), $property->getDbValue(), $this->getDataType());
        }

        return $this;
    }

    /**
     * Разбирает и применяет общую конфигурацию поля
     * @param array $config конфигурация поля
     * @throws UnexpectedValueException при ошибках в конфигурации
     */
    protected function applyCommonConfiguration($config)
    {
        $this->columnName = isset($config['columnName']) ? strval($config['columnName']) : $this->name;

        if (isset($config['visible'])) {
            $this->isVisible = (bool) $config['visible'];
        }
        if (isset($config['readOnly'])) {
            $this->isReadOnly = (bool) $config['readOnly'];
        }
        if (isset($config['defaultValue'])) {
            $this->defaultValue = $config['defaultValue'];
        }
        if (isset($config['accessor'])) {
            $this->accessor = strval($config['accessor']);
        }
        if (isset($config['mutator'])) {
            $this->mutator = strval($config['mutator']);
        }

        if (isset($config['validators'])) {
            $validators = $config['validators'];
            if (!is_array($validators)) {
                throw new UnexpectedValueException($this->translate(
                    'Field validators configuration should be an array.'
                ));
            }
            $this->validators = $validators;
        }

        if (isset($config['filters'])) {
            $filters = $config['filters'];
            if (!is_array($filters)) {
                throw new UnexpectedValueException($this->translate(
                    'Field filters configuration should be an array.'
                ));
            }
            $this->filters = $filters;
        }
    }

    /**
     * Разбирает и применяет конфигурацию для поля
     * @param array $config конфигурация поля
     */
    protected function applyConfiguration(array $config)
    {
    }
}
