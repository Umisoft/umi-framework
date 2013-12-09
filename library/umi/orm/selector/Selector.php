<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\selector;

use umi\dbal\builder\IExpressionGroup;
use umi\dbal\builder\ISelectBuilder;
use umi\i18n\ILocalesAware;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalesAware;
use umi\i18n\TLocalizable;
use umi\orm\collection\ICollection;
use umi\orm\collection\ICollectionManagerAware;
use umi\orm\collection\TCollectionManagerAware;
use umi\orm\exception\InvalidArgumentException;
use umi\orm\exception\NonexistentEntityException;
use umi\orm\metadata\field\IField;
use umi\orm\metadata\field\ILocalizableField;
use umi\orm\metadata\field\IRelationField;
use umi\orm\metadata\field\relation\BelongsToRelationField;
use umi\orm\metadata\field\relation\HasManyRelationField;
use umi\orm\metadata\field\relation\HasOneRelationField;
use umi\orm\metadata\field\relation\ManyToManyRelationField;
use umi\orm\metadata\IMetadata;
use umi\orm\metadata\IMetadataManagerAware;
use umi\orm\metadata\IObjectType;
use umi\orm\metadata\TMetadataManagerAware;
use umi\orm\object\property\ILocalizedProperty;
use umi\orm\objectset\IObjectSet;
use umi\orm\selector\condition\IFieldConditionGroup;

/**
 * Инструмент для формирования выборок объектов из коллекции,
 * а так же связанных объектов.
 */
class Selector implements ISelector, ILocalizable, ILocalesAware, ICollectionManagerAware, IMetadataManagerAware
{

    use TLocalizable;
    use TLocalesAware;
    use TCollectionManagerAware;
    use TMetadataManagerAware;

    /**
     * @var ICollection $collection коллекция объектов, из которой делаем выборку
     */
    protected $collection;
    /**
     * @var IObjectType[] $types массив из выбираемых типов типа [$typeName => $type, ...]
     */
    protected $types = [];
    /**
     * @var array $fields массив из выбираемых полей в виде array($fieldPath => [IField, $fieldSourceAlias), ...]
     */
    protected $fields = [];
    /**
     * @var IField[] $usedFields массив полей, которые участвуют в выборке в виде array($fieldName => IField, ...)
     * Предназначен для определения подходящих типов.
     */
    protected $usedFields = [];
    /**
     * @var IField[] $forcedFields массив полей, которые выбираются принудительно
     */
    protected $forcedFields = [];
    /**
     * @var array $withFields массив из выбираемых связанных полей в виде [$relationFieldAlias => [ICollection, IField[] $selectiveFields]]
     */
    protected $withFields = [];
    /**
     * @var array $resolvedFieldChains список разобранных цепочек используемых в запросе полей в виде [$fieldPath => [[IField, $fieldSourceAlias, IObjectsCollection], ...], ...]
     */
    protected $resolvedFieldChains = [];
    /**
     * @var array $orderConditions список ORDER BY - условий
     */
    protected $orderConditions = [];
    /**
     * @var IObjectSet $objectSet выбранные объекты
     */
    protected $objectSet;
    /**
     * @var int $limit ограничение на количество затрагиваемых строк
     */
    protected $limit;
    /**
     * @var int $offset смещение выборки
     */
    protected $offset = 0;
    /**
     * @var bool $withLocalization режим загрузки  всех локализованных свойств объектов
     */
    protected $withLocalization = false;
    /**
     * @var ISelectorFactory $selectorFactory
     */
    protected $selectorFactory;
    /**
     * @var IMetadata $metadata метаданные коллекции объектов
     */
    private $metadata;
    /**
     * @var IFieldConditionGroup $fieldConditionsGroup корневая группа условий выборки по значениям полей
     */
    private $fieldConditionsGroup;
    /**
     * @var IFieldConditionGroup $currentFieldConditionsGroup текущая группа условий выборки по значениям полей
     */
    private $currentFieldConditionsGroup;
    /**
     * @var int $placeholderCounter счетчик для плейсхолдеров
     */
    private $placeholderCounter = 0;
    /**
     * @var ISelectBuilder $selectBuilder билдер низкоуровневых select-запросов к БД
     */
    private $selectBuilder;

