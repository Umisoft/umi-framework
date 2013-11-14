<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\orm\unit\toolbox\factory;

use umi\orm\metadata\field\IField;
use umi\orm\metadata\field\numeric\IntegerField;
use umi\orm\metadata\field\special\CounterField;
use umi\orm\metadata\field\special\MaterializedPathField;
use umi\orm\metadata\field\string\StringField;
use umi\orm\object\IObject;
use umi\orm\object\property\ILocalizedProperty;
use umi\orm\toolbox\factory\PropertyFactory;
use utest\orm\ORMTestCase;

/**
 * Тесты фабрики свойств
 */
class PropertyFactoryTest extends ORMTestCase
{

    public function testPropertyFactory()
    {

        /**
         * @var IObject $object
         */
        $object = $this->getMock('umi\orm\object\Object', [], [], '', false);
        /**
         * @var IField $commonField
         */
        $commonField = new IntegerField('integer');
        $calculableField = new MaterializedPathField('mpath');
        $counterField = new CounterField('counter');
        $localizedField = new StringField('string', [
            'localizations' => [
                'ru' => ['columnName' => 'field_ru'],
                'en' => ['columnName' => 'field_en']
            ]
        ]);

        $propertyFactory = new PropertyFactory();
        $this->resolveOptionalDependencies($propertyFactory);

        $commonProperty = $propertyFactory->createProperty($object, $commonField);
        $this->assertInstanceOf(
            'umi\orm\object\property\IProperty',
            $commonProperty,
            'Неверный интерфейс у обычного свойства'
        );

        /**
         * @var ILocalizedProperty $localizedProperty
         */
        $localizedProperty = $propertyFactory->createProperty($object, $localizedField, 'ru');
        $this->assertInstanceOf(
            'umi\orm\object\property\ILocalizedProperty',
            $localizedProperty,
            'Ожидается, что для локализованного поля будет создано локализованное свойство'
        );
        $this->assertEquals('ru', $localizedProperty->getLocaleId());

        $calculableProperty = $propertyFactory->createProperty($object, $calculableField);
        $this->assertInstanceOf(
            'umi\orm\object\property\ICalculableProperty',
            $calculableProperty,
            'Ожидается, что для вычисляемого поля будет создано вычисляемое свойство'
        );

        $counterProperty = $propertyFactory->createProperty($object, $counterField);
        $this->assertInstanceOf(
            'umi\orm\object\property\ICounterProperty',
            $counterProperty,
            'Ожидается, что для поля-счетчика будет создано свойство-счетчик'
        );
    }
}
