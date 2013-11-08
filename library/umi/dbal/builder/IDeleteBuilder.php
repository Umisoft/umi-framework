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
 * Построитель Delete-запросов.
 */
interface IDeleteBuilder extends IQueryBuilder
{
    /**
     * Определяет имя таблицы для удаления данных.
     * @param string $tableName имя таблицы
     * @return self
     */
    public function from($tableName);

    /**
     * Задает условия WHERE
     * @param string $mode режим складывания выражений
     * @return self
     */
    public function where($mode = IExpressionGroup::MODE_AND);

    /**
     * Установливает LIMIT на количество удаляемых строк
     * @param integer|string $limit может быть либо числом, либо плейсхолдером
     * @return self
     */
    public function limit($limit);

    /**
     * Возвращает имя таблицы для удаления данных
     * @internal
     * @throws RuntimeException если имя таблицы не определено
     * @return string
     */
    public function getTableName();

    /**
     * Возвращает группу выражений для WHERE
     * @internal
     * @return IExpressionGroup|null null, если нет WHERE-выражений
     */
    public function getWhereExpressionGroup();

    /**
     * Возвращает limit на количество затрагиваемых строк
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
     * Добавляет простое выражение в текущую группу выражений
     * @param string $leftCond
     * @param string $operator
     * @param string $rightCond
     * @throws RuntimeException если не удалось добавить выражение
     * @return self
     */
    public function expr($leftCond, $operator, $rightCond);

    /**
     * Устанавливает условие сортировки
     * @param string $column имя столбца, может быть плейсхолдером
     * @param string $direction направление сортировки, ASC по умолчанию
     * @return self
     */
    public function orderBy($column, $direction = IQueryBuilder::ORDER_ASC);

    /**
     * Возвращает список правил сортировки
     * @internal
     * @return array в формате array(array('columnName', 'ASC'), ...))
     */
    public function getOrderConditions();
}
