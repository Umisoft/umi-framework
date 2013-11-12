<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\pagination\toolbox;

use umi\pagination\IPaginationAware;
use umi\toolkit\toolbox\TToolbox;

/**
 * Инструменты работы с пагинатором.
 */
class PaginationTools implements IPaginationTools
{
    /**
     * Имя набора инструментов
     */
    const NAME = 'pagination';

    use TToolbox;

    /**
     * @var string $paginatorFactoryClass класс фабрики для создания пагинатора
     */
    public $paginatorFactoryClass = 'umi\pagination\toolbox\factory\PaginatorFactory';

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->registerFactory('paginator', $this->paginatorFactoryClass, ['umi\pagination\IPaginatorFactory']);
    }

    /**
     * {@inheritdoc}
     */
    public function injectDependencies($object)
    {
        if ($object instanceof IPaginationAware) {
            $object->setPaginatorFactory($this->getPaginatorFactory());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginatorFactory()
    {
        return $this->getFactory('paginator');
    }

}