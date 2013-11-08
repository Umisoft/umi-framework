<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\route;

use umi\route\result\IRouteResult;

/**
 * Интерфейс маршрутизатора.
 */
interface IRouter
{
    /**
     * Проверяет соответствие URL и правила маршрутизатора.
     * @param string $url часть адреса URL
     * @return IRouteResult параметры подходящего маршрута
     */
    public function match($url);

    /**
     * Собирает URL из параметров.
     * @param $name название маршрута, вида "first/second"
     * @param array $params параметры ассемблирования
     * @param array $options опции асемблирования
     * @return string собранный URL
     */
    public function assemble($name, array $params = [], array $options = []);

    /**
     * Устанавливает базовый URL для маршрутизатора.
     * @param string $url базовый URL
     * @return self
     */
    public function setBaseUrl($url);

    /**
     * Возвращает базовый URL для маршрутизатора.
     * @return string базовый URL
     */
    public function getBaseUrl();
}