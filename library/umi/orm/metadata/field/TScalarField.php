<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\metadata\field;

use umi\orm\object\IObject;

/**
 * Трейт для поддержки скалярных полей
 */
trait TScalarField
{
    /**
     * Возвращает имя поля
     * @return string
     */
    abstract public function getName();

    /**
     * Возвращает php-тип данных поля. Используется для PDO.<br />
     * http://ru2.php.net/manual/en/function.gettype.php
     * @return string
     */
    abstract public function getDataType();

    /**
     * Подготавливает и возвращает значение свойства по внутреннему значению из БД
     * @internal
     * @param IObject $object объект, для которого подготавливается свойство
     * @param mixed $internalDbValue внутреннее значение свойства в БД
     * @return mixed
     */
    public function preparePropertyValue(IObject $object, $internalDbValue)
    {
        if (is_null($internalDbValue)) {
            return null;
        }

        @settype($internalDbValue, $this->getDataType());

        return $internalDbValue;
    }

    /**
     * Подготавливает и возвращает значение для записи в БД
     * @internal
     * @param IObject $object объект, для которого будет установлено свойство
     * @param mixed $propertyValue значение свойства
     * @return mixed
     */
    public function prepareDbValue(IObject $object, $propertyValue)//todo! unused object?
    {
        return $propertyValue;
    }

}