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

/**
 * Класс FilterFixture
 */
class FilterFixture implements IFilter
{

    /**
     * @var array $options опции фильтра
     */
    protected $options = [];

    /**
     * {@inheritdoc}
     */
    public function __construct(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filter($var)
    {
        if (!isset($this->options['default'])) {
            throw new \RuntimeException("No default option");
        }

        return $this->options['default'] . $var;
    }
}