<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\hmvc\toolbox;

use umi\hmvc\dispatcher\IDispatcher;
use umi\hmvc\IMVCEntityFactoryAware;
use umi\hmvc\IMVCEntityFactory;
use umi\toolkit\exception\UnsupportedServiceException;
use umi\toolkit\toolbox\IToolbox;
use umi\toolkit\toolbox\TToolbox;

/**
 * Инструменты для создания иерархической MVC-структуры приложений.
 */
class HMVCTools implements IToolbox
{
    /**
     * Имя набора инструментов.
     */
    const NAME = 'hmvc';

    use TToolbox;

    /**
     * @var string $MVCEntityFactoryClass фабрика сущностей компонента MVC
     */
    public $MVCEntityFactoryClass = 'umi\hmvc\toolbox\factory\MVCEntityFactory';
    /**
     * @var string $dispatcherClass класс диспетчера MVC-компонентов
     */
    public $dispatcherClass = 'umi\hmvc\dispatcher\Dispatcher';

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->registerFactory(
            'MVCEntityFactory',
            $this->MVCEntityFactoryClass,
            ['umi\hmvc\IMVCEntityFactory']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getService($serviceInterfaceName, $concreteClassName)
    {
        switch ($serviceInterfaceName) {
            case 'umi\hmvc\IMVCEntityFactory':
                return $this->getMVCEntityFactory();
            case 'umi\hmvc\dispatcher\IDispatcher':
                return $this->getDispatcher();
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
        if ($object instanceof IMVCEntityFactoryAware) {
            $object->setMVCEntityFactory($this->getMVCEntityFactory());
        }
    }

    /**
     * Возвращает фабрику MVC слоев.
     * @return IMVCEntityFactory
     */
    protected function getMVCEntityFactory()
    {
        return $this->getFactory('MVCEntityFactory');
    }

    /**
     * Возвращает диспетчер MVC-компонентов.
     * @return IDispatcher
     */
    protected function getDispatcher()
    {
        return $this->getPrototype(
            $this->dispatcherClass,
            ['umi\hmvc\dispatcher\IDispatcher']
        )
            ->createSingleInstance();
    }

}