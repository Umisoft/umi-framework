<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\filter;

/**
 * Интерфейс фильтрации.
 */
interface IFilterCollection extends IFilter
{
    /**
     * Добавляет фильтр в конец цепочки фильтров.
     * @param IFilter $filter фильтр
     * @return self
     */
    public function appendFilter(IFilter $filter);

    /**
     * Добавляет фильтр в начало цепочки фильтров.
     * @param IFilter $filter фильтр
     * @return self
     */
    public function prependFilter(IFilter $filter);
}