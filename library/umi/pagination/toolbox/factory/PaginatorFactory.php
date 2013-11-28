<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\pagination\toolbox\factory;

use umi\pagination\adapter\IPaginationAdapter;
use umi\pagination\exception\InvalidArgumentException;
use umi\pagination\IPaginatorFactory;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика пагинаторов.
 */
class PaginatorFactory implements IPaginatorFactory, IFactory
{

    use TFactory;

    /**
     * @var string $paginatorClass класс пагинатора
     */
    public $paginatorClass = 'umi\pagination\Paginator';

    /**
     * @var array $paginationAdapters список адаптеров пагинатора
     */
    public $paginationAdapters = [
        IPaginationAdapter::ARRAY_ADAPTER => 'umi\pagination\adapter\ArrayPaginationAdapter',
        'umi\orm\selector\ISelector'      => 'umi\pagination\adapter\SelectorPaginationAdapter',
    ];

    /**
     * {@inheritdoc}
     */
    public function createPaginator($objects, $itemsPerPage)
    {
        return $this->getPrototype(
            $this->paginatorClass,
            ['umi\pagination\IPaginator']
        )
            ->createInstance([$this->createAdapter($objects), $itemsPerPage]);
    }

    /**
     * Выбирает и создает адаптер для переданных объектов.
     * @param mixed $objects объекты пагинации
     * @return IPaginationAdapter адаптер
     */
    protected function createAdapter($objects)
    {
        return $this->getPrototype(
                $this->selectAdapter($objects),
                ['umi\pagination\adapter\IPaginationAdapter']
            )
            ->createInstance([$objects]);
    }

    /**
     * Выбирает адаптер для переданных объектов.
     * @param mixed $objects объекты
     * @return string класс выбранного адаптера
     * @throws InvalidArgumentException если адаптер не был выбран.
     */
    protected function selectAdapter($objects)
    {
        if (is_array($objects)) {
            return $this->paginationAdapters[IPaginationAdapter::ARRAY_ADAPTER];
        }

        foreach ($this->paginationAdapters as $interface => $adapter) {
            if ($objects instanceof $interface) {
                return $adapter;
            }
        }

        throw new InvalidArgumentException($this->translate(
            'Pagination adapter not found for objects({type}).',
            ['type' => is_object($objects) ? get_class($objects) : gettype($objects)]
        ));
    }
}