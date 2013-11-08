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
 * Интерфейс схемы индекса таблицы БД.
 */
interface IIndexScheme
{
    /**
     * Устанавливает/снимает флаг "схема удалена"
     * @internal
     * @param bool $isDeleted
     * @return self
     */
    public function setIsDeleted($isDeleted = true);

    /**
     * Проверяет, является ли схема удаленной
     * @return bool
     */
    public function getIsDeleted();

    /**
     * Проверяет, является ли схема новой
     * @return bool
     */
    public function getIsNew();

    /**
     * Устанавливает/снимает флаг "новая схема"
     * @internal
     * @param bool $isNew
     * @return self
     */
    public function setIsNew($isNew = true);

    /**
     * Проверяет, была ли изменена схема индекса
     * @return bool
     */
    public function getIsModified();

    /**
     * Устанавливает/снимает флаг "схема изменена"
     * @internal
     * @param bool $isModified
     * @return self
     */
    public function setIsModified($isModified = true);

    /**
     * Возвращает имя индекса
     * @return string
     */
    public function getName();

    /**
     * Возвращает уникальность индекса
     * @return bool
     */
    public function getIsUnique();

    /**
     * Выставляет уникальность индекса
     * @param bool $isUnique
     * @return self
     */
    public function setIsUnique($isUnique = true);

    /**
     * Выставляет тип индекса или сбрасывает его
     * @param string|null $type
     * @return self
     */
    public function setType($type);

    /**
     * Возвращает тип индекса
     * @return string
     */
    public function getType();

    /**
     * Добавляет к индексу столбец или изменяет существующий
     * @param string $name столбец
     * @param null|int $length длина
     * @return self
     */
    public function addColumn($name, $length = null);

    /**
     * Удаляет столбец из индекса
     * @param string $name столбец
     * @return self
     */
    public function deleteColumn($name);

    /**
     * Возвращает столбцы индекса
     * @return array
     */
    public function getColumns();
}
