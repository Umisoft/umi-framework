<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\metadata\field\special;

use umi\orm\metadata\field\special\GuidField;
use umi\orm\object\IObject;
use utest\orm\unit\metadata\field\FieldTestCase;

/**
 * Тест поля для хранения GUID.
 */
class GuidFieldTest extends FieldTestCase
{

    /**
     * @var GuidField $field
     */
    protected $field;

    protected function setUpFixtures()
    {
        $this->field = new GuidField(IObject::FIELD_GUID);
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

        $e = null;
        try {
            $this->field->validateInputPropertyValue('');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\InvalidArgumentException',
            $e,
            'Ожидается, что пустое значение не пройдет валидацию поля guid'
        );

        $e = null;
        try {
            $this->field->validateInputPropertyValue('9ee6745f-f40d-46d8-8043-d959');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\InvalidArgumentException',
            $e,
            'Ожидается, что guid в неверном формате не пройдет валидацию поля guid'
        );

        $this->assertTrue(
            $this->field->validateInputPropertyValue('9ee6745f-f40d-46d8-8043-d959594628ce'),
            'Ожидается, что guid в верном формате пройдет валидацию'
        );
    }
}
