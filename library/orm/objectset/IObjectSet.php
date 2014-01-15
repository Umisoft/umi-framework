<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\objectset;

use Countable;
use Iterator;
use umi\orm\exception\RuntimeException;
use umi\orm\object\IObject;
use umi\orm\selector\ISelector;

/**
 * Набор объектов коллекции.
 * Набор объектов представляет собой результат выборки объектов по каким-то параметрам
 * из одной коллекции. <br />
 * Наборы объектов могут быть использованы для доступа к результатам селектора,
 * для доступа к некоторым типам связей объекта.<br />
 */
interface IObjectSet extends Iterator, Countable
{
    /**
     * Устанавливает Selector для набора объектов.
     * @internal
     * @param ISelector $selector
     * @throws RuntimeException если Selector был установлен ранее
     * @return self
     */
    public function setSelector(ISelector $selector);

    /**
     * Возвращает Selector, установленный для набора объектов коллекции.
     * @throws RuntimeException если Selector не был установлен
     * @return ISelector
     */
    public function getSelector();

    /**
     * Возвращает весь список объектов IObject
     * @return array
     */
    public function fetchAll();

    /**
     * Возвращает следующий объект в наборе.
     * @return IObject|null объект, либо null, если достигли конца результата
     */
    public function fetch();

    /**
     * Сбрасывает все загруженные объекты.
     * Повторный fetch приведет к новому запросу в БД.
     * @return self
     */
    public function reset();
}
