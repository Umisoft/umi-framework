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
 * Интерфейс билдера результата маршрутизации.
 */
interface IRouteResultBuilder
{
    /**
     * Добавляет совпадение к результату.
     * @param string $name имя совпавшего маршрута
     * @param array $params параметры
     * @param string $matchedPart совпавшая часть URL
     * @return self
     */
    public function addMatch($name, array $params, $matchedPart);

    /**
     * Устанавливает оставшуюся часть URL
     * @param string $unmatchedPart оставшаяся часть URL.
     * @return self
     */
    public function setUnmatchedUrl($unmatchedPart);

    /**
     * Формирует результат маршрутеризации.
     * @return IRouteResult результат
     */
    public function getResult();
}