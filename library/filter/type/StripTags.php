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
 * Фильтр обрезает все тэги из строки.
 * Удаляет HTML и PHP-теги из строки.
 */
class StripTags implements IFilter
{

    /**
     * @var array $options опции фильтра
     * @example [allowed] = ['strong', 'img']
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
        $allowedTags = !empty($this->options['allowed']) ? '<' . implode('><', $this->options['allowed']) . '>' : null;

        return strip_tags($var, $allowedTags);
    }
}