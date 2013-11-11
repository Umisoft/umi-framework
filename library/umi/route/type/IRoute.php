<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\route\type;

/**
 * Интерфейс правила маршрутизатора.
 */
interface IRoute
{
    /** Опция для установки маршрута. */
    const OPTION_ROUTE = 'route';
    /** Опция для установки значений по умолчанию */
    const OPTION_DEFAULTS = 'defaults';

    /**
     * Проверяет соответствие части URL и правила маршрутизатора.
     * @param string $url часть адреса URL
     * @return int|bool длину совпадения, либо false, если совпадения нет
     */
    public function match($url);

    /**
     * Собирает часть URL из параметров.
     * @param array $params параметры URL
     * @return string часть URL
     */
    public function assemble(array $params = []);

    /**
     * Возвращает параметры, расширенные параметрами по умолчанию.
     * @return array
     */
    public function getParams();

    /**
     * Возвращает дочерние правила маршрутизатора.
     * @return self[]
     */
    public function getSubRoutes();
}