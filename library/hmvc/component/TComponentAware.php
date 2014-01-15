<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\component;

use umi\hmvc\exception\RequiredDependencyException;

/**
 * Трейт для внедрения поддержки создания MVC компонентов.
 */
trait TComponentAware
{
    /**
     * @var IComponentFactory $_hmvcComponentFactory фабрика
     */
    private $_hmvcComponentFactory;

    /**
     * Устанавливает фабрику MVC компонентов.
     * @param IComponentFactory $factory фабрика
     */
    public final function setHMVCComponentFactory(IComponentFactory $factory)
    {
        $this->_hmvcComponentFactory = $factory;
    }

    /**
     * Создает HMVC компонент.
     * @param array $options конфигурация
     * @return IComponent
     */
    protected final function createHMVCComponent(array $options)
    {
        return $this->getHMVCComponentFactory()
            ->createComponent($options);
    }

    /**
     * Возвращает фабрику для создания MVC компонентов.
     * @return IComponentFactory фабрика
     * @throws RequiredDependencyException если фабрика не была внедрена
     */
    private final function getHMVCComponentFactory()
    {
        if (!$this->_hmvcComponentFactory) {
            throw new RequiredDependencyException(sprintf(
                'HMVC component factory is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_hmvcComponentFactory;
    }
}
