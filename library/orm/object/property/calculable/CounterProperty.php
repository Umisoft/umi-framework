<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\object\property\calculable;

use umi\orm\metadata\field\IField;
use umi\orm\object\IObject;

/**
 * Класс свойства счетчика.
 */
class CounterProperty extends CalculableProperty implements ICounterProperty
{

    /**
     * Конструктор
     * @param IObject $object владелец свойства
     * @param IField $field поле типа данных
     */
    public function __construct(IObject $object, IField $field)
    {
        $this->object = $object;
        $this->field = $field;
    }

    /**
     * {@inheritdoc}
     */
    public function increment()
    {
        $value = $this->getValue() + 1;
        $this->setValue($value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function decrement()
    {
        $value = $this->getValue() - 1;
        $this->setValue($value);

        return $this;
    }
}
