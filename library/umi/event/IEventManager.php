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
 * Менеджер событий.
 */
interface IEventManager
{
    /**
     * Присоединяет менеджер событий, который так же будет уведомлен о происходящих
     * событиях текущего менеджера.
     * Можно присоединить неограниченное число менеджеров.
     * @param IEventManager $eventsManager
     * @return self
     */
    public function attach(IEventManager $eventsManager);

    /**
     * Вызывает все обработчики, зарегистрированные на событие
     * @param string $eventType тип события (уникальный строковый идентификатор)
     * @param mixed $target объект, в котором произошло событие
     * @param array $params список параметров события array('paramName' => 'paramVal', 'relParam' => &$var)
     * Параметр может передаваться по ссылке.
     * @param array $tags тэги, с которыми происходит событиею
     * Тэги позволяют подписаться на события, которые происходят с конкретными объектами.
     * @return self
     */
    public function fireEvent($eventType, $target, array $params = [], array $tags = []);

    /**
     * Распространяет событие по обработчикам, а так же по вложенным менеджерам
     * @param string $eventType тип события
     * @param IEvent $event событие
     * @return self
     */
    public function propagateEvent($eventType, IEvent $event);

    /**
     * Устанавливает обработчик события.
     * Тэги позволяют подписаться на события, которые происходят с конкретными объектами.
     * Если тэги не указаны, обработчик будет вызван для всех объектов, поднимающих события.
     * @param string $eventType тип события (уникальный строковый идентификатор)
     * @param callable $eventHandler callable обработчик события
     * @param array $tags тэги, с которыми должно происходить событие, на которое устанавливается обработчик
     * @return self
     */
    public function bindEvent($eventType, callable $eventHandler, array $tags = []);

    /**
     * Удаляет обработчик(и) события указанного типа
     * @param string $eventType тип события (уникальный строковый идентификатор)
     * @param callable $eventHandler конкретный обработчик события, если null,
     * будут удалены все обработчики событий указанного типа
     * @return self
     */
    public function unbindEvent($eventType, callable $eventHandler = null);

    /**
     * Удаляет все обработчики событий всех типов
     * @return self
     */
    public function unbindEvents();
}
