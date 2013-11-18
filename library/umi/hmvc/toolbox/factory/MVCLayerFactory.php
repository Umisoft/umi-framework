<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\toolbox\factory;

use umi\hmvc\IMVCLayerFactory;
use umi\hmvc\view\extension\IViewExtensionFactoryAware;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика MVC слоев компонента.
 * Фабрика для создания сервиса отображения и фабрик моделей и контроллеров.
 */
class MVCLayerFactory implements IMVCLayerFactory, IFactory
{
    use TFactory;

    /**
     * @var string $modelFactoryClass класс фабрики моделей
     */
    public $modelFactoryClass = 'umi\hmvc\toolbox\factory\ModelFactory';
    /**
     * @var string $viewClass класс сервиса отображения
     */
    public $viewClass = 'umi\hmvc\view\TemplateView';
    /**
     * @var string $controllerFactoryClass класс фабрики контроллеров
     */
    public $controllerFactoryClass = 'umi\hmvc\toolbox\factory\ControllerFactory';

    public $viewExtensionFactoryClass = 'umi\hmvc\toolbox\factory\ViewExtensionFactory';

    /**
     * {@inheritdoc}
     */
    public function createControllerFactory(array $options)
    {
        return $this->getPrototype(
                $this->controllerFactoryClass,
                ['umi\hmvc\controller\IControllerFactory']
            )
            ->createInstance([$options]);
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
    public function createViewExtensionFactory()
    {
        return $this->getPrototype(
                $this->viewExtensionFactoryClass,
                ['umi\hmvc\view\extension\IViewExtensionFactory']
            )
            ->createInstance();
    }
}