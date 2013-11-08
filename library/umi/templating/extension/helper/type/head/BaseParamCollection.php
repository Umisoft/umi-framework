<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\templating\extension\helper\type\head;

/**
 * Список параметров.
 */
abstract class BaseParamCollection
{
    /**
     * @var array $params список скриптов
     */
    protected static $params = [];

    /**
     * Добавляет параметр в конец списка.
     * @param mixed $param параметр
     * @return $this
     */
    protected function appendParam($param)
    {
        static::$params[] = $param;

        return $this;
    }

    /**
     * Добавляет параметр вначала списка.
     * @param mixed $param параметр
     * @return $this
     */
    protected function prependParam($param)
    {
        static::$params = array_merge([$param], static::$params);

        return $this;
    }
}