    /**
     * Конструктор
     * @param ICollection $collection коллекция объектов, из которой осуществляется выборка
     * @param IObjectSet $objectSet набор объектов с которым связан селектор
     * @param ISelectorFactory $selectorFactory фабрика селекторов
     */
    public function __construct(ICollection $collection, IObjectSet $objectSet, ISelectorFactory $selectorFactory)
    {
        $this->collection = $collection;
        $this->metadata = $collection->getMetadata();
        $this->objectSet = $objectSet;

        $this->selectorFactory = $selectorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function resetResult()
    {
        $this->selectBuilder = null;
        $this->objectSet->reset();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function types(array $typeNames)
    {
        foreach ($typeNames as $typeName) {
            $includeDescendants = false;
            if (substr($typeName, -1) === self::ASTERISK) {
                $includeDescendants = true;
                $typeName = rtrim($typeName, self::ASTERISK . IObjectType::PATH_SEPARATOR);
                if (!strlen($typeName)) {
                    $typeName = IObjectType::BASE;
                }
            }
            if (!$this->metadata->getTypeExists($typeName)) {
                throw new NonexistentEntityException($this->translate(
                    'Cannot select objects. Object type "{name}" does not exist.',
                    ["name" => $typeName]
                ));
            }
            $this->types[$typeName] = $this->metadata->getType($typeName);

            if ($includeDescendants) {
                $this->types($this->metadata->getDescendantTypesList($typeName));
            }

        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withLocalization($withLocalization = true)
    {
        $this->withLocalization = $withLocalization;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function fields(array $fieldNames = [])
    {
        foreach ($fieldNames as $fieldName) {
            if ($this->metadata->getFieldExists($fieldName)) {
                $field = $this->metadata->getField($fieldName);
                $this->fields[$fieldName] = $field;
            } else {
                throw new NonexistentEntityException($this->translate(
                    'Cannot select objects. Field "{name}" does not exist.',
                    ["name" => $fieldName]
                ));
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function with($relationFieldPath, array $fieldNames = [])
    {
        $fieldChain = $this->resolveFieldChain($relationFieldPath);
        array_pop($fieldChain);

        $relationField = null;
        $relationFieldAlias = null;

        /**
         * @var IRelationField $firstField первое в цепочке поле связи
         */
        list($firstField) = current($fieldChain);
        $this->forcedFields[$firstField->getName()] = $firstField;

        foreach ($fieldChain as $fieldInfo) {
            /**
             * @var IRelationField $relationField поле связи
             */
            list($relationField, $relationFieldAlias) = $fieldInfo;
            if (!$relationField instanceof BelongsToRelationField) {
                throw new InvalidArgumentException($this->translate(
                    'Cannot select with related object. Cannot resolve field path "{path}". Field "{name}" is not "belongs-to" relation.',
                    ["path" => $relationFieldPath, "name" => $relationField->getName()]
                ));
            }
        }

        $selectiveFields = [];
        $collection = $this->getCollectionManager()
            ->getCollection($relationField->getTargetCollectionName());
        $relatedMetadata = $collection->getMetadata();
        foreach ($fieldNames as $fieldName) {
            if ($relatedMetadata->getFieldExists($fieldName)) {
                $field = $relatedMetadata->getField($fieldName);
                $selectiveFields[$fieldName] = $field;
            } else {
                throw new NonexistentEntityException($this->translate(
                    'Cannot select with related object. Field "{name}" does not exist in collection "{collection}".',
                    ['name' => $fieldName, 'collection' => $collection->getName()]
                ));
            }
        }

        $this->withFields[$relationFieldAlias . self::ALIAS_SEPARATOR . $relationField->getName()] = [
            $collection,
            $selectiveFields
        ];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function begin($mode = IFieldConditionGroup::MODE_AND)
    {
        $parentGroup = $this->currentFieldConditionsGroup ? : $this->getFieldConditionsGroup();
        $group = $this->selectorFactory->createFieldConditionGroup($mode, $parentGroup);
        if ($parentGroup) {
            $parentGroup->addGroup($group);
        }

        $this->currentFieldConditionsGroup = $group;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function end()
    {
        if ($this->currentFieldConditionsGroup) {
            $parentGroup = $this->currentFieldConditionsGroup->getParentGroup();
            if ($parentGroup !== $this->getFieldConditionsGroup()) {
                $this->currentFieldConditionsGroup = $parentGroup;
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function where($fieldPath, $localeId = null)
    {
        $fieldsChain = $this->resolveFieldChain($fieldPath);
        /**
         * @var IField $conditionField поле по которому формируется условие
         */
        list($conditionField, $fieldSourceAlias) = end($fieldsChain);

        if (!$this->currentFieldConditionsGroup) {
            $this->begin();
        }

        if ($localeId && (!$conditionField instanceof ILocalizableField || !$conditionField->hasLocale($localeId))) {
            throw new NonexistentEntityException($this->translate(
                'Cannot set condition. Locale "{localeId}" for field "{path}" does not exist.',
                ["localeId" => $localeId, "path" => $fieldPath]
            ));
        } elseif (!$localeId) {
            $localeId = $this->getCurrentLocale();
        }

        $fieldCondition = $this->selectorFactory->createFieldCondition(
            $this,
            $conditionField,
            $fieldSourceAlias,
            self::PLACEHOLDER_PREFIX . $this->placeholderCounter++,
            $localeId
        );

        return $this->currentFieldConditionsGroup->addCondition($fieldCondition);
    }

    /**
     * {@inheritdoc}
     */
    public function orderBy($fieldPath, $direction = self::ORDER_ASC)
    {
        $fieldsChain = $this->resolveFieldChain($fieldPath);
        /**
         * @var IField $conditionField поле по которому формируется условие
         */
        list($conditionField, $fieldSourceAlias) = end($fieldsChain);
        $this->orderConditions[$fieldPath] = array($conditionField, $fieldSourceAlias, $direction);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function limit($limit, $offset = null)
    {
        $this->limit = $limit;
        if ($offset) {
            $this->offset = $offset;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectBuilder()
    {
        if (!$this->selectBuilder) {
            $this->selectBuilder = $this->collection->getMetadata()
                ->getCollectionDataSource()
                ->select();

            $selectiveFields = $this->resolveSelectiveFields($this->collection, $this->fields, $this->types);
            $selectiveFields = array_merge($selectiveFields, $this->forcedFields);
            $columns = $this->getSelectedColumns($selectiveFields, $this->collection->getSourceAlias());
            $columns = array_merge($columns, $this->getSelectedWithColumns());
            $this->selectBuilder->setColumns($columns);

            $this->applyFrom($this->selectBuilder);
            $this->applyJoins($this->selectBuilder);

            $this->selectBuilder->where(IExpressionGroup::MODE_AND);

            $this->applyTypeConditions($this->selectBuilder);

            if ($this->fieldConditionsGroup) {
                $this->fieldConditionsGroup->applyConditions($this->selectBuilder);
            }

            $this->applyOrderConditions($this->selectBuilder);
            $this->applyLimitConditions($this->selectBuilder);

        }

        return $this->selectBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function result()
    {
        return $this->objectSet;
    }

    /**
     * {@inheritdoc}
     */
    public function getResult()
    {
        return $this->objectSet;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal()
    {
        return $this->getSelectBuilder()
            ->getTotal();
    }

    /**
     * Возвращает итератор (IObjectSet)
     * @internal
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return IObjectSet
     */
    public function getIterator()
    {
        return $this->objectSet;
    }

    /**
     * Возвращает группу ограничений по значениям полей.
     * @return IFieldConditionGroup
     */
    protected function getFieldConditionsGroup()
    {
        if (!$this->fieldConditionsGroup) {
            $this->fieldConditionsGroup = $this->selectorFactory->createFieldConditionGroup(
                IFieldConditionGroup::MODE_AND
            );
        }

        return $this->fieldConditionsGroup;
    }

    /**
     * Применяет условия выборки по типам
     * @param ISelectBuilder $selectBuilder
     */
    protected function applyTypeConditions(ISelectBuilder $selectBuilder)
    {
        $types = $this->getSelectionTypes();

        if (count($types)) {
            $typeField = $this->collection->getObjectTypeField();
            $typeFieldColumn = $this->collection->getSourceAlias(
                ) . ISelector::FIELD_SEPARATOR . $typeField->getColumnName();
            $typeConditionPlaceholder = ':type' . self::PLACEHOLDER_SEPARATOR . $this->collection->getName();

            $typeConditions = [];
            foreach ($types as $typeName) {
                $typeConditions[] = $this->collection->getName() . IObjectType::PATH_SEPARATOR . $typeName;
            }

            $selectBuilder->expr($typeFieldColumn, 'IN', $typeConditionPlaceholder);
            $selectBuilder->bindArray($typeConditionPlaceholder, $typeConditions);
        }
    }

    /**
     * Разрешает и возвращает цепочку связанных полей по указанному пути
     * @param string $fieldPath путь (Ex: order.owner.name)
     * @throws InvalidArgumentException
     * @return array массив вида [[IField $field, string $collectionAlias, ICollection $fieldCollection], ...]
     */
    protected function resolveFieldChain($fieldPath)
    {
        if (isset($this->resolvedFieldChains[$fieldPath])) {
            return $this->resolvedFieldChains[$fieldPath];
        }

        $result = [];
        $pathInfo = explode(self::FIELD_SEPARATOR, $fieldPath);

        $latestCollection = $this->collection;
        $fieldSourceAlias = $this->collection->getSourceAlias();

        for ($i = 0; $i < count($pathInfo); $i++) {
            $fieldName = $pathInfo[$i];

            $metadata = $latestCollection->getMetadata();
            if (!$metadata->getFieldExists($fieldName)) {
                throw new InvalidArgumentException ($this->translate(
                    'Cannot resolve field path "{path}". Field "{name}" does not exist in "{metadata}".',
                    ["path" => $fieldPath, "name" => $fieldName, "metadata" => $metadata->getCollectionName()]
                ));
            }

            $field = $metadata->getField($fieldName);
            $result[] = [$field, $fieldSourceAlias, $latestCollection];

            if ($i == 0) {
                $this->usedFields[$field->getName()] = $field;
            }

            if ($field instanceof IRelationField) {
                $latestCollection = $this->getCollectionManager()
                    ->getCollection($field->getTargetCollectionName());
                $fieldSourceAlias .= self::ALIAS_SEPARATOR . $fieldName;

                if (!isset($pathInfo[$i + 1])) {
                    $latestCollectionIdField = $latestCollection->getIdentifyField();
                    $result[] = [$latestCollectionIdField, $fieldSourceAlias, $latestCollection];
                }
            } elseif (isset($pathInfo[$i + 1])) {
                throw new InvalidArgumentException($this->translate(
                    'Cannot resolve field path "{path}". Field "{name}" is not relation.',
                    ["path" => $fieldPath, "name" => $field->getName()]
                ));
            }
        }

        return $this->resolvedFieldChains[$fieldPath] = $result;
    }

    /**
     * Возвращает имена типов, которые должны присутствовать в выборке
     * @throws InvalidArgumentException если заданы невозможные условия выборки
     * @return array
     */
    protected function getSelectionTypes()
    {
        if (!count($this->usedFields)) {
            return array_keys($this->types);
        }
        $fields = array_keys($this->usedFields);

        if (!count($this->types)) {
            $types = $this->metadata->getTypesByFields($fields);
            if (!count($types)) {
                throw new InvalidArgumentException($this->translate(
                    'The selection is not possible. Conditions do not match metadata types.'
                ));
            }
            if (!count(array_diff($this->metadata->getTypesList(), $types))) {
                $types = [];
            }

            return $types;
        }

        $types = array_keys($this->types);
        $typesCount = count($types);
        if (count(array_intersect($this->metadata->getTypesByFields($fields), $types)) != $typesCount) {
            throw new InvalidArgumentException($this->translate(
                'The selection is not possible. Conditions do not match metadata types.'
            ));
        }

        return $types;
    }

    /**
     * Применяет выборку из таблицы коллекции
     * @param ISelectBuilder $selectBuilder
     */
    protected function applyFrom(ISelectBuilder $selectBuilder)
    {
        $tableName = $this->collection->getMetadata()
            ->getCollectionDataSource()
            ->getSourceName();
        $alias = $this->collection->getSourceAlias();
        $selectBuilder->from(array($tableName, $alias));
    }

    /**
     * Применяет JOIN условия для доступа к связанным полям
     * @param ISelectBuilder $selectBuilder
     */
    protected function applyJoins(ISelectBuilder $selectBuilder)
    {
        foreach ($this->resolvedFieldChains as $chain) {
            $chainLength = count($chain);
            for ($i = 0; $i < $chainLength - 1; $i++) {
                list ($relationField, $relationSourceAlias, $relationCollection) = $chain[$i];
                if (!$relationField instanceof IRelationField) {
                    continue;
                }

                $targetCollection = $chain[$i + 1][2];

                switch (true) {
                    case ($relationField instanceof HasOneRelationField):
                    {
                        /**
                         * @var HasOneRelationField $relationField
                         */
                        $this->applyJoinForHasManyRelation(
                            $selectBuilder,
                            $relationField,
                            $relationSourceAlias,
                            $relationCollection,
                            $targetCollection
                        );
                    }
                        break;
                    case ($relationField instanceof HasManyRelationField):
                    {
                        /**
                         * @var HasManyRelationField $relationField
                         */
                        $this->applyGroupByCollectionPk($selectBuilder);
                        $this->applyJoinForHasManyRelation(
                            $selectBuilder,
                            $relationField,
                            $relationSourceAlias,
                            $relationCollection,
                            $targetCollection
                        );
                    }
                        break;
                    case ($relationField instanceof BelongsToRelationField):
                    {
                        /**
                         * @var BelongsToRelationField $relationField
                         */
                        $this->applyJoinForBelongsToRelation(
                            $selectBuilder,
                            $relationField,
                            $relationSourceAlias,
                            $targetCollection
                        );
                    }
                        break;
                    case ($relationField instanceof ManyToManyRelationField):
                    {
                        $this->applyGroupByCollectionPk($selectBuilder);
                        /**
                         * @var ManyToManyRelationField $relationField
                         */
                        $this->applyJoinForManyToManyRelation(
                            $selectBuilder,
                            $relationField,
                            $relationSourceAlias,
                            $relationCollection,
                            $targetCollection
                        );
                    }
                        break;
                }
            }
        }
    }

    /**
     * Применяет JOIN для HAS_ONE и HAS_MANY
     * @param ISelectBuilder $selectBuilder
     * @param HasManyRelationField $relationField поле по которому осуществляется связь
     * @param string $relationSourceAlias алиас таблицы для $relationField
     * @param ICollection $relationCollection коллекция поля ПО которому осуществляется связь
     * @param ICollection $targetCollection коллекция НА которую осуществляется связь
     * @return $this
     */
    protected function applyJoinForHasManyRelation(
        ISelectBuilder $selectBuilder,
        HasManyRelationField $relationField,
        $relationSourceAlias,
        ICollection $relationCollection,
        ICollection $targetCollection
    )
    {

        $targetTableName = $targetCollection->getMetadata()
            ->getCollectionDataSource()
            ->getSourceName();
        $targetTableAlias = $relationSourceAlias . self::ALIAS_SEPARATOR . $relationField->getName();

        $targetFieldName = $relationField->getTargetFieldName();
        $targetField = $targetCollection->getMetadata()
            ->getField($targetFieldName);

        $relationPk = $relationCollection->getIdentifyField();

        $leftColumn = $targetTableAlias . self::FIELD_SEPARATOR . $targetField->getColumnName();
        $rightColumn = $relationSourceAlias . self::FIELD_SEPARATOR . $relationPk->getColumnName();

        $selectBuilder
            ->leftJoin(array($targetTableName, $targetTableAlias))
            ->on($leftColumn, '=', $rightColumn);

        return $this;
    }

    /**
     * Применяет JOIN для BELONGS_TO
     * @param ISelectBuilder $selectBuilder
     * @param BelongsToRelationField $relationField поле по которому осуществляется связь
     * @param string $relationSourceAlias алиас таблицы для $relationField
     * @param ICollection $targetCollection коллекция НА которую осуществляется связь
     * @return $this
     */
    protected function applyJoinForBelongsToRelation(
        ISelectBuilder $selectBuilder,
        BelongsToRelationField $relationField,
        $relationSourceAlias,
        ICollection $targetCollection
    )
    {

        $targetTableName = $targetCollection->getMetadata()
            ->getCollectionDataSource()
            ->getSourceName();
        $targetTableAlias = $relationSourceAlias . self::ALIAS_SEPARATOR . $relationField->getName();

        $targetPk = $targetCollection->getIdentifyField();

        $leftColumn = $targetTableAlias . self::FIELD_SEPARATOR . $targetPk->getColumnName();
        $rightColumn = $relationSourceAlias . self::FIELD_SEPARATOR . $relationField->getColumnName();

        $selectBuilder
            ->leftJoin(array($targetTableName, $targetTableAlias))
            ->on($leftColumn, '=', $rightColumn);

        return $this;
    }

    /**
     * Применяет JOIN'ы для MANY_TO_MANY через bridge-коллекцию
     * @param ISelectBuilder $selectBuilder
     * @param ManyToManyRelationField $relationField поле по которому осуществляется связь
     * @param string $relationSourceAlias алиас таблицы для $relationField
     * @param ICollection $relationCollection коллекция поля ПО которому осуществляется связь
     * @param ICollection $targetCollection коллекция НА которую осуществляется связь
     * @return $this
     */
    protected function applyJoinForManyToManyRelation(
        ISelectBuilder $selectBuilder,
        ManyToManyRelationField $relationField,
        $relationSourceAlias,
        ICollection $relationCollection,
        ICollection $targetCollection
    )
    {

        $targetTableName = $targetCollection->getMetadata()
            ->getCollectionDataSource()
            ->getSourceName();
        $targetTableAlias = $relationSourceAlias . self::ALIAS_SEPARATOR . $relationField->getName();
        $targetPk = $targetCollection->getIdentifyField();

        $relationPk = $relationCollection->getIdentifyField();

        $metadata = $this->getMetadataManager()
            ->getMetadata($relationField->getBridgeCollectionName());
        $bridgeTableName = $metadata->getCollectionDataSource()
            ->getSourceName();
        $bridgeTableAlias = $targetTableAlias . self::BRIDGE_ALIAS_POSTFIX;

        $bridgeRelationFieldName = $relationField->getRelatedFieldName();
        $bridgeRelationField = $metadata->getField($bridgeRelationFieldName);

        $bridgeTargetFieldName = $relationField->getTargetFieldName();
        $bridgeTargetField = $metadata->getField($bridgeTargetFieldName);

        // JOIN bridge collection
        $leftColumn = $bridgeTableAlias . self::FIELD_SEPARATOR . $bridgeRelationField->getColumnName();
        $rightColumn = $relationSourceAlias . self::FIELD_SEPARATOR . $relationPk->getColumnName();

        $selectBuilder
            ->leftJoin(array($bridgeTableName, $bridgeTableAlias))
            ->on($leftColumn, '=', $rightColumn);

        // JOIN target collection
        $leftColumn = $targetTableAlias . self::FIELD_SEPARATOR . $targetPk->getColumnName();
        $rightColumn = $bridgeTableAlias . self::FIELD_SEPARATOR . $bridgeTargetField->getColumnName();

        $selectBuilder
            ->leftJoin(array($targetTableName, $targetTableAlias))
            ->on($leftColumn, '=', $rightColumn);

        return $this;
    }

    /**
     * Применяет группировку по первичному ключу коллекции
     * @param ISelectBuilder $selectBuilder
     * @return $this
     */
    protected function applyGroupByCollectionPk(ISelectBuilder $selectBuilder)
    {
        $sourceAlias = $this->collection->getSourceAlias();
        $column = $sourceAlias . self::FIELD_SEPARATOR . $this->collection->getIdentifyField()
                ->getColumnName();
        $selectBuilder->groupBy($column);

        return $this;
    }

    /**
     * Возвращает имя и alias колонок для выборки полей
     * @param array $fields список выбираемых полей
     * @param string $fieldSourceAlias alias таблицы коллекции
     * @return array
     */
    protected function getSelectedColumns(array $fields, $fieldSourceAlias)
    {
        $columns = [];

        foreach ($fields as $fieldName => $field) {
            if ($field instanceof ILocalizableField && $field->getIsLocalized()) {
                $currentLocaleId = $this->getCurrentLocale();
                $defaultLocaleId = $this->getDefaultLocale();

                if ($this->withLocalization) {
                    foreach ($field->getLocalizations() as $localeId => $localeInfo) {
                        $columnName = $fieldSourceAlias . self::FIELD_SEPARATOR . $localeInfo['columnName'];
                        $alias = $fieldSourceAlias . self::ALIAS_SEPARATOR . $fieldName . ILocalizedProperty::LOCALE_SEPARATOR . $localeId;
                        $columns[] = [$columnName, $alias];
                    }
                } else {
                    $columnName = $fieldSourceAlias . self::FIELD_SEPARATOR . $field->getLocaleColumnName(
                            $currentLocaleId
                        );
                    $alias = $fieldSourceAlias . self::ALIAS_SEPARATOR . $fieldName . ILocalizedProperty::LOCALE_SEPARATOR . $currentLocaleId;
                    $columns[] = [$columnName, $alias];

                    if ($currentLocaleId !== $defaultLocaleId) {
                        $columnName = $fieldSourceAlias . self::FIELD_SEPARATOR . $field->getLocaleColumnName(
                                $defaultLocaleId
                            );
                        $alias = $fieldSourceAlias . self::ALIAS_SEPARATOR . $fieldName . ILocalizedProperty::LOCALE_SEPARATOR . $defaultLocaleId;
                        $columns[] = [$columnName, $alias];
                    }
                }

            } else {
                $columnName = $fieldSourceAlias . self::FIELD_SEPARATOR . $field->getColumnName();
                $alias = $fieldSourceAlias . self::ALIAS_SEPARATOR . $fieldName;
                $columns[] = [$columnName, $alias];
            }
        }

        return $columns;
    }

    /**
     * Возвращает имя и alias колонок для выборки связанных полей
     * @return array
     */
    protected function getSelectedWithColumns()
    {
        $result = [];
        foreach ($this->withFields as $fieldSourceAlias => $withInfo) {
            list ($collection, $fields) = $withInfo;
            $resolvedFields = $this->resolveSelectiveFields($collection, $fields, []);
            $result = array_merge($result, $this->getSelectedColumns($resolvedFields, $fieldSourceAlias));
        }

        return $result;
    }

    /**
     * Применяет ограничения на выборку
     * @param ISelectBuilder $selectBuilder
     */
    protected function applyOrderConditions(ISelectBuilder $selectBuilder)
    {
        foreach ($this->orderConditions as $collectionInfo) {
            /**
             * @var IField $field
             */
            list ($field, $fieldSourceAlias, $direction) = $collectionInfo;
            $alias = $fieldSourceAlias . self::FIELD_SEPARATOR . $field->getColumnName();
            $selectBuilder->orderBy($alias, $direction);
        }
    }

    /**
     * Применяет ограничения на выборку
     * @param ISelectBuilder $selectBuilder
     */
    protected function applyLimitConditions(ISelectBuilder $selectBuilder)
    {
        if ($this->limit) {
            $postfix = self::PLACEHOLDER_SEPARATOR . $this->collection->getName();

            $limitPlaceholder = ':limit' . $postfix;
            $offsetPlaceholder = ':offset' . $postfix;

            $selectBuilder
                ->limit($limitPlaceholder)
                ->bindInt($limitPlaceholder, $this->limit);
            if ($this->offset) {
                $selectBuilder
                    ->offset($offsetPlaceholder)
                    ->bindInt($offsetPlaceholder, $this->offset);
            }
        }
    }

    /**
     * Разрешает и возвращает список выбираемых полей.
     * @param ICollection $collection коллекция
     * @param IField[] $fields уточняющий список полей
     * @param IObjectType[] $types уточняющий список типов
     * @return IField[]
     */
    protected function resolveSelectiveFields(ICollection $collection, array $fields, array $types)
    {
        if (!count($fields)) {
            foreach ($types as $type) {
                $fields = array_merge($fields, $type->getFields());
            }

            if (!count($fields)) {
                $fields = $collection->getMetadata()
                    ->getFields();
            }
        }

        foreach ($fields as $fieldName => $field) {
            if ($field instanceof IRelationField && !$field instanceof BelongsToRelationField) {
                unset($fields[$fieldName]);
            }
        }

        return array_merge($collection->getForcedFieldsToLoad(), $fields);
    }
}
