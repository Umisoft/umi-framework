<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\route\toolbox;

use umi\route\IRouteAware;
use umi\toolkit\exception\UnsupportedServiceException;
use umi\toolkit\toolbox\IToolbox;
use umi\route\IRouteFactory;
use umi\toolkit\toolbox\TToolbox;

/**
 * Набор инструментов маршрутизации.
 */
class RouteTools implements IToolbox
{
    /**
     * Имя набора инструментов
     */
    const NAME = 'route';

    use TToolbox;

    /**
     * @var string $routeFactory класс фабрики правил маршрутеризации
     */
    public $routeFactoryClass = 'umi\route\toolbox\factory\RouteFactory';

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->registerFactory(
            'route',
            $this->routeFactoryClass,
            ['umi\route\IRouteFactory']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getService($serviceInterfaceName, $concreteClassName)
    {
        switch ($serviceInterfaceName) {
            case 'umi\route\IRouteFactory':
                return $this->getRouteFactory();
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
        if ($object instanceof IRouteAware) {
            $object->setRouteFactory($this->getRouteFactory());
        }
    }

    /**
     * Возвращает фабрику правил маршрутизатора.
     * @return IRouteFactory
     */
    protected function getRouteFactory()
    {
        return $this->getFactory('route');
    }
}