<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\metadata\field\numeric;

use umi\orm\metadata\field\numeric\BoolField;
use utest\orm\unit\metadata\field\FieldTestCase;

/**
 * Тест поля типа "булево"
 */
class BoolFieldTest extends FieldTestCase
{

    /**
     * @var BoolField $field
     */
    protected $field;

    protected function setUpFixtures()
    {
        $this->field = new BoolField('boolean');
    }

    /**
     * {@inheritdoc}
     */
    protected function getField()
    {
        return $this->field;
    }

    public function testMethods()
    {
        $this->assertEquals('bool', $this->field->getDataType(), 'Ожидается, что тип данных у поля BoolField - bool');

        $this->assertTrue(
            $this->field->validateInputPropertyValue(true),
            'Ожидается, что булево значение пройдет валидацию'
        );
        $this->assertTrue(
            $this->field->validateInputPropertyValue(false),
            'Ожидается, что булево значение пройдет валидацию'
        );
        $this->assertFalse(
            $this->field->validateInputPropertyValue(0),
            'Ожидается, что не булево значение не пройдет валидацию'
        );

        $this->assertNull(
            $this->field->preparePropertyValue($this->getMockObject(), null),
            'Неверная подготовка значения свойства из базы'
        );
        $this->assertTrue(
            $this->field->preparePropertyValue($this->getMockObject(), 1),
            'Неверная подготовка значения свойства из базы'
        );
        $this->assertTrue(
            $this->field->preparePropertyValue($this->getMockObject(), '1'),
            'Неверная подготовка значения свойства из базы'
        );
        $this->assertTrue(
            $this->field->preparePropertyValue($this->getMockObject(), 'false'),
            'Неверная подготовка значения свойства из базы'
        );
        $this->assertTrue(
            $this->field->preparePropertyValue($this->getMockObject(), true),
            'Неверная подготовка значения свойства из базы'
        );

        $this->assertFalse(
            $this->field->preparePropertyValue($this->getMockObject(), 0),
            'Неверная подготовка значения свойства из базы'
        );
        $this->assertFalse(
            $this->field->preparePropertyValue($this->getMockObject(), '0'),
            'Неверная подготовка значения свойства из базы'
        );
        $this->assertFalse(
            $this->field->preparePropertyValue($this->getMockObject(), false),
            'Неверная подготовка значения свойства из базы'
        );

    }

}
