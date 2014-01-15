<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\model;

/**
 * Интерфейс фабрики для создания моделей.
 * Хранит список моделей и предоставляет к ним доступ.
 */
interface IModelFactory
{
    /**
     * Создает новую модель по символическому имени.
     * @param string $name символическое имя
     * @param array $args аргументы конструктора
     * @return IModel|object
     */
    public function createByName($name, $args = []);

    /**
     * Создает новую модель по имени класса.
     * @param string $class класс
     * @param array $args аргументы конструктора
     * @return IModel|object
     */
    public function createByClass($class, $args = []);
}