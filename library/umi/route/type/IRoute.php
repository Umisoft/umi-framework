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
    /**
     * Опция, для принудительного выставления в ассемблируемый путь параметров по умолчанию.
     */
    const OPTION_FORCE_DEFAULT = 'forceDefault';

    /**
     * Проверяет соответствие части URL и правила маршрутизатора.
     * @param string $url часть адреса URL
     * @return int|bool длину совпадения, либо false, если совпадения нет
     */
    public function match($url);

    /**
     * Собирает часть URL из параметров.
     * @param array $params параметры URL
     * @param array $options опции сборки
     * @return string часть URL
     */
    public function assemble(array $params = [], array $options = []);

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