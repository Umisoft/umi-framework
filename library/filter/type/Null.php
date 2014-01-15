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
 * Фильтр NULL.
 * Преобразует значение к NULL.
 */
class Null implements IFilter
{

    /**
     * @var array $options опции фильтра
     * @example [optional_values] = ['null']
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
        $optional = isset($this->options['optional_values']) ? $this->options['optional_values'] : [];
        if (array_search($var, $optional) !== false) {
            return null;
        } else {
            return $var ? : null;
        }
    }
}