<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc;

use umi\hmvc\component\IComponent;
use umi\hmvc\component\request\IHTTPComponentRequest;
use umi\hmvc\controller\IControllerFactory;
use umi\hmvc\exception\RequiredDependencyException;
use umi\hmvc\macros\IMacrosFactory;
use umi\hmvc\model\IModelFactory;
use umi\hmvc\view\IView;

/**
 * Трейт для внедрения возможости создания сущностей для компонента MVC.
 */
trait TMVCEntityFactoryAware
{
    /**
     * @var IMVCEntityFactory $_mvcLayerFactory
     */
    private $_MVCEntityFactory;

    /**
     * Устанавливает фабрику MVC сущностей.
     * @param IMVCEntityFactory $factory фабрика
     */
    public final function setMVCEntityFactory(IMVCEntityFactory $factory)
    {
        $this->_MVCEntityFactory = $factory;
    }

    /**
     * Создает фабрику контроллеров для компонента.
     * @param IComponent $component
     * @param array $controllerList список контроллеров в формате ['controllerName' => 'controllerClassName', ...]
     * @return IControllerFactory
     */
    protected final function createMVCControllerFactory(IComponent $component, array $controllerList)
    {
        return $this->getMVCEntityFactory()
            ->createControllerFactory(
                $component,
                $controllerList
            );
    }

    /**
     * Создает фабрику макросов для компонента.
     * @param IComponent $component
     * @param array $macrosList список макросов в формате ['macrosName' => 'macrosClassName', ...]
     * @return IMacrosFactory
     */
    protected final function createMVCMacrosFactory(IComponent $component, array $macrosList)
    {
        return $this->getMVCEntityFactory()
            ->createMacrosFactory(
                $component,
                $macrosList
            );
    }

    /**
     * Создает фабрику моделей.
     * @param array $options опции
     * @return IModelFactory
     */
    protected final function createMVCModelFactory(array $options)
    {
        return $this->getMVCEntityFactory()
            ->createModelFactory($options);
    }

    /**
     * Создает слой отображения.
     * @param array $options опции
     * @return IView
     */
    protected final function createMVCView(array $options)
    {
        return $this->getMVCEntityFactory()
            ->createView($options);
    }

    /**
     * Создает HMVC компонент.
     * @param array $options конфигурация
     * @return IComponent
     */
    protected final function createMVCComponent(array $options)
    {
        return $this->getMVCEntityFactory()
            ->createComponent($options);
    }

    /**
     * Создает HTTP запрос для компонента.
     * @param IComponent $component
     * @return IHTTPComponentRequest
     */
    protected final function createMVCComponentRequest(IComponent $component)
    {
        return $this->getMVCEntityFactory()
            ->createComponentRequest($component);
    }

    /**
     * Возвращает фабрику слоев MVC.
     * @return IMVCEntityFactory
     * @throws RequiredDependencyException если фабрика не внедрена
     */
    private final function getMVCEntityFactory()
    {
        if (!$this->_MVCEntityFactory) {
            throw new RequiredDependencyException(sprintf(
                'MVC entity factory is not injected in class "%s".',
                get_class($this)
            ));
        }

        return $this->_MVCEntityFactory;
    }

}
