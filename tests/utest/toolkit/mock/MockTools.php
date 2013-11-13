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
    /**
     * Имя набора инструментов
     */
    const NAME = 'MockTools';

    use TToolbox;

    /**
     * {@inheritdoc}
     */
    public function getService($serviceInterfaceName, $concreteClassName)
    {
        switch ($serviceInterfaceName) {
            case 'utest\toolkit\mock\IMockService':
            {
                if (!is_null($concreteClassName)) {
                    return new $concreteClassName();
                }

                return new MockService();
            }

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

}
