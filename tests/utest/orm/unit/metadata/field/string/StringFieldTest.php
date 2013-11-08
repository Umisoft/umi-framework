<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\metadata\field\string;

use umi\orm\metadata\field\string\StringField;
use utest\orm\unit\metadata\field\FieldTestCase;

class StringFieldTest extends FieldTestCase
{

    /**
     * @var StringField $field
     */
    protected $field;

    protected function setUpFixtures()
    {
        $this->field = new StringField('string');
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
            'string',
            $this->field->getDataType(),
            'Ожидается, что тип данных у поля для guid - string'
        );

        $this->assertTrue(
            $this->field->validateInputPropertyValue('1'),
            'Ожидается, что текстовое значение пройдет валидацию'
        );
        $this->assertFalse(
            $this->field->validateInputPropertyValue(1),
            'Ожидается, что не текстовое значение не пройдет валидацию'
        );
        $this->assertFalse(
            $this->field->validateInputPropertyValue(true),
            'Ожидается, что не текстовое значение не пройдет валидацию'
        );

        $this->assertNull(
            $this->field->preparePropertyValue($this->getMockObject(), null),
            'Неверная подготовка значения свойства из базы'
        );
        $this->assertSame(
            '1',
            $this->field->preparePropertyValue($this->getMockObject(), 1),
            'Неверная подготовка значения свойства из базы'
        );
    }

}
