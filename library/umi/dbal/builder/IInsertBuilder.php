<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\builder;

use umi\dbal\exception\RuntimeException;

/**
 * Построитель Insert-запросов.
 */
interface IInsertBuilder extends IQueryBuilder
{
    /**
     * Определяет имя таблицы для вставки
     * @param string $tableName имя таблицы
     * @param bool $isIgnore игнорировать конфликты duplicate-key
     * @return self
     */
    public function insert($tableName, $isIgnore = false);

    /**
     * Устанавливает SET условия при duplicate-key конфликте.
     * Все последующие InsertBuilder::set() будут относиться к
     * ON DUPLICATE KEY секции запроса
     * @param string $columnName имя столбца, значение которого уникально
     * @param string $_ [optional] можно передать несколько столбцов, значения которых должны быть уникальны
     * @return self
     */
    public function onDuplicateKey($columnName, $_ = null);

    /**
     * Устанавливает значение столбца
     * @param string $columnName имя столбца
     * @param string|null $placeholder плейсхолдер,
     * если не указан будет соответствовать имени столбца :$columnName
     * @return self
     */
    public function set($columnName, $placeholder = null);

    /**
     * Устанавливает имена нескольких столбцов в качестве плейсхолдеров
     * @param string $columnName имя столбца
     * @param string $_ [optional] можно передать несколько столбцов
     * @return self
     */
    public function setPlaceholders($columnName, $_ = null);

    /**
     * Возвращает имя таблицы для обновления данных
     * @internal
     * @throws RuntimeException если имя таблицы не определено
     * @return string
     */
    public function getTableName();

    /**
     * Возвращает опцию IGNORE (игнорировать конфликты duplicate-key)
     * @internal
     * @return bool
     */
    public function getIsIgnore();

    /**
     * Возвращает список SET-выражений
     * @internal
     * @throws RuntimeException если не указано ни одного set-выражения
     * @return array вида array('columnName' => ':placeholder', ...)
     */
    public function getValues();

    /**
     * Возвращает список SET-выражений для ON DUPLICATE KEY секции
     * @internal
     * @return array|null вида array('columnName' => ':placeholder', ...)
     */
    public function getOnDuplicateKeyValues();

    /**
     * Возвращает список колонок, значения которых должны быть уникальными
     * @internal
     * @return array
     */
    public function getOnDuplicateKeyColumns();

}
