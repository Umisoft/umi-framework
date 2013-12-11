<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\metadata\field;

use umi\i18n\ILocalesAware;
use umi\i18n\TLocalesAware;
use umi\orm\metadata\field\BaseField;
use umi\orm\metadata\field\ILocalizableField;
use umi\orm\metadata\field\TLocalizableField;
use umi\orm\object\IObject;

/**
 * Тест локализуемого поля.
 */
class LocalizableFieldTest extends FieldTestCase
{

    /**
     * {@inheritdoc}
     */
    protected function getField()
    {
        return new MockLocalizableField('mock');
    }

    public function testEmptyLocalesConfig()
    {
        $field = new MockLocalizableField('mock', ['localizations' => []]);
        $this->assertFalse(
            $field->getIsLocalized(),
            'Ожидается, что локализуемое поле не локализовано, если у него не указаны локали'
        );
        $this->assertEmpty(
            $field->getLocalizations(),
            'Если в конфиге локализации локали не указано, ожидается, что никакие локали не будут возвращены'
        );
    }

    public function testWrongLocalesConfig()
    {
        $e = null;
        try {
            new MockLocalizableField('mock', ['localizations' => 'wrongLocalizationsConfig']);
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\UnexpectedValueException',
            $e,
            'Ожидается исключение при попытке создать поле с неверной конфигурацией локалей'
        );
    }

    public function testLocalesConfig()
    {
        $localizations = [
            'ru' => [
                'columnName'   => 'field_ru',
                'defaultValue' => 'default_ru'
            ],
            'en' => [
                'columnName'   => 'field_en',
                'defaultValue' => 'default_en'
            ]
        ];

        $field = new MockLocalizableField(
            'mock',
            [
                'columnName'    => 'field_ru',
                'defaultValue'  => 'default_ru',
                'localizations' => $localizations
            ]
        );
        $this->assertTrue(
            $field->getIsLocalized(),
            'Ожидается, что локализуемое поле локализовано, когда у него есть список локалей'
        );
        $this->assertEquals($localizations, $field->getLocalizations(), 'Неверно прочитан конфиг локализаций');
        $this->assertTrue($field->hasLocale('ru'), 'Ожидается, что локаль ru есть у поля');
        $this->assertFalse($field->hasLocale('de'), 'Ожидается, что локаль de отсутствует у поля');

        $this->assertEquals(
            'field_ru',
            $field->getLocaleColumnName(),
            'Ожидается, что при запросе локализованного столбца без указания локали вернется столбец по умолчанию'
        );
        $this->assertEquals(
            'default_ru',
            $field->getLocaleDefaultValue(),
            'Ожидается, что при запросе локализованного дефолтного значения без указания локали '
            . 'вернется значение по умолчанию'
        );

        $this->assertEquals(
            'field_en',
            $field->getLocaleColumnName('en'),
            'Ожидается, что при запросе локализованного столбца вернется столбец для указанной локали'
        );
        $this->assertEquals(
            'default_en',
            $field->getLocaleDefaultValue('en'),
            'Ожидается, что при запросе локализованного дефолтного значения вернется значениедля указанной локали'
        );

        $e = null;
        try {
            $field->getLocaleColumnName('it');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при попытке получить имя колонки для несуществующей локали'
        );

        $e = null;
        try {
            $field->getLocaleDefaultValue('it');
        } catch (\Exception $e) {
        }
        $this->assertInstanceOf(
            'umi\orm\exception\NonexistentEntityException',
            $e,
            'Ожидается исключение при попытке получить значение по умолчанию для несуществующей локали'
        );
    }
}

class MockLocalizableField extends BaseField implements ILocalizableField, ILocalesAware
{

    use TLocalizableField;
    use TLocalesAware;

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
