<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\builder;

use umi\dbal\exception\IException;
use umi\dbal\exception\RuntimeException;

/**
 * Интерфейс построителя Select-запросов.
 */
interface ISelectBuilder extends IQueryBuilder
{
    /**
     * Определяет список столбцов для выборки.
     * Список столбцов передается в параметрах метода.
     * Если столбцы не переданы, будет сформирован запрос, содержащий все столбцы (SELECT *)<br />
     * Пример:
     * <code>
     * ...
     * $subquery; // select-подзапрос, объект Query
     * $query->select(
     *     'field1',
     *     array('field2', 'alias'),
     *     array($subQuery, 'subfield'),
     *     array('tbl.field3 as field3')
     * );
     * ...
     * </code>
     * @param string|array $column,... список столбцов
     * @return self
     */
    public function select();

    /**
     * Устанавливает массив столбцов для выборки.
     * @param array $columns массив столбцов для выборки
     * @return self
     */
    public function setColumns(array $columns);

    /**
     * Запрещает серверу БД использовать кэш запросов (SQL_NO_CACHE).
     * Используется для тестов производительности, не рекомендуется выключать кэш.
     * @internal
     * @return self
     */
    public function disableCache();

    /**
     * Проверяет, выключен ли кэш для запросов
     * @internal
     * @return bool
     */
    public function getCacheDisabled();

    /**
     * Включает/выключает выборку только уникальных строк (SELECT DISTINCT)
     * @param boolean $enabled
     * @return self
     */
    public function distinct($enabled = true);

    /**
     * Определяет список таблиц для выборки.
     * Список таблиц передается в параметрах метода.<br />
     * Пример:
     * <code>
     * ...
     * $query->from('table1', array('table2', 'alias'));
     * ...
     * </code>
     * @throws RuntimeException если не передана ни одна таблица
     * @return self
     */
    public function from();

    /**
     * Задаёт условия WHERE.
     * @param string $mode режим складывания выражений
     * @return self
     */
    public function where($mode = IExpressionGroup::MODE_AND);

    /**
     * Задаёт условия HAVING.
     * @param string $mode режим складывания выражений
     * @return self
     */
    public function having($mode = IExpressionGroup::MODE_AND);

    /**
     * Создаёт JOIN таблицы.
     * @param array|string $table имя таблицы для джойна, может быть массивом вида array('name', 'alias');
     * @param string $type тип (LEFT, INNER, ...). По умолчанию INNER
     * @return self
     */
    public function join($table, $type = 'INNER');

    /**
     * Создаёт LEFT JOIN таблицы.
     * @param array|string $table имя таблицы для джойна, может быть массивом вида array('name', 'alias');
     * @return self
     */
    public function leftJoin($table);

    /**
     * Создаёт INNER JOIN таблицы.
     * @param array|string $table имя таблицы для джойна, может быть массивом вида array('name', 'alias');
     * @return self
     */
    public function innerJoin($table);

    /**
     * Задает условие для джойна последней таблицы.
     * @param string $leftColumn столбец основной таблицы
     * @param string $operator логический оператор ("=", ">", "<" ...)
     * @param string $rightColumn столбец присоединяемой таблицы
     * @return self
     */
    public function on($leftColumn, $operator, $rightColumn);

    /**
     * Устанавливает LIMIT на выборку.
     * @param integer|string $limit может быть либо числом, либо плейсхолдером
     * @param integer|string $offset может быть либо числом, либо плейсхолдером
     * @param bool $useCalcFoundRows использовать в запросе параметр для вычисления общего количества строк
     * @return self
     */
    public function limit($limit, $offset = null, $useCalcFoundRows = false);

    /**
     * Устанавливает OFFSET для выборки.
     * @param integer|string $offset может быть либо числом, либо плейсхолдером
     * @return self
     */
    public function offset($offset);

    /**
     * Устанавливает условие группировки.
     * @param string $column имя столбца, может быть плейсхолдером
     * @param string $direction направление сортировки, ASC по умолчанию
     * @return self
     */
    public function groupBy($column, $direction = IQueryBuilder::ORDER_ASC);

    /**
     * Возвращает массив столбцов для выборки.
     * в формате array(array('columnName', 'alias'), ...))
     * @internal
     * @return array
     */
    public function getSelectColumns();

    /**
     * Возвращает массив таблиц для выборки.
     * @internal
     * @return array в формате array(array('tableName', 'alias'), ...))
     */
    public function getTables();

    /**
     * Возвращает limit на выборку.
     * @internal
     * @return integer
     */
    public function getLimit();

    /**
     * Возвращает offset выборки.
     * @internal
     * @return integer
     */
    public function getOffset();

    /**
     * Проверяет, используется ли SQL_CALC_FOUND_ROWS для запроса.
     * @internal
     * @return bool
     */
    public function getUseCalcFoundRows();

    /**
     * Проверяет включен ли DISTINCT для выборки.
     * @internal
     * @return bool
     */
    public function getDistinct();

    /**
     * Возвращает список всех JOIN.
     * @internal
     * @return IJoinBuilder[]
     */
    public function getJoins();

    /**
     * Возвращает список правил группировки.
     * @internal
     * @return array в формате array('columnName' => 'ASC'))
     */
    public function getGroupByConditions();

    /**
     * Возвращает группу выражений для WHERE.
     * @internal
     * @return IExpressionGroup|null null, если нет WHERE-выражений
     */
    public function getWhereExpressionGroup();

    /**
     * Возвращает группу выражений для HAVING.
     * @internal
     * @return IExpressionGroup|null null, если нет HAVING-выражений
     */
    public function getHavingExpressionGroup();

    /**
     * Возвращает найденное число записей, удовлетворяюших запросу без LIMIT.
     * @throws IException
     * @return int
     */
    public function getTotal();

    /**
     * Начинает новую группу выражений.
     * Группа становится текущей до вызова end.
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
     * @param string $leftCondition левое выражение
     * @param string $operator оператор
     * @param string $rightCondition правое выражение
     * @throws RuntimeException если не удалось добавить выражение
     * @return self
     */
    public function expr($leftCondition, $operator, $rightCondition);

    /**
     * Добавляет условие сортировки.
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
}
