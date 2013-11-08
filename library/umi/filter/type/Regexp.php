<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\filter\type;

use umi\filter\exception\RuntimeException;
use umi\filter\IFilter;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Фильтр по регулярному выражению.
 * Преобразует значение по регулярному выражению.
 */
class Regexp implements IFilter, ILocalizable
{

    use TLocalizable;

    /**
     * @var array $options опции фильтра
     */
    protected $options = [];

    /**
     * Конструктор.
     * @param array $options опции фильтра
     * @throws RuntimeException если не переданы обязательные опции
     */
    public function __construct(array $options)
    {
        $this->options = $options;

        $requiredOptions = ['pattern', 'replacement'];
        foreach ($requiredOptions as $option) {
            if (!isset($this->options[$option])) {
                throw new RuntimeException($this->translate(
                    'Required param "{param}" not found in options.',
                    ['param' => $option]
                ));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function filter($var)
    {

        $limit = isset($this->options['limit']) ? $this->options['limit'] : -1;

        return preg_replace($this->options['pattern'], $this->options['replacement'], $var, $limit);
    }
}