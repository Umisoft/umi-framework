<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\filter\mock;

use umi\filter\IFilter;
use umi\filter\IFilterCollection;

/**
 * Мок-класс коллекции фильтров
 */
class FilterCollectionFixture implements IFilterCollection
{
    /**
     * {@inheritdoc}
     */
    public function filter($var)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function appendFilter(IFilter $filter)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function prependFilter(IFilter $filter)
    {
        return $this;
    }
}
