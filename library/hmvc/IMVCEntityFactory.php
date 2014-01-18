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
use umi\hmvc\component\response\IComponentResponseFactory;
use umi\hmvc\controller\IControllerFactory;
use umi\hmvc\macros\IMacrosFactory;
use umi\hmvc\model\IModelFactory;
use umi\hmvc\view\IView;

/**
 * Интерфейс для создания сущностей, используемых в компоненте MVC.
 */
interface IMVCEntityFactory
{

    /**
     * Создает фабрику контроллеров для компонента.
     * @param IComponent $component
     * @param IComponentResponseFactory $responseFactory фабрика результатов работы компонента
     * @param array $controllerList список контроллеров в формате ['controllerName' => 'controllerClassName', ...]
     * @return IControllerFactory
     */
    public function createControllerFactory(IComponent $component, IComponentResponseFactory $responseFactory, array $controllerList);

    /**
     * Создает фабрику макросов для компонента.
     * @param IComponent $component
     * @param IComponentResponseFactory $responseFactory фабрика результатов работы компонента
     * @param array $macrosList список макросов в формате ['macrosName' => 'macrosClassName', ...]
     * @return IMacrosFactory
     */
    public function createMacrosFactory(IComponent $component, IComponentResponseFactory $responseFactory, array $macrosList);

    /**
     * Создает фабрику моделей.
     * @param array $options опции фабрики
     * @return IModelFactory
     */
    public function createModelFactory(array $options);

    /**
     * Создает слой отображения.
     * @param array $options опции
     * @return IView
     */
    public function createView(array $options);

    /**
     * Создает MVC компонент.
     * @param array $options конфигурация
     * @return IComponent
     */
    public function createComponent(array $options);

    /**
     * Создает HTTP запрос для компонента.
     * @param IComponent $component
     * @return IHTTPComponentRequest
     */
    public function createComponentRequest(IComponent $component);

    /**
     * Создает фабрику результатов работы компонента.
     * @param IComponent $component
     * @return IComponentResponseFactory
     */
    public function createResponseFactory(IComponent $component);
}