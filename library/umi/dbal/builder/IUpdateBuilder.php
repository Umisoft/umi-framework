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
 * Построитель Update-запросов.
 */
interface IUpdateBuilder extends IQueryBuilder
{
    /**
     * Определяет имя таблицы для обновления.
     * @param string $tableName имя таблицы
     * @param bool $isIgnore игнорировать конфликты duplicate-key
     * @return self
     */
    public function update($tableName, $isIgnore = false);

    /**
     * Устанавливает значение столбца.
     * @param string $columnName имя столбца
     * @param string|null $placeholder плейсхолдер,
     * если не указан будет соответствовать имени столбца :$columnName
     * @return self
     */
    public function set($columnName, $placeholder = null);

    /**
     * Устанавливает имена нескольких столбцов в качестве плейсхолдеров.
     * @param string $columnName имя столбца
     * @param string $_ [optional] можно передать несколько столбцов
     * @return self
     */
    public function setPlaceholders($columnName, $_ = null);

    /**
     * Задаёт условия WHERE.
     * @param string $mode режим складывания выражений
     * @return self
     */
    public function where($mode = IExpressionGroup::MODE_AND);

    /**
     * Устанавливает LIMIT на количество затрагиваемых строк.
     * @param integer|string $limit может быть либо числом, либо плейсхолдером
     * @return self
     */
    public function limit($limit);

    /**
     * Возвращает имя таблицы для обновления данных.
     * @internal
     * @return string
     * @throws RuntimeException если имя таблицы не определено
     */
    public function getTableName();

    /**
     * Возвращает опцию IGNORE (игнорировать конфликты duplicate-key).
     * @internal
     * @return bool
     */
    public function getIsIgnore();

    /**
     * Возвращает список SET-выражений.
     * @internal
     * @throws RuntimeException если не указано ни одного set-выражения
     * @return array вида array('columnName' => ':placeholder', ...)
     */
    public function getValues();

    /**
     * Возвращает группу выражений для WHERE.
     * @internal
     * @return IExpressionGroup|null null, если нет WHERE-выражений
     */
    public function getWhereExpressionGroup();

    /**
     * Возвращает limit на количество затрагиваемых строк.
     * @internal
     * @return integer
     */
    public function getLimit();

    /**
     * Начинает новую группу выражений.
     * Группа становится текущей до вызова end
     * @param string $mode режим сложения составных выражений
     * @return self
     */
    public function begin($mode = IExpressionGroup::MODE_AND);

    /**
     * Завершает текущую группу выражений.
     * Текущей становится родительская группа.
     * @return self
     */
    public function end();

    /**
     * Добавляет простое выражение в текущую группу выражений.
     * @param string $leftCond
     * @param string $operator
     * @param string $rightCond
     * @throws RuntimeException если не удалось добавить выражение
     * @return self
     */
    public function expr($leftCond, $operator, $rightCond);

    /**
     * Устанавливает условие сортировки.
     * @param string $column имя столбца, может быть плейсхолдером
     * @param string $direction направление сортировки, ASC по умолчанию
     * @return self
     */
    public function orderBy($column, $direction = IQueryBuilder::ORDER_ASC);

    /**
     * Возвращает список правил сортировки.
     * @internal
     * @return array в формате array(array('columnName', 'ASC'), ...))
     */
    public function getOrderConditions();

    /**
     * Проверяет, можно ли выполнить запрос.
     * @return bool
     */
    public function getUpdatePossible();
}
