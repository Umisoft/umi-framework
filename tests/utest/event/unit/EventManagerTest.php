<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\event\unit;

use umi\event\EventManager;
use umi\event\IEvent;
use umi\event\IEventFactory;
use umi\event\toolbox\factory\EventFactory;
use utest\event\EventTestCase;

/**
 * Тесты менеджера событий
 *
 */
class EventManagerTest extends EventTestCase
{

    /**
     * @var IEventFactory $eventFactory
     */
    protected $eventFactory;

    protected function setUpFixtures() {
        $this->eventFactory = new EventFactory();
        $this->resolveOptionalDependencies($this->eventFactory);
    }
    
    /**
     * @param IEvent $event
     */
    public function eventHandler(IEvent $event)
    {
        $events = & $event->getParam('events');
        $events[] = 'Second bind';
    }

    public function testEventManager()
    {

        $eventManager = new EventManager($this->eventFactory);
        $target = new \stdClass();
        $events = [];

        $this->assertInstanceOf(
            'umi\event\IEventManager',
            $eventManager->fireEvent('testEvent', $target),
            'Ожидается, что IEventManager::fireEvent() вернет себя, даже если нет обработчиков'
        );
        $this->assertInstanceOf(
            'umi\event\IEventManager',
            $eventManager->unbindEvent('testEvent'),
            'Ожидается, что IEventManager::unbindEvent() вернет себя, даже если нет обработчиков'
        );

        $eventHandler = function () use (&$events) {
            $events[] = 'First bind';
        };

        $this->assertInstanceOf(
            'umi\event\IEventManager',
            $eventManager->bindEvent('testEvent', $eventHandler),
            'Ожидается, что IEventManager::bindEvent() вернет себя'
        );
        $eventManager->bindEvent('testEvent', array($this, 'eventHandler'));

        $childEventManager = new EventManager($this->eventFactory);
        $this->assertInstanceOf(
            'umi\event\IEventManager',
            $eventManager->attach($childEventManager),
            'Ожидается, что IEventManager::attach() вернет себя'
        );

        $childEventManager->bindEvent(
            'testEvent',
            function () use (&$events) {
                $events[] = 'Child first bind';
            }
        );
        $childEventManager->bindEvent(
            'testEvent',
            function () use (&$events) {
                $events[] = 'Child second bind';
            }
        );

        $events = [];
        $eventManager->fireEvent('testEvent', $target, ['events' => &$events]);
        $this->assertEquals(
            ['Second bind', 'First bind', 'Child second bind', 'Child first bind'],
            $events,
            'Ожидается, что были вызваны все обработчики'
        );

        $this->assertInstanceOf(
            'umi\event\IEventManager',
            $childEventManager->unbindEvent('testEvent'),
            'Ожидается, что IEventManager::unbindEvent() вернет себя'
        );

        $events = [];
        $eventManager->fireEvent('testEvent', $target, ['events' => &$events]);
        $this->assertEquals(
            ['Second bind', 'First bind'],
            $events,
            'Ожидается, что дочерние обработчики не были вызваны'
        );

        $this->assertInstanceOf(
            'umi\event\IEventManager',
            $eventManager->unbindEvent('testEvent', $eventHandler),
            'Ожидается, что IEventManager::unbindEvent() вернет себя'
        );

        $events = [];
        $eventManager->fireEvent('testEvent', $target, ['events' => &$events]);
        $this->assertEquals(['Second bind'], $events, 'Ожидается, что один из обработчиков был сброшен');

        $this->assertInstanceOf(
            'umi\event\IEventManager',
            $eventManager->unbindEvents(),
            'Ожидается, что IEventManager::unbindEvents() вернет себя'
        );

        $events = [];
        $eventManager->fireEvent('testEvent', $target, ['events' => &$events]);
        $this->assertEquals([], $events, 'Ожидается, что все обработчики были сброшены');

    }

    public function testStopPropagation()
    {
        $eventManager = new EventManager($this->eventFactory);
        $childEventManager = new EventManager($this->eventFactory);

        $eventManager->attach($childEventManager);

        $target = new \stdClass();
        $events = [];

        $childEventManager->bindEvent(
            'testEvent',
            function () use (&$events) {
                $events[] = 'Child first bind';
            }
        );
        $childEventManager->bindEvent(
            'testEvent',
            function (IEvent $event) use (&$events) {
                $events[] = 'Child second bind';
                $event->stopPropagation(true);
            }
        );

        $eventManager->fireEvent('testEvent', $target);
        $this->assertEquals(
            ['Child second bind'],
            $events,
            'Ожидается, что все ранее назначенные обработчики не были вызваны из-за остановки события'
        );

        $eventManager->bindEvent(
            'testEvent',
            function (IEvent $event) use (&$events) {
                $events[] = 'First bind';
                $event->stopPropagation(true);
            }
        );

        $events = [];
        $eventManager->fireEvent('testEvent', $target);
        $this->assertEquals(
            ['First bind'],
            $events,
            'Ожидается, что все ранее дочерние обработчики не были вызваны из-за остановки события'
        );

    }

    public function testTaggedEvents()
    {
        $eventManager = new EventManager($this->eventFactory);

        $target = new \stdClass();
        $events = [];

        $eventManager->bindEvent(
            'taggedEvent',
            function () use (&$events) {
                $events[] = 'Вызван обработчик события, тэги для которого не указаны.';
            }
        );

        $eventManager->bindEvent(
            'taggedEvent',
            function () use (&$events) {
                $events[] = 'Вызван обработчик события, у которого указан tag1.';
            },
            ['tag1']
        );

        $eventManager->bindEvent(
            'taggedEvent',
            function () use (&$events) {
                $events[] = 'Вызван обработчик события, у которого указан tag1 и tag2.';
            },
            ['tag1', 'tag2']
        );

        $eventManager->bindEvent(
            'taggedEvent',
            function () use (&$events) {
                $events[] = 'Вызван обработчик события, у которого указан tag1 и tag3.';
            },
            ['tag1', 'tag3']
        );

        // поднимаем события без тэгов
        $eventManager->fireEvent('taggedEvent', $target);
        $this->assertEquals(
            [
                'Вызван обработчик события, тэги для которого не указаны.'
            ],
            $events,
            'Не корректно работают теггируемые события.'
        );

        // поднимаем событие с тэгом tag1
        $events = [];
        $eventManager->fireEvent('taggedEvent', $target, [], ['tag1']);
        $this->assertEquals(
            [
                'Вызван обработчик события, у которого указан tag1.',
                'Вызван обработчик события, тэги для которого не указаны.'
            ],
            $events,
            'Не корректно работают теггируемые события.'
        );

        // поднимаем событие с тэгом tag1, tag2
        $events = [];
        $eventManager->fireEvent('taggedEvent', $target, [], ['tag1', 'tag2']);

        $this->assertEquals(
            [
                'Вызван обработчик события, у которого указан tag1 и tag2.',
                'Вызван обработчик события, у которого указан tag1.',
                'Вызван обработчик события, тэги для которого не указаны.'
            ],
            $events,
            'Не корректно работают теггируемые события.'
        );

        // поднимаем событие с тэгом tag3
        $events = [];
        $eventManager->fireEvent('taggedEvent', $target, [], ['tag3']);
        $this->assertEquals(
            [
                'Вызван обработчик события, тэги для которого не указаны.'
            ],
            $events,
            'Не корректно работают теггируемые события.'
        );

    }

}


