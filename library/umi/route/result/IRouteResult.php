<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\route\result;

/**
 * Интерфейс результата маршрутизации.
 */
interface IRouteResult
{
    /**
     * Возвращает массив совпадений.
     * @return array
     */
    public function getMatches();

    /**
     * Возвращает имя маршрута.
     * @return string
     */
    public function getName();

    /**
     * Возвращает совпавшую часть URL.
     * @return string
     */
    public function getMatchedUrl();

    /**
     * Возвращает несовпавшую часть URL.
     * @return string
     */
    public function getUnmatchedUrl();
}