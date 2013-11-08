<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\form\unit\element;

use umi\filter\IFilterFactory;
use umi\form\exception\InvalidArgumentException;
use umi\validation\IValidatorFactory;

/**
 * Базовые тесты элементов с вариантами значений.
 */
abstract class BaseMultiElementTest extends BaseElementTest
{

    /**
     * Базовые тесты.
     */
    public function testBasic()
    {
        $element = $this->getElement('testElement', ['data-id' => 'id'], ['choices' => []]);
        $element->setLabel('My element');

        $this->assertEquals('My element', $element->getLabel(), 'Ожидается, что лейбл будет установлен.');
        $this->assertArrayHasKey('data-id', $element->getAttributes(), 'Ожидается, что аттрибуты будут установлены.');
        $this->assertArrayHasKey(
            'name',
            $element->getAttributes(),
            'Ожидается, что имя будет установлено как аттрибут.'
        );

        $this->assertEquals('testElement', $element->getName(), 'Ожидается, что имя элемента будет установлено.');
    }

    /**
     * Тест установки/получения значений элементов.
     */
    public function testValues()
    {
        $element = $this->getElement(
            'testElement',
            [],
            [
                'choices' => ['test1', 'test2', 'test3']
            ]
        );

        $this->assertSame($element, $element->setValue(1), 'Ожидается, что будет возвращен $this');
        $this->assertEquals(1, $element->getValue(), 'Ожидается, что значение будет установлено.');
    }

    /**
     * @test исключения, при попытке установить значение не из списка.
     * @expectedException InvalidArgumentException
     */
    public function setWrongValue()
    {
        $element = $this->getElement(
            'testElement',
            [],
            [
                'choices' => ['test1', 'test2', 'test3']
            ]
        );

        $element->setValue(12);
    }

    public function testValidators()
    {
        $e = $this->getElement(
            'test',
            [],
            [
                'choices' => [
                    null => 'test value'
                ]
            ]
        );

        $this->assertInstanceOf(
            'umi\validation\IValidatorCollection',
            $e->getValidators(),
            'Ожидается, что цепочку валидаторов можно получить у любого элемента.'
        );

        $this->assertSame(
            $e,
            $e->setValidators($this->getValidatorCollection([IValidatorFactory::TYPE_REQUIRED => []])),
            'Ожидается, что будет получен $this'
        );

        $this->assertTrue($e->isValid());
        $e->setValue('');
        $this->assertFalse($e->isValid());
    }

    public function testFilters()
    {
        $e = $this->getElement(
            'test',
            [],
            [
                'choices' => [
                    1 => 'test value'
                ]
            ]
        );

        $this->assertInstanceOf(
            'umi\filter\IFilterCollection',
            $e->getFilters(),
            'Ожидается, что цепочку фильтров можно получить у любого элемента.'
        );

        $this->assertSame(
            $e,
            $e->setFilters($this->getFilterCollection([IFilterFactory::TYPE_INT => []])),
            'Ожидается, что будет получен $this'
        );

        $e->setValue('1aa');

        $this->assertEquals(1, $e->getValue());
    }
}