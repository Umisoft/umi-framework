<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\driver;

use umi\dbal\exception\NonexistentEntityException;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Класс схемы столбца таблицы БД.
 */
class ColumnScheme implements IColumnScheme, ILocalizable
{

    use TLocalizable;

    /**
     * @var IDbDriver $dbDriver драйвер БД
     */
    protected $dbDriver;
    /**
     * @var ITableScheme $table таблица, к которой принадлежит колонка
     */
    protected $table;
    /**
     * @var string $name имя столбца
     */
    protected $name;
    /**
     * @var string $internalType внутренний тип данных столбца в синтаксисе конкретного драйвера БД
     */
    protected $internalType;
    /**
     * @var array $options опции столбца
     */
    protected $options = [
        self::OPTION_LENGTH        => null,
        self::OPTION_SIZE          => null,
        self::OPTION_DECIMALS      => null,
        self::OPTION_UNSIGNED      => false,
        self::OPTION_ZEROFILL      => false,
        self::OPTION_NULLABLE      => true,
        self::OPTION_PRIMARY_KEY   => false,
        self::OPTION_AUTOINCREMENT => false,
        self::OPTION_DEFAULT_VALUE => null,
        self::OPTION_COMMENT       => null,
        self::OPTION_COLLATION     => null
    ];
    /**
     * @var bool $isModified модифицирована ли схема столбца
     */
    private $isModified = false;
    /**
     * @var bool $isNew является ли схема столбца новой
     */
    private $isNew = false;
    /**
     * @var bool $isDeleted является ли схема столбца удаленной
     */
    private $isDeleted = false;

    /**
     * Конструктор
     * @param string $name имя столбца
     * @param string $internalType внутренний тип столбца
     * @param array $options список параметров столбца
     * @param IDbDriver $driver драйвер БД
     * @param ITableScheme $table таблица
     */
    public function __construct($name, $internalType, $options, IDbDriver $driver, ITableScheme $table)
    {
        $this->name = $name;
        $this->dbDriver = $driver;
        $this->table = $table;
        $this->internalType = $internalType;

        if (isset($options[self::OPTION_TYPE])) {
            unset($options[self::OPTION_TYPE]);
        }
        foreach ($options as $optionName => $optionValue) {
            $this->setOption($optionName, $optionValue);
        }
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
    public function setType($type, array $options = [])
    {

        $internalType = $this->dbDriver->getColumnInternalType($type);
        if ($this->internalType != $internalType) {
            $this->internalType = $internalType;
            $this->setIsModified(true);
        }

        $options = array_merge($this->dbDriver->getColumnTypeOptions($type), $options);
        unset($options[self::OPTION_TYPE]);
        $this->options = [
            self::OPTION_LENGTH        => null,
            self::OPTION_SIZE          => null,
            self::OPTION_DECIMALS      => null,
            self::OPTION_UNSIGNED      => false,
            self::OPTION_ZEROFILL      => false,
            self::OPTION_NULLABLE      => true,
            self::OPTION_PRIMARY_KEY   => false,
            self::OPTION_AUTOINCREMENT => false,
            self::OPTION_DEFAULT_VALUE => null,
            self::OPTION_COMMENT       => null,
            self::OPTION_COLLATION     => null
        ];

        foreach ($options as $optionName => $optionValue) {
            $this->setOption($optionName, $optionValue);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setOption($name, $value)
    {

        $oldValue = null;
        switch ($name) {
            case self::OPTION_SIZE:
            {
                $oldValue = $this->internalType;
                $value = $this->dbDriver->getColumnInternalTypeBySize($this->internalType, $value);
                $this->internalType = $value;
            }
                break;
            case self::OPTION_LENGTH:
            case self::OPTION_DECIMALS:
            {
                $oldValue = $this->options[$name];
                $value = (int) $value;
                $this->options[$name] = $value;
            }
                break;
            case self::OPTION_PRIMARY_KEY:
            case self::OPTION_AUTOINCREMENT:
            case self::OPTION_ZEROFILL:
            case self::OPTION_UNSIGNED:
            case self::OPTION_NULLABLE:
            {
                $oldValue = $this->options[$name];
                $value = (bool) $value;
                $this->options[$name] = $value;
            }
                break;
            case self::OPTION_DEFAULT_VALUE:
            case self::OPTION_COMMENT:
            case self::OPTION_COLLATION:
            {
                $oldValue = $this->options[$name];
                $this->options[$name] = $value;
            }
                break;

            default:
                {
                throw new NonexistentEntityException($this->translate(
                    'Option {option} for column does not exist.',
                    ['option' => $name]
                ));
                }
        }

        if ($oldValue != $value && $name != IColumnScheme::OPTION_PRIMARY_KEY) {
            $this->setIsModified(true);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getInternalType()
    {
        return $this->internalType;
    }

    /**
     * {@inheritdoc}
     */
    public function getLength()
    {
        return (isset($this->options[self::OPTION_LENGTH])) ? $this->options[self::OPTION_LENGTH] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getDecimals()
    {
        return (isset($this->options[self::OPTION_DECIMALS])) ? $this->options[self::OPTION_DECIMALS] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsUnsigned()
    {
        return (isset($this->options[self::OPTION_UNSIGNED])) ? $this->options[self::OPTION_UNSIGNED] : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsZerofill()
    {
        return (isset($this->options[self::OPTION_ZEROFILL])) ? $this->options[self::OPTION_ZEROFILL] : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsNullable()
    {
        return (isset($this->options[self::OPTION_NULLABLE])) ? $this->options[self::OPTION_NULLABLE] : true;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollation()
    {
        return (isset($this->options[self::OPTION_COLLATION])) ? $this->options[self::OPTION_COLLATION] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultValue()
    {
        return (isset($this->options[self::OPTION_DEFAULT_VALUE])) ? $this->options[self::OPTION_DEFAULT_VALUE] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getComment()
    {
        return (isset($this->options[self::OPTION_COMMENT])) ? $this->options[self::OPTION_COMMENT] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsPk()
    {
        return (isset($this->options[self::OPTION_PRIMARY_KEY])) ? $this->options[self::OPTION_PRIMARY_KEY] : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsAutoIncrement()
    {
        return (isset($this->options[self::OPTION_AUTOINCREMENT])) ? $this->options[self::OPTION_AUTOINCREMENT] : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsModified()
    {
        return $this->isModified;
    }

    /**
     * Установить/снять флаг "схема изменена"
     * @param bool $isModified
     * @return $this
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
    public function getIsDeleted()
    {
        return $this->isDeleted;
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

}
