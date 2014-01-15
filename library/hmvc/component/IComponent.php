<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\component;

use umi\hmvc\component\request\IComponentRequest;
use umi\hmvc\component\response\IComponentResponse;
use umi\route\IRouter;

/**
 * Интерфейс MVC компонента системы.
 */
interface IComponent
{
    /** Опция, для конфигурирования роутера */
    const OPTION_ROUTES = 'routes';
    /** Опция, для конфигурирования моделей */
    const OPTION_MODELS = 'models';
    /** Опция, для конфигурирования отображения */
    const OPTION_VIEW = 'view';
    /** Опция, для конфигурирования контроллеров */
    const OPTION_CONTROLLERS = 'controllers';
    /** Опция, для конфигурирования дочерних компонентов */
    const OPTION_COMPONENTS = 'components';

    /** Имя параметра маршрута, для передачи управления дочернему компоненту */
    const MATCH_COMPONENT = 'component';
    /** Имя параметра маршрута, для передачи управления контроллеру */
    const MATCH_CONTROLLER = 'controller';

    /** Имя контроллера для обработки исключений */
    const ERROR_CONTROLLER = 'error';

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
     * Выполняет HTTP запрос.
     * @param IComponentRequest $request запрос
     * @return IComponentResponse
     */
    public function execute(IComponentRequest $request);

    /**
     * Выполняет запрос заданным контроллером.
     * @param string $controller имя контроллера
     * @param IComponentRequest $request HTTP запрос
     * @return IComponentResponse
     */
    public function call($controller, IComponentRequest $request);
}