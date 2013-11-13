<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\form\mock\binding;

use umi\event\TEventObservant;
use umi\form\binding\IDataBinding;

/**
 * Мок-класс для биндинга формы в свойства.
 */
class BindObject implements IDataBinding
{

    use TEventObservant;

    private $values = [];

    public function getData()
    {
        return $this->values;
    }

    public function setData(array $data)
    {
        $this->values = $data;

        return $this;
    }

    public function __set($name, $value)
    {
        $this->values[$name] = $value;

        $this->fireEvent(
            self::EVENT_UPDATE,
            [
                $name => $value
            ]
        );
    }

    public function __get($name)
    {
        return isset($this->values[$name]) ? $this->values[$name] : null;
    }
}