<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\extension\helper\collection;

/**
 * Интерфейс коллекции помощников шаблонов.
 */
interface IHelperCollection
{
    /**
     * Добавляет помощники шаблонизатора в коллекцию.
     * @param array $helpers помощники
     * @return self
     */
    public function addHelpers(array $helpers);

    /**
     * Добавляет помощник шаблонизатора в коллекцию.
     * @param string $name имя помощника
     * @param string|callable $helper помощник
     * @return self
     */
    public function addHelper($name, $helper);

    /**
     * Возвращает имена помощников для шаблонизатора.
     * @return array
     */
    public function getList();

    /**
     * @param string $name
     * @return bool
     */
    public function hasHelper($name);

    /**
     * Возвращает помощник шаблонизатора.
     * @param string $name имя помощника
     * @return callable
     */
    public function getCallable($name);
}