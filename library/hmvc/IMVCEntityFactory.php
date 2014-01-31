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
use umi\hmvc\controller\IControllerFactory;
use umi\hmvc\macros\IMacrosFactory;
use umi\hmvc\model\IModelFactory;
use umi\hmvc\view\IViewRenderer;

/**
 * Интерфейс для создания сущностей, используемых в компоненте MVC.
 */
interface IMVCEntityFactory
{

    /**
     * Создает фабрику контроллеров для компонента.
     * @param IComponent $component
     * @param array $controllerList список контроллеров в формате ['controllerName' => 'controllerClassName', ...]
     * @return IControllerFactory
     */
    public function createControllerFactory(IComponent $component, array $controllerList);

    /**
     * Создает фабрику макросов для компонента.
     * @param IComponent $component
     * @param array $macrosList список макросов в формате ['macrosName' => 'macrosClassName', ...]
     * @return IMacrosFactory
     */
    public function createMacrosFactory(IComponent $component, array $macrosList);

    /**
     * Создает фабрику моделей.
     * @param array $options опции фабрики
     * @return IModelFactory
     */
    public function createModelFactory(array $options);

    /**
     * Создает рендерер шаблонов.
     * @param array $options опции
     * @return IViewRenderer
     */
    public function createViewRenderer(array $options);

    /**
     * Создает MVC компонент.
     * @param string $name имя компонента
     * @param string $path иерархический путь компонента
     * @param array $options конфигурация
     * @return IComponent
     */
    public function createComponent($name, $path, array $options);

}