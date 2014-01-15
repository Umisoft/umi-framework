<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */
namespace umi\event\toolbox\factory;

use umi\event\IEventFactory;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика событий и менеджеров событий
 */
class EventFactory implements IEventFactory, IFactory
{

    use TFactory;

    /**
     * @var string $eventManagerClass класс для создания менеджера событий
     */
    public $eventManagerClass = 'umi\event\EventManager';
    /**
     * @var string $eventClass класс события
     */
    public $eventClass = '\umi\event\Event';

    /**
     * {@inheritdoc}
     */
    public function createEventManager()
    {
        return $this->getPrototype(
                $this->eventManagerClass,
                ['umi\event\IEventManager']
            )
            ->createInstance([$this]);
    }

    /**
     * {@inheritdoc}
     */
    public function createEvent($type, $target, array $params = [], array $tags = [])
    {
        return $this->getPrototype(
                $this->eventClass,
                ['umi\event\IEvent']
            )
            ->createInstance([$type, $target, $params, $tags]);
    }
}

 