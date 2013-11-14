<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\toolkit;

use umi\toolkit\exception\RequiredDependencyException;

/**
 * Трейт для поддержки toolkit.
 */
trait TToolkitAware
{
    /**
     * @var IToolkit $_toolkit
     */
    private $_toolkit;

    /**
     * Устанавливает toolkit.
     * @param IToolkit $toolkit
     */
    public function setToolkit(IToolkit $toolkit)
    {
        $this->_toolkit = $toolkit;
    }

    /**
     * Возвращает toolkit.
     * @throws RequiredDependencyException если toolkit не был внедрен
     * @return IToolkit
     */
    protected function getToolkit()
    {
        if (!$this->_toolkit) {
            throw new RequiredDependencyException(sprintf(
                'Toolkit are not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_toolkit;
    }

    /**
     * Возвращает экземпляр сервиса.
     * @param string $serviceInterfaceName имя интерфейса сервиса
     * @param null|string $concreteClassName класс конкретной реализации сервиса, может быть учтен при
     * получении экземпляра сервиса.
     * @return object
     */
    protected function getService($serviceInterfaceName, $concreteClassName = null) {
        return $this->getToolkit()->getService($serviceInterfaceName, $concreteClassName);
    }

    /**
     * Проверяет, зарегистрирован ли сервис
     * @param string $serviceInterfaceName имя интерфейса сервиса
     * @return bool
     */
    protected function hasService($serviceInterfaceName) {
        return $this->getToolkit()->hasService($serviceInterfaceName);
    }
}
