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
 * Фабрика событий и менеджеров событий
 */
class EventFactory implements IEventFactory {
    /**
     * {@inheritdoc}
     */
    public function createEventManager()
    {
        return new EventManager($this);
    }

    /**
     * {@inheritdoc}
     */
    public function createEvent($type, $target, array $params = [], array $tags = [])
    {
        return new Event($type, $target, $params, $tags);
    }
}
 