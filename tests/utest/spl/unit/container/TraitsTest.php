<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\spl\unit\container;

use utest\TestCase;
use utest\spl\mock\container\ArrayPropertyContainer;

/**
 * Тесты трейтов для контейнера TraitsTest
 */
class TraitsTest extends TestCase
{

    public function testArrayAccess()
    {
        $array = new ArrayPropertyContainer();

        $array['test'] = 'val';
        $this->assertEquals(['test' => 'val'], $array->array, 'Ожидается, что значение будет записано');
        $this->assertEquals('val', $array['test'], 'Ожидается, что значение будет записано');
        $this->assertTrue(isset($array['test']), 'Ожидается, что значение будет записано');

        unset($array['test']);
        $this->assertEmpty($array->array, 'Ожидается, что значение будет уничтожено');
    }

    public function testPropertyAccess()
    {
        $array = new ArrayPropertyContainer();

        $array->test = 'val';
        $this->assertEquals(['test' => 'val'], $array->array, 'Ожидается, что значение будет записано');
        $this->assertEquals('val', $array->test, 'Ожидается, что значение будет записано');
        $this->assertTrue(isset($array->test), 'Ожидается, что значение будет записано');

        unset($array->test);
        $this->assertEmpty($array->array, 'Ожидается, что значение будет уничтожено');
    }
}