<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\toolbox\factory;

use umi\hmvc\component\IComponent;
use umi\hmvc\controller\IController;
use umi\hmvc\controller\IControllerFactory;
use umi\hmvc\exception\OutOfBoundsException;
use umi\hmvc\model\IModelAware;
use umi\hmvc\model\IModelFactory;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;
use umi\toolkit\prototype\IPrototype;

/**
 * Фабрика контроллеров MVC-компонента.
 */
class ControllerFactory implements IControllerFactory, IFactory, IModelAware
{
    use TFactory;
    /**
     * @var array $controllersList список контроллеров
     */
    protected $controllersList = [];
    /**
     * @var IModelFactory $modelFactory фабрика моделей
     */
    protected $modelFactory;
    /**
     * @var IComponent $component компонент
     */
    protected $component;

    /**
     * Конструктор.
     * @param IComponent $component
     * @param array $controllerList список контроллеров в формате ['controllerName' => 'controllerClassName', ...]
     */
    public function __construct(IComponent $component, array $controllerList)
    {
        $this->component = $component;
        $this->controllersList = $controllerList;
    }

    /**
     * {@inheritdoc}
     */
    public function createController($name, $args = [])
    {
        if (!$this->hasController($name)) {
            throw new OutOfBoundsException($this->translate(
                'Cannot create "{name}" controller. Controller not registered.',
                ['name' => $name]
            ));
        }

        $controller = $this->createControllerByClass($this->controllersList[$name], $args);
        $controller->setComponent($this->component);

        return $controller;
    }

    /**
     * {@inheritdoc}
     */
    public function hasController($name)
    {
        return isset($this->controllersList[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function setModelFactory(IModelFactory $factory)
    {
        $this->modelFactory = $factory;
    }

    /**
     * Создает контроллер заданного класса.
     * @param string $class класс контроллера
     * @param array $args аргументы конструктора
     * @return IController
     */
    protected function createControllerByClass($class, $args = [])
    {
        $controller = $this->getPrototype(
                $class,
                ['umi\hmvc\controller\IController'],
                function (IPrototype $prototype)
                {
                    $prototype->registerConstructorDependency(
                    'umi\hmvc\model\IModel',
                    function ($concreteClassName) {
                        if ($this->modelFactory) {
                            return $this->modelFactory->createByClass($concreteClassName);
                        }

                        return null;
                    }
                );
                }
            )
            ->createInstance($args);

        if ($controller instanceof IModelAware) {
            $controller->setModelFactory($this->modelFactory);
        }

        return $controller;
    }

}