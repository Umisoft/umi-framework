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
 * Фильтр Html сущностей.
 * Преобразует все возможные символы в соответствующие HTML-сущности
 * @see http://php.net/manual/ru/function.htmlentities.php
 */
class HtmlEntities implements IFilter
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
        $flags = isset($this->options['flags']) ? $this->options['flags'] : null;
        $encoding = isset($this->options['encoding']) ? $this->options['encoding'] : null;

        return htmlentities($var, $flags, $encoding);
    }
}