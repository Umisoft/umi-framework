<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\selector;

use umi\orm\collection\ICollection;
use umi\orm\metadata\field\IField;
use umi\orm\metadata\field\relation\ManyToManyRelationField;
use umi\orm\object\IObject;
use umi\orm\selector\condition\IFieldCondition;
use umi\orm\selector\condition\IFieldConditionGroup;

/**
 * Фабрика для селекторов.
 */
interface ISelectorFactory
{
    /**
     * Создает экземпляр селектора
     * @param ICollection $objectsCollection коллекция объектов, для которой создается селектор
     * @return ISelector
     */
    public function createSelector(ICollection $objectsCollection);

    /**
     * Создает экземпляр селектора с пустым результатом выборки.
     * @param ICollection $objectsCollection коллекция объектов, для которой создается селектор
     * @return ISelector
     */
    public function createEmptySelector(ICollection $objectsCollection);

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
    );

    /**
     * Создает группу выражений селектора
     * @param string $mode режим сложения составных выражений
     * @param null|IFieldConditionGroup $parentGroup родительская группа выражений
     * @return IFieldConditionGroup
     */
    public function createFieldConditionGroup(
        $mode = IFieldConditionGroup::MODE_AND,
        IFieldConditionGroup $parentGroup = null
    );

    /**
     * Создает выражение селектора
     * @param ISelector $selector селектор, к которому относится выражение
     * @param IField $field поле типа данных
     * @param string $collectionAlias алиас для коллекции
     * @param string $placeholder уникальный плейсхолдер для поля
     * @param string $localeId идентификатор локали (для локализованных полей)
     * @return IFieldCondition
     */
    public function createFieldCondition(
        ISelector $selector,
        IField $field,
        $collectionAlias,
        $placeholder,
        $localeId = null
    );
}
