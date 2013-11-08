<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\object\property;

use umi\orm\exception\IException;
use umi\orm\metadata\field\IField;

/**
 * Интерфейс свойства объекта данных.
 */
interface IProperty
{
    /**
     * Возвращает имя свойства
     * @return string
     */
    public function getName();

    /**
     * Возвращает имя свойства с суффиксом локали
     * @return string
     */
    public function getFullName();

    /**
     * Возвращает поле типа данных, которое соответсвует свойству
     * @return IField
     */
    public function getField();

    /**
     * Проверяет, загружено ли значение свойства
     * @return bool
     */
    public function getIsLoaded();

    /**
     * Инициализирует свойство, устанавливает внутреннее значение свойства и
     * помечает свойство как isLoaded
     * @internal
     * @param string $dbValue
     * @return self
     */
    public function setInitialValue($dbValue);

    /**
     * Возвращает внутреннее  значение свойства из БД. <br />
     * Поднимает событие IProperty.onLazyLoad, если свойство еще не было загружено.
     * @internal
     * @return string
     */
    public function getDbValue();

    /**
     * Возвращает прежнее внутреннее значение свойства из БД. Для rollback().
     * @internal
     * @return string
     */
    public function getPreviousDbValue();

    /**
     * Устанавливает значение свойства "по умолчанию"
     * @return self
     */
    public function setDefaultValue();

    /**
     * Устанавливает новое значение свойства
     * @param mixed $value значение свойства
     * @throws IException если не удалось установить значение свойства
     * @return self
     */
    public function setValue($value);

    /**
     * Возвращает значение свойства
     * @return mixed
     */
    public function getValue();

    /**
     * Возвращает прежнее значение свойства
     * @return mixed
     */
    public function getPreviousValue();

    /**
     * Возвращает имя getter'а для доступа к значению поля
     * @return string
     */
    public function getAccessor();

    /**
     * Возвращает имя setter'а для установки значения поля
     * @return string
     */
    public function getMutator();

    /**
     * Проверяет, доступно ли свойство на запись
     * @return boolean
     */
    public function getIsReadOnly();

    /**
     * Откатывает состояние свойства. Устанавливает свойству старое значение,
     * помечает свойство как не модифицированное.
     * @return self
     */
    public function rollback();

    /**
     * Проверяет, модифицировано ли значение свойства
     * @internal
     * @return bool
     */
    public function getIsModified();

    /**
     * Помечает свойство, как консистентное с БД
     * @internal
     * @return self
     */
    public function setIsConsistent();

    /**
     * Возвращает признак подготовленности значения
     * @internal
     * @return bool
     */
    public function getIsValuePrepared();

}
