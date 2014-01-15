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
 * Фильтр преобразует строку к верхнему регистру.
 * Преобразует строковое значение к верхнему регистру.
 */
class StringToUpper implements IFilter
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
        $encoding = isset($this->options['encoding']) ? $this->options['encoding'] : mb_internal_encoding();

        return mb_strtoupper($var, $encoding);
    }
}