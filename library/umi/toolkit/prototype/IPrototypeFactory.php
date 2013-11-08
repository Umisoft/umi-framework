<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\toolkit\prototype;

/**
 * Фабрика прототипов сервисов.
 */
interface IPrototypeFactory
{
    /**
     * Создает и возвращает прототип сервиса.
     * @param string $className имя класса для создания прототипа
     * @param array $contracts список контрактов, которым должен соответствовать прототип
     * @return IPrototype
     */
    public function create($className, array $contracts = []);
}
