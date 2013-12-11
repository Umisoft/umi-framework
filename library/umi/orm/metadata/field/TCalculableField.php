<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\metadata\field;

use umi\dbal\builder\IQueryBuilder;
use umi\dbal\builder\IUpdateBuilder;
use umi\orm\object\IObject;
use umi\orm\object\property\IProperty;

/**
 * Трейт для полей с вычисляемым значением.
 */
trait TCalculableField
{

    /**
     * Возвращает php-тип данных поля. Используется для PDO.<br />
     * http://ru2.php.net/manual/en/function.gettype.php
     * @return string
     */
    abstract public function getDataType();

    /**
     * Возвращает имя столбца таблицы для поля
     * @return string
     */
    abstract public function getColumnName();

    /**
     * Вычисляет и возвращает значение для записи в БД.
     * @param IObject $object объект, для которого вычисляется значение
     * @return string|int|float
     */
    abstract public function calculateDBValue(IObject $object);

    /**
     * {@inheritdoc}
     */
    public function persistProperty(IObject $object, IProperty $property, IQueryBuilder $builder)
    {

        if ($builder instanceof IUpdateBuilder) {
            $builder->set($this->getColumnName());
            $value = $this->calculateDBValue($object);
            $builder->bindValue(':' . $this->getColumnName(), $value, $this->getDataType());
            $property->setValue($value);
        }

        return $this;
    }
}
