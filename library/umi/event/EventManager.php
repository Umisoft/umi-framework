<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\event;

use InvalidArgumentException;
use SplObjectStorage;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalizable;

/**
 * Менеджер событий.
 */
class EventManager implements IEventManager, ILocalizable
{

    use TLocalizable;

    /**
     * @var string $eventClass класс события
     */
    public $eventClass = '\umi\event\Event';
    /**
     * @var array $eventHandlers список зарегистрированный обработчиков событий
     */
    protected $eventHandlers = [];
    /**
     * @var SplObjectStorage|IEventManager[] $attachedManagers список менеджеров
     */
    protected $attachedManagers;

    /**
     * {@inheritdoc}
     */
    public function attach(IEventManager $eventsManager)
    {
        if (!$this->attachedManagers) {
            $this->attachedManagers = new SplObjectStorage;
        }
        $this->attachedManagers->attach($eventsManager);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function propagateEvent($eventType, IEvent $event)
    {
        if (!isset($this->eventHandlers[$eventType]) && is_null($this->attachedManagers)) {
            return $this;
        }

        // current handlers propagation
        if (isset($this->eventHandlers[$eventType])) {
            foreach ($this->eventHandlers[$eventType] as $handlerInfo) {
                list ($eventHandler, $tags) = $handlerInfo;

                $handlerTagsCount = count($tags);
                if ($handlerTagsCount) {
                    if (!count($event->getTags())) {
                        continue;
                    }
                    if (count(array_intersect($tags, $event->getTags())) != $handlerTagsCount) {
                        continue;
                    }
                }

                call_user_func($eventHandler, $event);

                if ($event->getPropagationIsStopped()) {
                    return $this;
                }
            }
        }

        // attached managers propagation
        if ($this->attachedManagers) {
            foreach ($this->attachedManagers as $attachedManager) {
                $attachedManager->propagateEvent($eventType, $event);
                if ($event->getPropagationIsStopped()) {
                    return $this;
                }
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function fireEvent($eventType, $target, array $params = [], array $tags = [])
    {
        if (!isset($this->eventHandlers[$eventType]) && is_null($this->attachedManagers)) {
            return $this;
        }
        /**
         * @var IEvent $event
         */
        $event = new $this->eventClass($eventType, $target, $params, $tags);

        return $this->propagateEvent($eventType, $event);
    }

    /**
     * {@inheritdoc}
     */
    public function bindEvent($eventType, callable $eventHandler, array $tags = [])
    {
        if (!is_callable($eventHandler)) {
            throw new \InvalidArgumentException($this->translate(
                'Cannot bind event "{eventType}". Event handler should be callable.',
                ['eventType' => $eventType]
            ));
        }
        if (!isset($this->eventHandlers[$eventType])) {
            $this->eventHandlers[$eventType] = [];
        }
        array_unshift($this->eventHandlers[$eventType], [$eventHandler, $tags]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function unbindEvent($eventType, callable $eventHandler = null)
    {
        if (!isset($this->eventHandlers[$eventType])) {
            return $this;
        }

        if (is_null($eventHandler)) {
            unset($this->eventHandlers[$eventType]);

            return $this;
        }

        foreach ($this->eventHandlers[$eventType] as $order => $handlerInfo) {
            list($nextHandler) = $handlerInfo;
            if ($eventHandler === $nextHandler) {
                unset($this->eventHandlers[$eventType][$order]);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function unbindEvents()
    {
        $this->eventHandlers = [];

        return $this;
    }
}
