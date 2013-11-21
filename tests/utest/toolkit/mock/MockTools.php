<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\toolkit\mock;

use umi\toolkit\exception\UnsupportedServiceException;
use umi\toolkit\toolbox\IToolbox;
use umi\toolkit\toolbox\TToolbox;

/**
 * Набор инструментов для тестов
 */
class MockTools implements IMockTools, IToolbox
{

    use TToolbox;
    /**
     * Имя набора инструментов
     */
    const NAME = 'MockTools';
    /**
     * @var string $factoryClass
     */
    public $factoryClass = 'utest\toolkit\mock\TestFactory';
    /**
     * @var array $factoryOptions
     */
    public $factoryOptions = [];

    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->registerFactory(
            'testFactory',
            $this->factoryClass
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getService($serviceInterfaceName, $concreteClassName)
    {
        switch ($serviceInterfaceName) {
            case 'utest\toolkit\mock\IMockService':
                if (!is_null($concreteClassName)) {
                    return new $concreteClassName();
                }

                return new MockService();
            case 'utest\toolkit\mock\TestFactory':
                return $this->getTestFactory();

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
        if ($object instanceof MockServicingInterface) {
            $object->setDependency('injectedDependency');
        }
    }

    /**
     * Создает и возвращает фабрику.
     * @return TestFactory
     */
    protected function getTestFactory()
    {
        return $this->getFactory('testFactory', $this->factoryOptions);
    }

}
