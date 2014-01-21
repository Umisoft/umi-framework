<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\component;

use umi\hmvc\controller\IController;
use umi\hmvc\exception\OutOfBoundsException;
use umi\hmvc\exception\RuntimeException;
use umi\hmvc\macros\IMacros;
use umi\hmvc\view\IViewRenderer;
use umi\route\IRouter;

/**
 * Интерфейс MVC компонента системы.
 */
interface IComponent
{
    /**
     * Опция, для конфигурирования роутера
     */
    const OPTION_ROUTES = 'routes';
    /**
     * Опция, для конфигурирования моделей
     */
    const OPTION_MODELS = 'models';
    /**
     * Опция, для конфигурирования отображения
     */
    const OPTION_VIEW = 'view';
    /**
     * Опция, для конфигурирования контроллеров
     */
    const OPTION_CONTROLLERS = 'controllers';
    /**
     * Опция, для конфигурирования макросов
     */
    const OPTION_MACROS = 'macros';
    /**
     * Опция, для конфигурирования дочерних компонентов
     */
    const OPTION_COMPONENTS = 'components';

    /**
     * Имя параметра маршрута, для передачи управления дочернему компоненту
     */
    const MATCH_COMPONENT = 'component';
    /**
     * Имя параметра маршрута, для передачи управления контроллеру
     */
    const MATCH_CONTROLLER = 'controller';

    /**
     * Имя контроллера для обработки исключений
     */
    const ERROR_CONTROLLER = 'error';
    /**
     * Контроллер для отображения сетки компонента
     */
    const LAYOUT_CONTROLLER = 'layout';

    /**
     * Имя макроса для отображения ошибок работы макросов
     */
    const ERROR_MACROS = 'error';

    /**
     * Проверяет, существует ли дочерний компонент с заданным именем.
     * @param string $name имя компонента
     * @return bool
     */
    public function hasChildComponent($name);

    /**
     * Возвращает дочерний MVC компонент.
     * @param string $name имя компонента
     * @return IComponent
     */
    public function getChildComponent($name);

    /**
     * Возвращает маршрутеризатор компонента.
     * @return IRouter
     */
    public function getRouter();

    /**
     * Проверяет, существует ли контроллер в компоненте.
     * @param string $controllerName имя контроллера
     * @return bool
     */
    public function hasController($controllerName);

    /**
     * Возвращает контроллер компонента.
     * @param string $controllerName имя контроллера
     * @param array $args аргументы для создания контроллера
     * @throws OutOfBoundsException если контроллер не существует
     * @return IController
     */
    public function getController($controllerName, array $args = []);

    /**
     * Проверяет, существует ли макрос в компоненте.
     * @param string $macrosName имя макроса
     * @return bool
     */
    public function hasMacros($macrosName);

    /**
     * Возвращает макрос компонента.
     * @param string $macrosName имя макроса
     * @param array $args аргументы для создания макроса
     * @throws OutOfBoundsException если макрос не существует
     * @throws RuntimeException если макрос не callable
     * @return IMacros
     */
    public function getMacros($macrosName, array $args = []);

    /**
     * Возвращает рендерер шаблонов компонента.
     * @return IViewRenderer
     */
    public function getViewRenderer();

}