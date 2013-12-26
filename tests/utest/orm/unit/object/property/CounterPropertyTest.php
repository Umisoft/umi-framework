<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\object\property;

use umi\orm\metadata\field\special\CounterField;
use umi\orm\object\IObject;
use umi\orm\object\property\calculable\CounterProperty;
use utest\orm\ORMTestCase;

/**
 * Тесты свойства-счетчика
 */
class CounterPropertyTest extends ORMTestCase
{

    public function testCounterProperty()
    {
        /**
         * @var IObject $object
         */
        $object = $this->getMock('umi\orm\object\Object', [], [], '', false);

        $counterField = new CounterField('counter');
        $counterProperty = new CounterProperty($object, $counterField);

        $counterProperty->setInitialValue(0);
        $counterProperty->increment();
        $counterProperty->increment();
        $counterProperty->increment();

        $this->assertEquals(3, $counterProperty->getValue());

        $counterProperty->decrement();
        $counterProperty->decrement();

        $this->assertEquals(1, $counterProperty->getValue());

    }
}
