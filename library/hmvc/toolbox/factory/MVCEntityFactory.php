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
use umi\hmvc\component\response\IComponentResponseFactory;
use umi\hmvc\IMVCEntityFactory;
use umi\hmvc\view\extension\IViewExtensionFactory;
use umi\hmvc\view\extension\IViewExtensionFactoryAware;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика для создания сущностей, используемых в компоненте MVC.
 */
class MVCEntityFactory implements IMVCEntityFactory, IFactory
{
    use TFactory;

    /**
     * @var string $modelFactoryClass класс фабрики моделей
     */
    public $modelFactoryClass = 'umi\hmvc\toolbox\factory\ModelFactory';
    /**
     * @var string $viewClass класс сервиса отображения
     */
    public $viewClass = 'umi\hmvc\view\View';
    /**
     * @var string $controllerFactoryClass класс фабрики контроллеров
     */
    public $controllerFactoryClass = 'umi\hmvc\toolbox\factory\ControllerFactory';
    /**
     * @var string $macrosFactoryClass класс фабрики макросов
     */
    public $macrosFactoryClass = 'umi\hmvc\toolbox\factory\MacrosFactory';
    /**
     * @var string $viewExtensionFactoryClass класс фабрики расширений View
     */
    public $viewExtensionFactoryClass = 'umi\hmvc\toolbox\factory\ViewExtensionFactory';
    /**
     * @var string $componentClass MVC компонент по умолчанию
     */
    public $componentClass = 'umi\hmvc\component\Component';
    /**
     * @var string $componentResponseClass класс результата работы компонента
     */
    public $componentRequestClass = 'umi\hmvc\component\request\HTTPComponentRequest';
    /**
     * @var string $componentResponseFactoryClass фабрика результатов работы компонента
     */
    public $componentResponseFactoryClass = 'umi\hmvc\toolbox\factory\ComponentResponseFactory';

    /**
     * {@inheritdoc}
     */
    public function createControllerFactory(IComponent $component, IComponentResponseFactory $responseFactory, array $controllerList)
    {
        return $this->getPrototype(
                $this->controllerFactoryClass,
                ['umi\hmvc\controller\IControllerFactory']
            )
            ->createInstance([$component, $responseFactory, $controllerList]);
    }

    /**
     * {@inheritdoc}
     */
    public function createMacrosFactory(IComponent $component, IComponentResponseFactory $responseFactory, array $macrosList)
    {
        return $this->getPrototype(
            $this->macrosFactoryClass,
            ['umi\hmvc\macros\IMacrosFactory']
        )
            ->createInstance([$component, $responseFactory, $macrosList]);
    }

    /**
     * {@inheritdoc}
     */
    public function createModelFactory(array $options)
    {
        return $this->getPrototype(
                $this->modelFactoryClass,
                ['umi\hmvc\model\IModelFactory']
            )
            ->createInstance([$options]);
    }

    /**
     * {@inheritdoc}
     */
    public function createView(array $options)
    {
        $view = $this->getPrototype(
                $this->viewClass,
                ['umi\hmvc\view\IView']
            )
            ->createInstance([$options]);

        if ($view instanceof IViewExtensionFactoryAware) {
            $view->setViewExtensionFactory($this->createViewExtensionFactory());
        }

        return $view;
    }

    /**
     * {@inheritdoc}
     */
    public function createComponent(array $options)
    {
        $componentClass = isset($options['componentClass']) ? $options['componentClass'] : $this->componentClass;
        unset($options['componentClass']);

        return $this->getPrototype(
            $componentClass,
            ['umi\hmvc\component\IComponent']
        )
            ->createInstance([$options]);
    }

    /**
     * {@inheritdoc}
     */
    public function createComponentRequest(IComponent $component)
    {
        return $this->getPrototype(
            $this->componentRequestClass,
            ['umi\hmvc\component\request\IHTTPComponentRequest']
        )
            ->createInstance([$component]);
    }

    /**
     * {@inheritdoc}
     */
    public function createResponseFactory(IComponent $component)
    {
        return $this->getPrototype(
            $this->componentResponseFactoryClass,
            ['umi\hmvc\component\response\IComponentResponseFactory']
        )
            ->createInstance([$component]);
    }

    /**
     * Создает фабрику расширений для View
     * @return IViewExtensionFactory
     */
    protected function createViewExtensionFactory()
    {
        return $this->getPrototype(
            $this->viewExtensionFactoryClass,
            ['umi\hmvc\view\extension\IViewExtensionFactory']
        )
            ->createInstance();
    }
}