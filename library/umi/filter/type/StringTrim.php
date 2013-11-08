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
 * Фильтр обрезает строку по краям.
 * Удаляет пробелы (или другие символы) из начала и конца строки.
 */
class StringTrim implements IFilter
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
        $charlist = isset($this->options['charlist']) ? $this->options['charlist'] : " \t\n\r\0\x0B";

        return trim($var, $charlist);
    }
}