<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\model;

use umi\toolkit\exception\RequiredDependencyException;

/**
 * Трейт для внедрения поддержки работы с моделями.
 */
trait TModelAware
{
    /**
     * @var IModelFactory $_hmvcModelFactory фабрика моделей
     */
    private $_hmvcModelFactory;

    /**
     * Устанавливает фабрику моделей.
     * @param IModelFactory $factory
     */
    public final function setModelFactory(IModelFactory $factory)
    {
        $this->_hmvcModelFactory = $factory;
    }

    /**
     * Создает новую модель по символическому имени.
     * @param string $name
     * @return IModel|object
     */
    protected final function createModelByName($name)
    {
        return $this->getModelFactory()
            ->createByName($name);
    }

    /**
     * Создает новую модель по имени класса.
     * @param string $class класс
     * @return IModel|object
     */
    protected final function createModelByClass($class)
    {
        return $this->getModelFactory()
            ->createByClass($class);
    }

    /**
     * Возвращает фабрику моделей.
     * @return IModelFactory фабрика
     * @throws RequiredDependencyException если фабрика не внедрена
     */
    private final function getModelFactory()
    {
        if (!$this->_hmvcModelFactory) {
            throw new RequiredDependencyException(sprintf(
                'Model factory is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_hmvcModelFactory;
    }
}
