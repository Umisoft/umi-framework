<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\object\property;

/**
 * Интерфейс свойства-счетчика.
 */
interface ICounterProperty extends ICalculableProperty
{

    /**
     * Увеличивает значение счетчика на единицу.
     * @return self
     */
    public function increment();

    /**
     * Уменьшает значение счетчика на единицу.
     * @return self
     */
    public function decrement();
}
