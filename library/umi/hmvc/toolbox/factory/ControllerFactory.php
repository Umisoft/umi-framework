<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\toolbox\factory;

use umi\hmvc\controller\IController;
use umi\hmvc\controller\IControllerFactory;
use umi\hmvc\exception\OutOfBoundsException;
use umi\hmvc\model\IModelAware;
use umi\hmvc\model\IModelFactory;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;
use umi\toolkit\prototype\IPrototype;

/**
 * Фабрика контроллеров.
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
     * Конструктор.
     * @param array $options опции
     */
    public function __construct(array $options)
    {
        $this->controllersList = $options;
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

        return $this->createControllerByClass($this->controllersList[$name], $args);
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
     * {@inheritdoc}
     */
    protected function initPrototype(IPrototype $prototype)
    {
        $prototype->registerConstructorDependency(
            'umi\hmvc\model\IModel',
            function ($concreteClassName) {
                if ($this->modelFactory instanceof IModelFactory) {
                    return $this->modelFactory->createByClass($concreteClassName);
                }

                return null;
            }
        );
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
                ['umi\hmvc\controller\IController']
            )
            ->createInstance($args);

        if ($controller instanceof IModelAware) {
            $controller->setModelFactory($this->modelFactory);
        }

        return $controller;
    }

}