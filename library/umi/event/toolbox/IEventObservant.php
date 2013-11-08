<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\event\toolbox;

use umi\event\IEventManager;

/**
 * Интерфейс для поддержки событий.
 */
interface IEventObservant
{
    /**
     * Устанавливает менеджер событий
     * @param IEventManager $eventManager
     * @return self
     */
    public function setEventManager(IEventManager $eventManager);

    /**
     * Возвращает менеджер событий
     * @return IEventManager
     */
    public function getEventManager();

    /**
     * Подписаться на события другого IEventObservant
     * @param IEventObservant $target
     * @return self
     */
    public function subscribeTo(IEventObservant $target);

    /**
     * Генерирует новое событие
     * @param string $eventType тип события (уникальный строковый идентификатор)
     * @param array $params список параметров события array('paramName' => 'paramVal', 'relParam' => &$var)
     * Параметр может передаваться по ссылке.
     * @param array|null $tags тэги, с которыми происходит событиею
     * Тэги позволяют подписаться на события, которые происходят с конкретными объектами.
     * @return self
     */
    public function fireEvent($eventType, array $params = [], array $tags = null);

    /**
     * Устанавливает обработчик события
     * @param string $eventType тип события (уникальный строковый идентификатор)
     * @param callable $eventHandler callable обработчик события
     * @param array|null $tags тэги, с которыми должно происходить событие, на которое устанавливается обработчик.
     * Тэги позволяют подписаться на события, которые происходят с конкретными объектами.
     * Если тэги не указаны, обработчик будет вызван для всех объектов, поднимающих события.
     * @return self
     */
    public function bindEvent($eventType, callable $eventHandler, array $tags = null);

    /**
     * Удаляет обработчик(и) события указанного типа
     * @param string $eventType тип события (уникальный строковый идентификатор)
     * @param callable $eventHandler конкретный обработчик события, если null,
     * будут удалены все обработчики событий указанного типа
     * @return self
     */
    public function unbindEvent($eventType, callable $eventHandler = null);

    /**
     * Устанавливает локальные обработчики событий
     * @return self
     */
    public function bindLocalEvents();
}
