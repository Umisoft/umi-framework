<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\spl\mock\container;

use umi\spl\container\IContainer;
use umi\spl\container\TArrayAccess;
use umi\spl\container\TPropertyAccess;

/**
 * Мок-класс ArrayContainer
 */
class ArrayPropertyContainer implements IContainer, \ArrayAccess
{

    /**
     * @var array $array
     */
    public $array;

    use TArrayAccess;
    use TPropertyAccess;

    /**
     * {@inheritdoc}
     */
    public function get($attribute)
    {
        return $this->array[$attribute];
    }

    /**
     * {@inheritdoc}
     */
    public function set($attribute, $value)
    {
        $this->array[$attribute] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function has($attribute)
    {
        return isset($this->array[$attribute]);
    }

    /**
     * {@inheritdoc}
     */
    public function del($attribute)
    {
        unset($this->array[$attribute]);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->array = [];
    }
}