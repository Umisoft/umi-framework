<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\toolkit\toolbox;

use umi\toolkit\exception\UnsupportedServiceException;
use umi\toolkit\factory\IFactory;

/**
 * Набор инструментов.
 */
interface IToolbox extends IFactory
{
    /**
     * Возвращает сервис из набора по указанному интерфейсу.
     * @param string $serviceInterfaceName контракт сервиса
     * @param string $concreteClassName имя класса конкретной реализации, может быть использовано
     * для создания нового экземпляра сервиса.
     * @throws UnsupportedServiceException если сервис не поддерживается
     * @return object
     */
    public function getService($serviceInterfaceName, $concreteClassName);

    /**
     * Внедряет в объект сервисы, известные набору инструментов.
     * @param object $object
     */
    public function injectDependencies($object);
}
