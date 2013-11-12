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
use umi\toolkit\toolbox\TToolbox;

/**
 * Набор инструментов для фильтрации.
 */
class FilterTools implements IFilterTools
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
    public function getFilterFactory()
    {
        return $this->getFactory('filter');
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
}