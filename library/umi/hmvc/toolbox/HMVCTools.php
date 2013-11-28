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
use umi\hmvc\IMVCLayerAware;
use umi\hmvc\IMVCLayerFactory;
use umi\toolkit\exception\UnsupportedServiceException;
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
    public function getService($serviceInterfaceName, $concreteClassName)
    {
        switch ($serviceInterfaceName) {
            case 'umi\hmvc\component\IComponentFactory':
                return $this->getComponentFactory();
            case 'umi\hmvc\component\request\IComponentRequestFactory':
                return $this->getComponentRequestFactory();
        }
        throw new UnsupportedServiceException($this->translate(
            'Toolbox "{name}" does not support service "{interface}".',
            ['name' => self::NAME, 'interface' => $serviceInterfaceName]
        ));
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