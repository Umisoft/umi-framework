<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\filter;

/**
 * Класс коллекции фильтров.
 */
class FilterCollection implements IFilterCollection
{
    /**
     * @var IFilter[] $collection коллекция фильтров
     */
    protected $collection = [];

    /**
     * Конструктор коллекции фильтров
     * @param IFilter[] $collection коллекция фильтров
     */
    public function __construct(array $collection)
    {
        $this->collection = $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function filter($var)
    {
        foreach ($this->collection as $filter) {
            $var = $filter->filter($var);
        }

        return $var;
    }

    /**
     * {@inheritdoc}
     */
    public function appendFilter(IFilter $filter)
    {
        array_push($this->collection, $filter);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function prependFilter(IFilter $filter)
    {
        array_unshift($this->collection, $filter);

        return $this;
    }
}