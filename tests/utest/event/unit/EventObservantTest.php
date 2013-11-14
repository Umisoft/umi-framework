<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\event\unit;

use umi\event\IEvent;
use umi\event\TEventObservant;
use utest\event\mock\EventObservant;
use utest\TestCase;

/**
 * Тесты компонента, поддерживающего работу с событиями
 *
 */
class EventObservantTest extends TestCase
{
    /**
     * @var EventObservant $observant1
     */
    protected $observant1;
    /**
     * @var EventObservant $observant2
     */
    protected $observant2;
    /**
     * @var EventObservant $observant3
     */
    protected $observant3;

    public function setUpFixtures()
    {
        $this->getTestToolkit()->registerToolbox(
            require(__DIR__ . '/../../../../library/umi/event/toolbox/config.php')
        );

        $this->observant1 = new EventObservant('observant1');
        $this->resolveOptionalDependencies($this->observant1);

        $this->observant2 = new EventObservant('observant2');
        $this->resolveOptionalDependencies($this->observant2);

        $this->observant3 = new EventObservant('observant3');
        $this->resolveOptionalDependencies($this->observant3);

        $this->observant1->subscribeTo($this->observant2);
        $this->observant2->subscribeTo($this->observant3);
    }

    public function testObservantMethods()
    {
        $eventManager = $this->observant1->getEventManager();

        $this->assertInstanceOf(
            'umi\event\IEventManager',
            $eventManager,
            'Ожидается, что IEventObservant::getEventManager() вернет IEventManager'
        );

        $this->assertInstanceOf(
            'umi\event\IEventObservant',
            $this->observant1->subscribeTo($this->observant2),
            'Ожидается, что IEventObservant::subscribeTo() вернет себя'
        );

        $this->assertEquals(
            $eventManager,
            $this->observant1->bindEvent(
                'test',
                function ($event) {
                }
            ),
            'Ожидается, что IEventObservant::bindEvent() вернет свой менеджер событий'
        );
        $this->assertEquals(
            $eventManager,
            $this->observant1->fireEvent('test'),
            'Ожидается, что IEventObservant::fireEvent() вернет свой менеджер событий'
        );
        $this->assertEquals(
            $eventManager,
            $this->observant1->unbindEvent('test'),
            'Ожидается, что IEventObservant::unbindEvent() вернет свой менеджер событий'
        );

    }

    public function testEventsSubscription()
    {

        $names = & $this->observant1->names;
        $this->observant1->bindEvent(
            'testEventBeforeSetName',
            function (IEvent $event) use (&$names) {
                $names[] = $event->getParam('name');
            }
        );

        $this->observant1->setName('new_observant1');
        $this->assertEquals(
            ['new_observant1'],
            $this->observant1->names,
            'Ожидается, что перехват события пополнит массив имен'
        );

        $this->observant2->setName('new_observant2');
        $this->assertEquals(
            ['new_observant1', 'new_observant2'],
            $this->observant1->names,
            'Ожидается, что перехват события у подписанного объекта пополнит массив имен'
        );

        $this->observant3->setName('new_observant3');
        $this->assertEquals(
            ['new_observant1', 'new_observant2', 'new_observant3'],
            $this->observant1->names,
            'Ожидается, что перехват события у подписанного объекта пополнит массив имен'
        );

        $this->observant1->unbindEvent('testEventBeforeSetName');
        $this->observant1->setName('observant1');
        $this->assertEquals(
            ['new_observant1', 'new_observant2', 'new_observant3'],
            $this->observant1->names,
            'Ожидается, что перехват события не произошел'
        );

    }

    public function testEventsPropagation()
    {

        $this->observant1->bindEvent(
            'testEventBeforeSetName',
            function (IEvent $event) {
                $name = & $event->getParam('name');
                $name .= '_1';
            }
        );

        $this->observant2->bindEvent(
            'testEventBeforeSetName',
            function (IEvent $event) {
                $name = & $event->getParam('name');
                $name .= '_2';
            }
        );

        $this->observant3->bindEvent(
            'testEventBeforeSetName',
            function (IEvent $event) {
                $name = & $event->getParam('name');
                $name .= '_3';
            }
        );

        $this->observant1->setName('new_observant1');
        $this->assertEquals(
            'new_observant1_1',
            $this->observant1->name,
            'Ожидается, что перехват события изменит имя по ссылке'
        );

        $this->observant2->setName('new_observant2');
        $this->assertEquals(
            'new_observant2_2_1',
            $this->observant2->name,
            'Ожидается, что все перехваты событий изменят имя по ссылке'
        );

        $this->observant3->setName('new_observant3');
        $this->assertEquals(
            'new_observant3_3_2_1',
            $this->observant3->name,
            'Ожидается, что все перехваты событий изменят имя по ссылке'
        );

    }
}

