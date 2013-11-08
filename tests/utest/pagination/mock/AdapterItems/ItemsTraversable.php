<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\pagination\mock\AdapterItems;

/**
 * Класс ItemsTraversable
 */
class ItemsTraversable implements \Iterator
{

    /**
     * @var array $container
     */
    private $container = [];

    /**
     * Конструктор.
     * @param array $container
     */
    public function __construct(array $container = [])
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        return current($this->container);
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        return next($this->container);
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return key($this->container);
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        return key($this->container) !== null;
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        return reset($this->container);
    }
}
