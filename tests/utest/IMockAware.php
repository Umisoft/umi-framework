<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest;

/**
 * Интерфейс mock класса для тестирования внедрения зависимости.
 */
interface IMockAware
{

    /**
     * Возвращает внедренный сервис.
     * @return object
     */
    public function getService();
}
