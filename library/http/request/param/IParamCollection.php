<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\http\request\param;

use umi\spl\container\IContainer;

/**
 * Интерфейс коллекции параметров.
 */
interface IParamCollection extends IContainer
{

    /**
     * Возвращает параметр из коллекции
     * @param string $name Имя параметра
     * @param mixed $default Значение по умолчанию
     * @return mixed Значение параметра
     */
    public function get($name, $default = null);

    /**
     * Возвращает содержимое коллекции параметров в виде массива.
     * @return array массив параметров
     */
    public function toArray();

    /**
     * Устанавливает содержимое коллекции параметров.
     * @param array $data массив параметров
     * @return self
     */
    public function setArray(array $data);
}