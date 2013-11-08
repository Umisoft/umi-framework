<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc;

use umi\hmvc\controller\IControllerFactory;
use umi\hmvc\exception\RequiredDependencyException;
use umi\hmvc\model\IModelFactory;
use umi\hmvc\view\IView;

/**
 * Трейт для внедрения возможости создания слоев MVC.
 */
trait TMVCLayerAware
{
    /**
     * @var IMVCLayerFactory $_mvcLayerFactory
     */
    private $_mvcLayerFactory;

    /**
     * Устанавливает фабрику MVC сущностей.
     * @param IMVCLayerFactory $factory фабрика
     */
    public final function setMvcFactory(IMVCLayerFactory $factory)
    {
        $this->_mvcLayerFactory = $factory;
    }

    /**
     * Создает фабрику контроллеров.
     * @param array $options опции
     * @return IControllerFactory
     */
    protected final function createMvcControllerFactory(array $options)
    {
        return $this->getMVCLayerFactory()
            ->createControllerFactory($options);
    }

    /**
     * Создает фабрику моделей.
     * @param array $options опции
     * @return IModelFactory
     */
    protected final function createMvcModelFactory(array $options)
    {
        return $this->getMVCLayerFactory()
            ->createModelFactory($options);
    }

    /**
     * Создает слой отображения.
     * @param array $options опции
     * @return IView
     */
    protected final function createMvcView(array $options)
    {
        return $this->getMVCLayerFactory()
            ->createView($options);
    }

    /**
     * Возвращает фабрику слоев MVC.
     * @return IMVCLayerFactory
     * @throws RequiredDependencyException если фабрика не внедрена
     */
    private final function getMVCLayerFactory()
    {
        if (!$this->_mvcLayerFactory) {
            throw new RequiredDependencyException(sprintf(
                'MVC layer factory is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_mvcLayerFactory;
    }

}
