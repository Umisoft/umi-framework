<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\toolbox;

use umi\hmvc\component\IComponentAware;
use umi\hmvc\component\IComponentFactory;
use umi\hmvc\component\request\IComponentRequestAware;
use umi\hmvc\component\request\IComponentRequestFactory;
use umi\hmvc\component\response\IComponentResponseAware;
use umi\hmvc\component\response\IComponentResponseFactory;
use umi\hmvc\controller\result\IControllerResultAware;
use umi\hmvc\controller\result\IControllerResultFactory;
use umi\hmvc\IMVCLayerAware;
use umi\hmvc\IMVCLayerFactory;
use umi\toolkit\toolbox\IToolbox;
use umi\toolkit\toolbox\TToolbox;

/**
 * Инструменты для создания Hierarchy Model-TemplateView-Controller архитектуры приложений.
 */
class HMVCTools implements IToolbox
{
    /**
     * Имя набора инструментов.
     */
    const NAME = 'hmvc';

    use TToolbox;

    /**
     * @var string $controllerResultFactoryClass фабрика результатов работы контроллера
     */
    public $controllerResultFactoryClass = 'umi\hmvc\toolbox\factory\ControllerResultFactory';
    /**
     * @var string $componentResponseFactoryClass фабрика результатов работы компонента
     */
    public $componentResponseFactoryClass = 'umi\hmvc\toolbox\factory\ComponentResponseFactory';
    /**
     * @var string $componentResponseFactoryClass фабрика результатов работы компонента
     */
    public $componentRequestFactoryClass = 'umi\hmvc\toolbox\factory\ComponentRequestFactory';
    /**
     * @var string $mvcFactoryClass фабрика MVC слоев
     */
    public $mvcLayerFactoryClass = 'umi\hmvc\toolbox\factory\MVCLayerFactory';
    /**
     * @var string $componentFactoryClass фабрика MVC компонентов
     */
    public $componentFactoryClass = 'umi\hmvc\toolbox\factory\ComponentFactory';

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->registerFactory(
            'controllerResult',
            $this->controllerResultFactoryClass,
            ['umi\hmvc\controller\result\IControllerResultFactory']
        );
        $this->registerFactory(
            'componentResponse',
            $this->componentResponseFactoryClass,
            ['umi\hmvc\component\response\IComponentResponseFactory']
        );

        $this->registerFactory(
            'componentRequest',
            $this->componentRequestFactoryClass,
            ['umi\hmvc\component\request\IComponentRequestFactory']
        );

        $this->registerFactory(
            'mvcLayer',
            $this->mvcLayerFactoryClass,
            ['umi\hmvc\IMVCLayerFactory']
        );

        $this->registerFactory(
            'component',
            $this->componentFactoryClass,
            ['umi\hmvc\component\IComponentFactory']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function injectDependencies($object)
    {
        if ($object instanceof IMVCLayerAware) {
            $object->setMvcFactory($this->getMVCFactory());
        }

        if ($object instanceof IComponentAware) {
            $object->setHMVCComponentFactory($this->getComponentFactory());
        }

        if ($object instanceof IControllerResultAware) {
            $object->setControllerResultFactory($this->getControllerResultFactory());
        }

        if ($object instanceof IComponentResponseAware) {
            $object->setComponentResponseFactory($this->getComponentResponseFactory());
        }

        if ($object instanceof IComponentRequestAware) {
            $object->setComponentRequestFactory($this->getComponentRequestFactory());
        }
    }

    /**
     * Возвращает фабрику для создания MVC компонентов.
     * @return IComponentFactory
     */
    protected function getComponentFactory()
    {
        return $this->getFactory('component');
    }

    /**
     * Возвращает фабрику HTTP запросов компонента.
     * @return IComponentRequestFactory
     */
    protected function getComponentRequestFactory()
    {
        return $this->getFactory('componentRequest');
    }

    /**
     * Возвращает фабрику результатов работы контроллера.
     * @return IControllerResultFactory
     */
    protected function getControllerResultFactory()
    {
        return $this->getFactory('controllerResult');
    }

    /**
     * Возвращает фабрику результатов работы компонента.
     * @return IComponentResponseFactory
     */
    protected function getComponentResponseFactory()
    {
        return $this->getFactory('componentResponse');
    }

    /**
     * Возвращает фабрику MVC слоев.
     * @return IMVCLayerFactory
     */
    protected function getMVCFactory()
    {
        return $this->getFactory('mvcLayer');
    }
}