<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\metadata\field;

use umi\orm\metadata\field\BaseField;
use umi\orm\object\IObject;

/**
 * Тест методов базового класса BaseField.
 */
class BaseFieldTest extends FieldTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getField()
    {
        return new MockField('mock');
    }

    public function testDefaultConfig()
    {

        $field = new MockField('mock');

        $this->assertEquals('mock', $field->getName(), 'Неверное имя поля');
        $this->assertEquals(
            $field->getName(),
            $field->getColumnName(),
            'Ожидается, что по умолчанию имя колонки совпадает с именем поля'
        );
        $this->assertTrue($field->getIsVisible(), 'Ожидается, что по умолчанию поле видимое');
        $this->assertFalse($field->getIsReadOnly(), 'Ожидается, что по умолчанию поле доступно на изменение');
        $this->assertNull($field->getDefaultValue(), 'Ожидается, что по умолчанию дефолное значение у поля null');
        $this->assertNull($field->getAccessor(), 'Ожидается, что по умолчанию метод доступа к значению не установлен');
        $this->assertNull($field->getMutator(), 'Ожидается, что по умолчанию метод изменения значения не установлен');
        $this->assertEmpty($field->getValidators(), 'Ожидается, что по умолчанию валидаторы для поля не установлены');
        $this->assertEmpty($field->getFilters(), 'Ожидается, что по умолчанию фильтры для поля не установлены');

    }

    public function testConfig()
    {

        $field = new MockField(
            'mock',
            [
                'columnName' => 'column_for_field',
                'visible' => 0,
                'locked' => 1,
                'readOnly' => 1,
                'defaultValue' => 10,
                'accessor' => 'getField',
                'mutator' => 'setField',
                'validators' => [
                    'fieldValidator' => [
                        'from' => 5,
                        'to'   => 15
                    ]
                ],
                'filters' => [
                    'fieldFilter' => [
                        'from' => 5,
                        'to'   => 15
                    ]
                ]
            ]
        );

        $this->assertEquals('column_for_field', $field->getColumnName(), 'Неверно определено имя колонки бд для поля');
        $this->assertFalse($field->getIsVisible(), 'Неверно прочитана видимость поля');
        $this->assertTrue($field->getIsReadOnly(), 'Неверно прочитана возможность редактировать поле');
        $this->assertSame(10, $field->getDefaultValue(), 'Неверно прочитано дефолтное значение поля');
        $this->assertEquals('getField', $field->getAccessor(), 'Неверно прочитан метод доступа к значению поля');
        $this->assertEquals('setField', $field->getMutator(), 'Неверно прочитан метод модификации значения поля');
        $this->assertEquals(
            ['fieldValidator' => ['from' => 5, 'to' => 15]],
            $field->getValidators(),
            'Неверно прочитаны валидаторы поля'
        );
        $this->assertEquals(
            ['fieldFilter' => ['from' => 5, 'to' => 15]],
            $field->getFilters(),
            'Неверно прочитаны фильтры поля'
        );
    }

    public function testWrongConfig()
    {

        $e = null;
        try {
            new MockField('mock', ['validators' => 'WrongValidatorsConfig']);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение при попытке выставить неверную конфигурацию валидаторов'
        );

        $e = null;
        try {
            new MockField('mock', ['filters' => 'WrongFiltersConfig']);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение при попытке выставить неверную конфигурацию фильтров'
        );
    }
}

class MockField extends BaseField
{
    /**
     * {@inheritdoc}
     */
    public function getDataType()
    {
        return 'string';
    }

    /**
     * {@inheritdoc}
     */
    public function validateInputPropertyValue($propertyValue)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function preparePropertyValue(IObject $object, $internalDbValue)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDbValue(IObject $object, $propertyValue)
    {
        return null;
    }
}
