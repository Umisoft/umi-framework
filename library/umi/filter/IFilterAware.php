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
 * Интерфейс для компонентов, поддерживающих фильтрацию.
 */
interface IFilterAware
{
    /**
     * Устанавливает фабрику для создания фильтров.
     * @param IFilterFactory $filterFactory фабрика
     */
    public function setFilterFactory(IFilterFactory $filterFactory);
}
