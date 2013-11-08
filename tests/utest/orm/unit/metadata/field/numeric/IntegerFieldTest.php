<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\metadata\field\numeric;

use umi\orm\metadata\field\numeric\IntegerField;
use utest\orm\unit\metadata\field\FieldTestCase;

/**
 * Тест для целочисленного поля.
 */
class IntegerFieldTest extends FieldTestCase
{

    /**
     * @var IntegerField $field
     */
    protected $field;

    protected function setUpFixtures()
    {
        $this->field = new IntegerField('integer');
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
        $this->assertEquals(
            'integer',
            $this->field->getDataType(),
            'Ожидается, что тип данных у поля IntegerField - integer'
        );

        $this->assertTrue(
            $this->field->validateInputPropertyValue(1),
            'Ожидается, что целочисленное значение пройдет валидацию'
        );
        $this->assertFalse(
            $this->field->validateInputPropertyValue('1'),
            'Ожидается, что нецелочисленное значение не пройдет валидацию'
        );
        $this->assertFalse(
            $this->field->validateInputPropertyValue(1.2),
            'Ожидается, что нецелочисленное значение не пройдет валидацию'
        );

        $this->assertNull(
            $this->field->preparePropertyValue($this->getMockObject(), null),
            'Неверная подготовка значения свойства из базы'
        );
        $this->assertEquals(
            1,
            $this->field->preparePropertyValue($this->getMockObject(), 1),
            'Неверная подготовка значения свойства из базы'
        );
        $this->assertEquals(
            1,
            $this->field->preparePropertyValue($this->getMockObject(), '1'),
            'Неверная подготовка значения свойства из базы'
        );
        $this->assertEquals(
            1,
            $this->field->preparePropertyValue($this->getMockObject(), 1.5),
            'Неверная подготовка значения свойства из базы'
        );
        $this->assertEquals(
            1,
            $this->field->preparePropertyValue($this->getMockObject(), '1.5'),
            'Неверная подготовка значения свойства из базы'
        );
        $this->assertEquals(
            1,
            $this->field->preparePropertyValue($this->getMockObject(), '1true'),
            'Неверная подготовка значения свойства из базы'
        );
        $this->assertEquals(
            0,
            $this->field->preparePropertyValue($this->getMockObject(), 'true'),
            'Неверная подготовка значения свойства из базы'
        );
        $this->assertEquals(
            -1,
            $this->field->preparePropertyValue($this->getMockObject(), -1),
            'Неверная подготовка значения свойства из базы'
        );
        $this->assertEquals(
            -1,
            $this->field->preparePropertyValue($this->getMockObject(), '-1'),
            'Неверная подготовка значения свойства из базы'
        );

    }

}
