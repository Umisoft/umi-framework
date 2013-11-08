<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\filter\type;

use umi\filter\IFilter;

/**
 * Фильтр преобразует строку к целочисленому значению.
 * Преобразует значение к целочисленому.
 */
class Int implements IFilter
{

    /**
     * @var array $options опции фильтра
     */
    protected $options = [];

    /**
     * Конструктор
     * @param array $options опции фильтра
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function filter($var)
    {
        $base = isset($this->options['base']) ? $this->options['base'] : null;

        return intval($var, $base);
    }
}