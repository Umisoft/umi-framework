<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\macros;

use umi\hmvc\exception\OutOfBoundsException;

/**
 * Интерфейс фабрики макросов для компонента.
 */
interface IMacrosFactory
{

    /**
     * Создает макрос по имени.
     * @param string $name имя макроса
     * @param array $args аргументы конструктора
     * @throws OutOfBoundsException если макрос не существует
     * @return IMacros
     */
    public function createMacros($name, $args = []);

    /**
     * Проверяет существует ли макрос.
     * @param string $name имя макроса
     * @return bool
     */
    public function hasMacros($name);

}
 