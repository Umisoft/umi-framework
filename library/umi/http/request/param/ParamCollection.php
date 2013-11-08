<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\http\request\param;

use umi\spl\container\TArrayAccess;

/**
 * Базовая реализация контейнера параметров.
 */
class ParamCollection implements IParamCollection
{

    use TArrayAccess;

    /**
     * @var array $container тип контейнера
     */
    protected $container;

    /**
     * Конструктор.
     * @param array $params конструктор
     */
    public function __construct(array &$params)
    {
        $this->container = & $params;
    }

    /**
     * {@inheritdoc}
     */
    public function get($attribute, $default = null)
    {
        return $this->has($attribute) ? $this->container[$attribute] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function set($attribute, $value)
    {
        $this->container[$attribute] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function has($attribute)
    {
        return isset($this->container[$attribute]);
    }

    /**
     * {@inheritdoc}
     */
    public function del($attribute)
    {
        unset($this->container[$attribute]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->container;
    }

    /**
     * {@inheritdoc}
     */
    public function setArray(array $data)
    {
        $this->container = $data;
    }
}