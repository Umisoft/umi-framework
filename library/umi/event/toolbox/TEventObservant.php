<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\event\toolbox;

use umi\event\EventManager;
use umi\event\IEventManager;

/**
 * Трейт для поддержки событий.
 */
trait TEventObservant
{
    /**
     * @var IEventManager $_eventManager локальный менеджер событий
     */
    protected $_eventManager;

    /**
     * Устанавливает менеджер событий
     * @param IEventManager $eventManager
     * @return $this
     */
    public function setEventManager(IEventManager $eventManager)
    {
        $this->_eventManager = $eventManager;

        return $this;
    }

    /**
     * Возвращает менеджер событий
     * @internal
     * @return IEventManager
     */
    public function getEventManager()
    {
        if (!$this->_eventManager) {
            $this->_eventManager = new EventManager();
        }

        return $this->_eventManager;
    }

    /**
     * Подписаться на события другого IEventObservant
     * @param IEventObservant $target
     * @return $this
     */
    public function subscribeTo(IEventObservant $target)
    {
        $target->getEventManager()
            ->attach($this->getEventManager());

        return $this;
    }

    /**
     * Генерирует новое событие
     * @param string $eventType тип события (уникальный строковый идентификатор)
     * @param array $params список параметров события array('paramName' => 'paramVal', 'relParam' => &$var)
     * Параметр может передаваться по ссылке.
     * @param array $tags тэги, с которыми происходит событиею
     * Тэги позволяют подписаться на события, которые происходят с конкретными объектами.
     * @return $this
     */
    public function fireEvent($eventType, array $params = [], array $tags = [])
    {
        return $this->getEventManager()
            ->fireEvent($eventType, $this, $params, $tags);
    }

    /**
     * Устанавливает обработчик события.
     * Тэги позволяют подписаться на события, которые происходят с конкретными объектами.
     * Если тэги не указаны, обработчик будет вызван для всех объектов, поднимающих события.
     * @param string $eventType тип события (уникальный строковый идентификатор)
     * @param callable $eventHandler callable обработчик события
     * @param array $tags тэги, с которыми должно происходить событие, на которое устанавливается обработчик
     * @return $this
     */
    public function bindEvent($eventType, callable $eventHandler, array $tags = [])
    {
        return $this->getEventManager()
            ->bindEvent($eventType, $eventHandler, $tags);
    }

    /**
     * Удаляет обработчик(и) события указанного типа
     * @param string $eventType тип события (уникальный строковый идентификатор)
     * @param callable $eventHandler конкретный обработчик события, если null,
     * будут удалены все обработчики событий указанного типа
     * @return $this
     */
    public function unbindEvent($eventType, callable $eventHandler = null)
    {
        return $this->getEventManager()
            ->unbindEvent($eventType, $eventHandler);
    }

    /**
     * Устанавливает локальные обработчики событий
     * @return $this
     */
    public function bindLocalEvents()
    {
        return $this;
    }
}
