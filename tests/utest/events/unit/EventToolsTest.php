<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\events\unit;

use umi\event\toolbox\EventTools;
use utest\TestCase;

/**
 * Тесты инструментов для поддержки событий
 *
 */
class EventToolsTest extends TestCase
{

    public function testEventTools()
    {
        $eventTools = new EventTools();
        $this->resolveOptionalDependencies($eventTools);

        $this->assertInstanceOf(
            'umi\event\IEventManager',
            $eventTools->createEventManager(),
            'Ожидается, что IEventTools::getEventManager вернет IEventManager'
        );
    }
}


