<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\event\unit;

use umi\event\Event;
use utest\TestCase;

/**
 * Тесты события
 *
 */
class EventTest extends TestCase
{

    public function testEvent()
    {
        $target = new \stdClass();
        $rel = 'rel';
        $param = 'param';
        $event = new Event('testEvent', $target, ['param' => $param, 'rel' => &$rel], ['tag1', 'tag2']);

        $this->assertEquals('testEvent', $event->getType(), 'Неверный тип события');
        $this->assertTrue($target === $event->getTarget(), 'Неверный объект, в котором произошло событие');
        $this->assertEquals(['tag1', 'tag2'], $event->getTags(), 'Неверные теги события');

        $this->assertEquals('param', $event->getParam('param'), 'Неверное значение параметра');
        $this->assertNull($event->getParam('no_param'), 'Ожидается null, если у события нет запрашиваемого параметра');

        $link = & $event->getParam('rel');
        $link = 'new_rel';
        $this->assertEquals('new_rel', $event->getParam('rel'), 'Параметр передан не по ссылке');

        $this->assertFalse($event->getPropagationIsStopped(), 'Ожидается, что по умолчанию событие не остановлено');
        $this->assertInstanceOf(
            'umi\event\IEvent',
            $event->stopPropagation(true),
            'Ожидается, что IEvent::stopPropagation() вернет себя'
        );
        $this->assertTrue($event->getPropagationIsStopped(), 'Ожидается, что событие было остановлено');

    }

}


