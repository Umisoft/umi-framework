<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\form\unit\binding;

use utest\form\FormTestCase;
use utest\form\mock\binding\BindObject;

/**
 * Тесты трейта биндинга в свойства.
 */
class ObjectBindingTraitTest extends FormTestCase
{
    /**
     * @var BindObject $bindObject
     */
    public $bindObject;

    public function setUpFixtures()
    {
        $this->bindObject = new BindObject();
        $this->resolveOptionalDependencies($this->bindObject);
    }

    /**
     * Тестирование биндинга.
     */
    public function testDataBinding()
    {
        $this->assertInstanceOf(
            'utest\form\mock\binding\BindObject',
            $this->bindObject->setData(
                [
                    'email' => 'test value'
                ]
            ),
            'Ожидается, что будет возвращен $this'
        );
        $this->assertEquals('test value', $this->bindObject->email, 'Ожидается, что данные установлены в массив.');

        $this->assertEquals(
            [
                'email' => 'test value',
            ],
            $this->bindObject->getData(),
            'Ожидается, что данные не изменены.'
        );

        $this->bindObject->email = 'changed';
        $this->assertEquals(
            [
                'email' => 'changed',
            ],
            $this->bindObject->getData(),
            'Ожидается, что данные изменены.'
        );
    }
}