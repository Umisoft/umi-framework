<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\toolbox\factory;

use umi\orm\metadata\field\ICalculableField;
use umi\orm\metadata\field\IField;
use umi\orm\metadata\field\ILocalizableField;
use umi\orm\metadata\field\special\CounterField;
use umi\orm\object\IObject;
use umi\orm\object\property\ICalculableProperty;
use umi\orm\object\property\ICounterProperty;
use umi\orm\object\property\ILocalizedProperty;
use umi\orm\object\property\IProperty;
use umi\orm\object\property\IPropertyFactory;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика свойств объекта.
 */
class PropertyFactory implements IPropertyFactory, IFactory
{

    use TFactory;

    /**
     * @var string $defaultClass класс свойства по умолчанию
     */
    public $defaultPropertyClass = 'umi\orm\object\property\Property';
    /**
     * @var string $defaultCalculablePropertyClass класс свойства с вычисляемым значением
     */
    public $defaultCalculablePropertyClass = 'umi\orm\object\property\CalculableProperty';
    /**
     * @var string $defaultLocalizedPropertyClass класс локализованного свойства
     */
    public $defaultLocalizedPropertyClass = 'umi\orm\object\property\LocalizedProperty';
    /**
     * @var string $defaultCounterPropertyClass класс свойства-счетчика
     */
    public $defaultCounterPropertyClass = 'umi\orm\object\property\CounterProperty';

    /**
     * {@inheritdoc}
     */
    public function createProperty(IObject $object, IField $field, $localeId = null)
    {

        switch (true) {
            case ($field instanceof CounterField):
            {
                return $this->createCounterProperty($object, $field);
            }
            case ($field instanceof ICalculableField):
            {
                return $this->createCalculableProperty($object, $field);
            }
            case ($field instanceof ILocalizableField && $field->getIsLocalized()):
            {
                return $this->createLocalizedProperty($object, $field, $localeId);
            }
            default:
                {
                return $this->createCommonProperty($object, $field);
                }
        }
    }

    /**
     * Создает экземпляр обычного свойства для указанного объекта
     * @param IObject $object объект
     * @param IField $field поле типа данных
     * @return IProperty
     */
    public function createCommonProperty(IObject $object, IField $field)
    {
        $property = $this->getPrototype(
                $this->defaultPropertyClass,
                ['umi\orm\object\property\IProperty']
            )
            ->createInstance([$object, $field]);

        return $property;
    }

    /**
     * Создает экземпляр локализованного свойства для указанного объекта
     * @param IObject $object объект
     * @param ILocalizableField $field поле типа данных
     * @param string $localeId идентификатор локали для свойства
     * @return ILocalizedProperty
     */
    protected function createLocalizedProperty(IObject $object, ILocalizableField $field, $localeId)
    {
        $property = $this->getPrototype(
                $this->defaultLocalizedPropertyClass,
                ['umi\orm\object\property\ILocalizedProperty']
            )
            ->createInstance([$object, $field, $localeId]);

        return $property;
    }

    /**
     * Создает экземпляр вычисляемого свойства для указанного объекта
     * @param IObject $object объект
     * @param ICalculableField $field поле типа данных
     * @return ICalculableProperty
     */
    protected function createCalculableProperty(IObject $object, ICalculableField $field)
    {
        $property = $this->getPrototype(
                $this->defaultCalculablePropertyClass,
                ['umi\orm\object\property\ICalculableProperty']
            )
            ->createInstance([$object, $field]);

        return $property;
    }

    /**
     * Создает экземпляр обычного свойства для указанного объекта
     * @param IObject $object объект
     * @param CounterField $field поле типа данных
     * @return ICounterProperty
     */
    public function createCounterProperty(IObject $object, CounterField $field)
    {
        $property = $this->getPrototype(
                $this->defaultCounterPropertyClass,
                ['umi\orm\object\property\ICounterProperty']
            )
            ->createInstance([$object, $field]);

        return $property;
    }

}
