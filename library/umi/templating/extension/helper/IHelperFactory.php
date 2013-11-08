<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\extension\helper;

/**
 * Интерфейс фабрики для создания помощников вида.
 */
interface IHelperFactory
{
    /**
     * Создает помощник для шаблонов.
     * @param string $class класс помощника вида
     * @return callable
     */
    public function createHelper($class);

}