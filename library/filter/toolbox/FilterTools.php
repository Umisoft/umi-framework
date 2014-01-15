<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\filter\toolbox;

use umi\filter\IFilterAware;
use umi\filter\IFilterFactory;
use umi\toolkit\exception\UnsupportedServiceException;
use umi\toolkit\toolbox\IToolbox;
use umi\toolkit\toolbox\TToolbox;

/**
 * Набор инструментов для фильтрации.
 */
class FilterTools implements IToolbox
{
    /**
     * Имя набора инструментов
     */
    const NAME = 'filter';

    use TToolbox;

    /**
     * @var string $filterFactoryClass класс фабрики для фильтров
     */
    public $filterFactoryClass = 'umi\filter\toolbox\factory\FilterFactory';

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->registerFactory(
            'filter',
            $this->filterFactoryClass,
            ['umi\filter\IFilterFactory']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getService($serviceInterfaceName, $concreteClassName)
    {
        switch ($serviceInterfaceName) {
            case 'umi\filter\IFilterFactory':
                return $this->getFilterFactory();
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
        if ($object instanceof IFilterAware) {
            $object->setFilterFactory($this->getFilterFactory());
        }
    }

    /**
     * Возвращает фабрику фильтров.
     * @return IFilterFactory
     */
    protected function getFilterFactory()
    {
        return $this->getFactory('filter');
    }
}