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
 * Интерфейс схемы внешнего ключа таблицы БД.
 */
interface IConstraintScheme
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
     * Возвращает имя внешнего ключа
     * @return string
     */
    public function getName();

    /**
     * Возвращает имя столбца, на которого действует ограничение внешнего ключа
     * @return string
     */
    public function getColumnName();

    /**
     * Выставляет имя столбца, на которого действует ограничение внешнего ключа
     * @param string $columnName
     * @return self
     */
    public function setColumnName($columnName);

    /**
     * Возвращает имя связанной таблицы
     * @return string
     */
    public function getReferenceTableName();

    /**
     * Выставляет имя связанной таблицы
     * @param string $referenceTableName
     * @return self
     */
    public function setReferenceTableName($referenceTableName);

    /**
     * Возвращает имя связанного столбца
     * @return string
     */
    public function getReferenceColumnName();

    /**
     * Выставляет имя связанного столбца
     * @param string $referenceColumnName
     * @return self
     */
    public function setReferenceColumnName($referenceColumnName);

    /**
     * Возвращает действие, происходящие при удалении значения в связанной таблице
     * @return string
     */
    public function getOnDeleteAction();

    /**
     * Выставляет действие, происходящие при удалении значения в связанной таблице
     * @param string $onDeleteAction
     * @return self
     */
    public function setOnDeleteAction($onDeleteAction = null);

    /**
     * Возвращает действие, происходящие при изменении значения в связанной таблице
     * @return string
     */
    public function getOnUpdateAction();

    /**
     * Выставляет действие, происходящие при изменении значения в связанной таблице
     * @param string $onUpdateAction
     * @return self
     */
    public function setOnUpdateAction($onUpdateAction = null);

}
