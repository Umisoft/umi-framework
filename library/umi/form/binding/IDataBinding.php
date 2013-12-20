<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\form\binding;

use umi\event\IEventObservant;

/**
 * Интерфейс связывания данных формы и объекта.
 */
interface IDataBinding
{
    /**
     * Событие, бросаемое при изменении какого либо свойства объекта.
     */
    const EVENT_UPDATE = 'form.objectUpdate';

    /**
     * Устанавливает данные в объект.
     * @param array $data
     * @return self
     */
    public function setData(array $data);

    /**
     * Возвращает данные из объекта.
     * @return array
     */
    public function getData();
}