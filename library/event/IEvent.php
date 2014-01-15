<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\event;

/**
 * Событие.
 */
interface IEvent
{
    /**
     * Возвращает тип события
     * @return string
     */
    public function getType();

    /**
     * Возвращает компонент, в котором произошло событие
     * @return mixed
     */
    public function getTarget();

    /**
     * Возвращает тэги, с которыми произошло событие
     * @return array
     */
    public function getTags();

    /**
     * Отменяет вызов остальных обработчиков события
     * @param bool $stopped
     * @return self
     */
    public function stopPropagation($stopped = true);

    /**
     * Узнает, нужно ли вызывать оставшиеся обработчики событий
     * @return bool
     */
    public function getPropagationIsStopped();

    /**
     * Возвращает параметр события.
     * $event->getParam() используется для получения значения параметра
     * &$event->getParam('paramName') используется для получения ссылки на параметр
     * @param string $name имя параметра
     * @return mixed значение параметра, null если не определен
     */
    public function &getParam($name);

    /**
     * Возвращает параметры события.
     * @return array значение параметров
     */
    public function getParams();
}
