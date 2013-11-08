<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\controller;

/**
 * Интерфейс фабрики контроллеров.
 */
interface IControllerFactory
{
    /**
     * Создает контроллер по символическому имени.
     * @param string $name имя контроллера
     * @param array $args аргументы для создания контроллера
     * @return IController
     */
    public function createController($name, $args = []);

    /**
     * Проверяет, существует ли контроллер с заданным символическим именем.
     * @param string $name имя контроллера
     * @return bool
     */
    public function hasController($name);
}