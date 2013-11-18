<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\dbal\builder;

/**
 * Построитель Join - секции запроса.
 */
interface IJoinBuilder
{
    /**
     * Условие для джойна. Условия для джойна всегда складываются по AND
     * @param string $leftColumn имя столбца основной таблицы в формате TableOrAlias.columnName
     * @param string $operator логический оператор ("=", ">", "<" ...)
     * @param string $rightColumn столбец присоединяемой таблицы в формате TableOrAlias.columnName
     * @return self
     */
    public function on($leftColumn, $operator, $rightColumn);

    /**
     * Возвращает список условий для джойна
     * @internal
     * @return array в формате array(array('leftColumn', 'logicOperator', 'rightColumn'), ...)
     */
    public function getConditions();

    /**
     * Возвращает имя таблицы
     * @internal
     * @return array в формате array('name', 'alias')
     */
    public function getTable();

    /**
     * Возвращает тип джойна
     * @internal
     * @return string тип джойна
     */
    public function getType();
}
