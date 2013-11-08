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
 * Фильтр преобразует строку к логическому типу.
 * Преобразует значение к boolean.
 */
class Boolean implements IFilter
{

    /**
     * @var array $options опции фильтра
     * @example [optional_values] = ['yes' => true, 'no' => false]
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
        if (isset($this->options['optional_values'][$var])) {
            return (bool) $this->options['optional_values'][$var];
        } else {
            return (bool) $var;
        }
    }
}