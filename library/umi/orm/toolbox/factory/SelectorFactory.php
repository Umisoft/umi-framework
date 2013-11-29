<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\toolbox\factory;

use umi\orm\collection\ICollection;
use umi\orm\metadata\field\IField;
use umi\orm\metadata\field\relation\ManyToManyRelationField;
use umi\orm\object\IObject;
use umi\orm\objectset\IObjectSet;
use umi\orm\objectset\IObjectSetFactory;
use umi\orm\selector\condition\IFieldConditionGroup;
use umi\orm\selector\ISelector;
use umi\orm\selector\ISelectorFactory;
use umi\toolkit\factory\IFactory;
use umi\toolkit\factory\TFactory;

/**
 * Фабрика для селекторов.
 */
class SelectorFactory implements ISelectorFactory, IFactory
{

    use TFactory;

    /**
     * @var string $selectorClass имя класса селектора
     */
    public $selectorClass = 'umi\orm\selector\Selector';
    /**
     * @var string $fieldConditionClass класс выражения селектора
     */
    public $fieldConditionClass = 'umi\orm\selector\condition\FieldCondition';
    /**
     * @var string $fieldConditionsGroupClass класс группы выражений селектора
     */
    public $fieldConditionsGroupClass = 'umi\orm\selector\condition\FieldConditionGroup';

    /**
     * @var IObjectSetFactory $objectSetFactory фабрика наборов объектов
     */
    protected $objectSetFactory;

    /**
     * Конструктор
     * @param IObjectSetFactory $objectSetFactory фабрика наборов объектов
     */
    public function __construct(IObjectSetFactory $objectSetFactory)
    {
        $this->objectSetFactory = $objectSetFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createSelector(ICollection $objectsCollection)
    {
        $objectSet = $this->objectSetFactory->createObjectSet();

        return $this->getSelectorInstance($objectsCollection, $objectSet);
    }

    /**
     * {@inheritdoc}
     */
    public function createEmptySelector(ICollection $objectsCollection)
    {
        $emptyObjectSet = $this->objectSetFactory->createEmptyObjectSet();

        return $this->getSelectorInstance($objectsCollection, $emptyObjectSet);
    }

    /**
     * Создает селектор для связи ManyToMany
     * @param IObject $object
     * @param ManyToManyRelationField $manyToManyRelationField
     * @param ICollection $targetCollection
     * @return ISelector
     */
    public function createManyToManySelector(
        IObject $object,
        ManyToManyRelationField $manyToManyRelationField,
        ICollection $targetCollection
    )
    {
        $manyToManyObjectSet = $this->objectSetFactory->createManyToManyObjectSet($object, $manyToManyRelationField);

        return $this->getSelectorInstance($targetCollection, $manyToManyObjectSet);
    }

    /**
     * {@inheritdoc}
     */
    public function createFieldConditionGroup(
        $mode = IFieldConditionGroup::MODE_AND,
        IFieldConditionGroup $parentGroup = null
    )
    {
        return $this->getPrototype(
            $this->fieldConditionsGroupClass,
            ['umi\orm\selector\condition\IFieldConditionGroup']
        )
        ->createInstance([$mode, $parentGroup]);
    }

    /**
     * {@inheritdoc}
     */
    public function createFieldCondition(
        ISelector $selector,
        IField $field,
        $collectionAlias,
        $placeholder,
        $localeId = null
    )
    {
        return $this->getPrototype(
            $this->fieldConditionClass,
            ['umi\orm\selector\condition\IFieldCondition']
        )
        ->createInstance([$selector, $field, $collectionAlias, $placeholder, $localeId]);
    }

    /**
     * Возвращает селектор для указанной коллекции и набора объектов
     * @param ICollection $objectsCollection
     * @param IObjectSet $objectSet
     * @return ISelector
     */
    protected function getSelectorInstance(ICollection $objectsCollection, IObjectSet $objectSet)
    {
        /**
         * @var ISelector $selector
         */
        $selector = $this->getPrototype(
            $this->selectorClass,
            ['umi\orm\selector\ISelector']
        )
        ->createInstance([$objectsCollection, $objectSet, $this]);
        $objectSet->setSelector($selector);

        return $selector;
    }
}
