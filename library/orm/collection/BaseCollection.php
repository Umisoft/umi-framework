<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\collection;

use umi\i18n\ILocalesAware;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalesAware;
use umi\i18n\TLocalizable;
use umi\orm\exception\IException;
use umi\orm\exception\InvalidArgumentException;
use umi\orm\exception\LoadEntityException;
use umi\orm\exception\NonexistentEntityException;
use umi\orm\exception\NotAllowedOperationException;
use umi\orm\exception\RuntimeException;
use umi\orm\manager\IObjectManagerAware;
use umi\orm\manager\TObjectManagerAware;
use umi\orm\metadata\field\IField;
use umi\orm\metadata\field\ILocalizableField;
use umi\orm\metadata\field\relation\ManyToManyRelationField;
use umi\orm\metadata\IMetadata;
use umi\orm\metadata\IObjectType;
use umi\orm\object\IObject;
use umi\orm\object\property\calculable\ICalculableProperty;
use umi\orm\object\property\IProperty;
use umi\orm\persister\IObjectPersisterAware;
use umi\orm\persister\TObjectPersisterAware;
use umi\orm\selector\ISelector;
use umi\orm\selector\ISelectorFactory;

/**
 * Базовый класс коллекции объектов.
 */
abstract class BaseCollection
    implements ICollection, ILocalizable, ILocalesAware, IObjectManagerAware, IObjectPersisterAware
{

    use TLocalizable;
    use TObjectManagerAware;
    use TObjectPersisterAware;
    use TLocalesAware;

    /**
     * @var string $name имя коллекции
     */
    protected $name;
    /**
     * @var IMetadata $metadata метаданные коллекции
     */
    protected $metadata;
    /**
     * @var ISelectorFactory $selectorFactory
     */
    protected $selectorFactory;

    /**
     * Конструктор
     * @param string $collectionName имя коллекции
     * @param IMetadata $metadata метаданные коллекции
     * @param ISelectorFactory $selectorFactory фабрика селекторов
     */
    public function __construct($collectionName, IMetadata $metadata, ISelectorFactory $selectorFactory)
    {
        $this->name = $collectionName;
        $this->metadata = $metadata;
        $this->selectorFactory = $selectorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function contains(IObject $object)
    {
        return $object->getCollection() === $this;
    }

    /**
     * Возвращает иерархический объект по уникальному GUID
     * @param integer|string $guid GUID объекта
     * @param bool $withLocalization загружать ли значения локализованных свойств объекта.
     * По умолчанию выключено.
     * @throws IException если не удалось получить объект
     * @return IObject
     */
    public function get($guid, $withLocalization = false)
    {
        if (!$this->getGUIDField()
            ->checkGUIDFormat($guid)
        ) {
            throw new InvalidArgumentException($this->translate(
                'Cannot get object by GUID "{guid}". Wrong GUID format.',
                ['guid' => $guid]
            ));
        }
        if (!$object = $this->getObjectManager()
            ->getObjectInstanceByGuid($guid)
        ) {
            $objectsSet = $this->select()
                ->where(
                    $this->getGUIDField()
                        ->getName()
                )
                ->equals(strtolower($guid))
                ->withLocalization($withLocalization)
                ->result();

            //closing cursor explicitly for SQLite
            $all = $objectsSet->fetchAll();
            $result = array_shift($all);

            if (!$object = $result) {
                throw new RuntimeException($this->translate(
                    'Cannot get object with GUID "{guid}" from collection "{collection}".',
                    ['guid' => $guid, 'collection' => $this->getName()]
                ));
            }
        }

        return $object;
    }

    /**
     * Возвращает объект по уникальному идентификатору в БД.
     * Используется ORM для внутренних целей, запрещено использовать в высокоуровневом коде.
     * @internal
     * @param integer|string $objectId
     * @param bool $withLocalization загружать ли значения локализованных свойств объекта.
     * По умолчанию выключено.
     * @throws IException если не удалось получить объект
     * @return IObject
     */
    public function getById($objectId, $withLocalization = false)
    {
        if (!$object = $this->getObjectManager()
            ->getObjectInstanceById($this, $objectId)
        ) {
            $objectsSet = $this->select()
                ->where(
                    $this->getIdentifyField()
                        ->getName()
                )
                ->equals($objectId)
                ->withLocalization($withLocalization)
                ->result();

            //closing cursor explicitly for SQLite
            $all = $objectsSet->fetchAll();
            $result = array_shift($all);
            if (!$object = $result) {
                throw new RuntimeException($this->translate(
                    'Cannot get object with id "{id}" from collection "{collection}".',
                    ['id' => $objectId, 'collection' => $this->getName()]
                ));
            }
        }

        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function loadObject(IObjectType $objectType, array $objectInfo)
    {
        $identify = $this->getIdentifyField()
            ->getName();
        $guid = $this->getGUIDField()
            ->getName();
        if (!isset($objectInfo[$identify])) {
            throw new LoadEntityException($this->translate(
                'Cannot load object. Identify field value is not found.'
            ));
        }
        if (!isset($objectInfo[$guid])) {
            throw new LoadEntityException($this->translate(
                'Cannot load object. GUID value is not found.'
            ));
        }

        $object = $this->getObjectManager()
            ->registerLoadedObject($this, $objectType, $objectInfo[$identify], $objectInfo[$guid]);
        $object->setInitialValues($objectInfo);

        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function fullyLoadObject(IObject $object, $withLocalization = false)
    {
        if (!$object->getId()) {
            throw new LoadEntityException($this->translate(
                'Cannot load object. Object id required.'
            ));
        }

        $fieldsToLoad = [];
        $loadedValues = $object->getInitialValues();

        foreach ($object->getType()
            ->getFields() as $fieldName => $field) {
            if ((!$withLocalization && !isset($loadedValues[$fieldName]))
                || ($withLocalization && $field instanceof ILocalizableField && $field->getIsLocalized())
            ) {
                $fieldsToLoad[] = $fieldName;
            }
        }

        if (count($fieldsToLoad)) {
            $pkFiledName = $this->getIdentifyField()
                ->getName();

            $objectsSet = $this->select()
                ->fields($fieldsToLoad)
                ->withLocalization($withLocalization)
                ->where($pkFiledName)
                ->equals($object->getId())
                ->result();

            if (!$objectsSet->fetch()) {
                throw new LoadEntityException($this->translate(
                    'Cannot load object with id "{id}" from collection "{collection}".',
                    ['id' => $object->getId(), 'collection' => $this->getName()]
                ));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function select()
    {
        return $this->selectorFactory->createSelector($this);
    }

    /**
     * {@inheritdoc}
     */
    public function emptySelect()
    {
        return $this->selectorFactory->createEmptySelector($this);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(IObject $object)
    {
        if (!$this->contains($object)) {
            throw new NotAllowedOperationException($this->translate(
                'Cannot delete object. Object from another collection given.'
            ));
        }
        $this->getObjectPersister()
            ->markAsDeleted($object);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getManyToManySelector(IObject $object, ManyToManyRelationField $manyToManyRelationField)
    {
        return $this->selectorFactory->createManyToManySelector($object, $manyToManyRelationField, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function getForcedFieldsToLoad()
    {
        return [
            IObject::FIELD_IDENTIFY => $this->getRequiredField(IObject::FIELD_IDENTIFY),
            IObject::FIELD_GUID     => $this->getRequiredField(IObject::FIELD_GUID),
            IObject::FIELD_TYPE     => $this->getRequiredField(IObject::FIELD_TYPE),
            IObject::FIELD_VERSION  => $this->getRequiredField(IObject::FIELD_VERSION)
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifyField()
    {
        return $this->getRequiredField(IObject::FIELD_IDENTIFY);
    }

    /**
     * {@inheritdoc}
     */
    public function getGUIDField()
    {
        return $this->getRequiredField(IObject::FIELD_GUID);
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectTypeField()
    {
        return $this->getRequiredField(IObject::FIELD_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function getVersionField()
    {
        return $this->getRequiredField(IObject::FIELD_VERSION);
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceAlias()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldAlias($fieldName)
    {
        return $this->name . ISelector::ALIAS_SEPARATOR . $fieldName;
    }

    /**
     * Запускает запросы на добавление в БД нового объекта коллекции.
     * @internal
     * @param IObject $object
     * @throws RuntimeException
     * @return mixed
     */
    public function persistNewObject(IObject $object)
    {
        if (!$this->contains($object)) {
            throw new NotAllowedOperationException($this->translate(
                'Cannot persist new object. Object from another collection given.'
            ));
        }

        $dataSource = $this->getMetadata()
            ->getCollectionDataSource();
        $identifyColumnName = $this->getIdentifyField()
            ->getColumnName();

        $insertBuilder = $dataSource->insert();

        // set object id
        if ($object->getId()) {
            $insertBuilder
                ->set($identifyColumnName)
                ->bindValue(
                    ':' . $identifyColumnName,
                    $object->getId(),
                    $this
                        ->getIdentifyField()
                        ->getDataType()
                );
        }

        // set type
        $typeName = $object->getTypePath();
        $objectTypeField = $this->getObjectTypeField();
        $columnName = $objectTypeField->getColumnName();
        $insertBuilder->set($columnName);
        $insertBuilder->bindValue(':' . $columnName, $typeName, $objectTypeField->getDataType());

        foreach ($object->getModifiedProperties() as $property) {
            if ($this->getMetadata()
                ->getFieldExists($property->getName())
            ) {
                $field = $this->getMetadata()
                    ->getField($property->getName());
                $field->persistProperty($object, $property, $insertBuilder);
            }
        }

        $insertBuilder->execute();
        
        if (!$object->getId()) {
            $objectId = $insertBuilder->getConnection()->lastInsertId();
            if (!$objectId) {
                throw new RuntimeException($this->translate(
                    'Cannot persist object. Cannot get last inserted id for object.'
                ));
            }
            $object->getProperty(IObject::FIELD_IDENTIFY)
                ->setInitialValue($objectId);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function persistModifiedObject(IObject $object)
    {
        if (!$this->contains($object)) {
            throw new NotAllowedOperationException($this->translate(
                'Cannot persist modified object. Object from another collection given.'
            ));
        }

        $modifiedProperties = $object->getModifiedProperties();
        if (!count($modifiedProperties)) {
            return;
        }

        $dataSource = $this->getMetadata()
            ->getCollectionDataSource();
        $identifyColumnName = $this->getIdentifyField()
            ->getColumnName();

        $updateBuilder = $dataSource->update();

        $calculableProperties = [];
        foreach ($object->getModifiedProperties() as $property) {
            if ($this->getMetadata()
                ->getFieldExists($property->getName())
            ) {
                if ($property instanceof ICalculableProperty) {
                    $calculableProperties[] = $property;
                } else {
                    $field = $this->getMetadata()
                        ->getField($property->getName());
                    $field->persistProperty($object, $property, $updateBuilder);
                }
            }
        }

        if ($updateBuilder->getUpdatePossible()) {

            $versionProperty = $object->getProperty(IObject::FIELD_VERSION);
            $version = (int) ($versionProperty->getPreviousDbValue() ? : $versionProperty->getDbValue());
            $newVersion = $version + 1;
            $versionProperty->setValue($newVersion);

            $versionColumnName = $this->getVersionField()
                ->getColumnName();

            $this->getVersionField()
                ->persistProperty($object, $versionProperty, $updateBuilder);

            $updateBuilder->where()
                ->expr($identifyColumnName, '=', ':objectId');
            $updateBuilder->bindValue(
                ':objectId',
                $object->getId(),
                $this->getIdentifyField()
                    ->getDataType()
            );

            $updateBuilder->where()
                ->expr($versionColumnName, '=', ':' . $versionColumnName);
            $updateBuilder->bindValue(
                ':' . $versionColumnName,
                $version,
                $this->getVersionField()
                    ->getDataType()
            );

            $result = $updateBuilder->execute();

            if ($result->rowCount() != 1) {

                $selectBuilder = $dataSource->select($versionColumnName);
                $selectBuilder->where()
                    ->expr($identifyColumnName, '=', ':objectId');
                $selectBuilder->bindValue(
                    ':objectId',
                    $object->getId(),
                    $this->getIdentifyField()
                        ->getDataType()
                );

                $selectResult = $selectBuilder->execute();
                $selectResultRow = $selectResult->fetch();

                if (is_array($selectResultRow) && $selectResultRow[$versionColumnName] != $version) {
                    throw new RuntimeException($this->translate(
                        'Cannot modify object with id "{id}" and type "{type}". Object is out of date.',
                        ['id' => $object->getId(), 'type' => $object->getTypePath()]
                    ));
                }

                throw new RuntimeException($this->translate(
                    'Cannot modify object with id "{id}" and type "{type}". Database row is not modified.',
                    ['id' => $object->getId(), 'type' => $object->getTypePath()]
                ));
            }
        }

        $this->persistCalculableProperties($object, $calculableProperties);
    }

    /**
     * {@inheritdoc}
     */
    public function persistDeletedObject(IObject $object)
    {
        if (!$this->contains($object)) {
            throw new NotAllowedOperationException($this->translate(
                'Cannot persist deleted object. Object from another collection given.'
            ));
        }
        $dataSource = $this->getMetadata()
            ->getCollectionDataSource();

        $deleteBuilder = $dataSource->delete();

        $deleteBuilder
            ->where()
            ->expr(
                $this
                    ->getIdentifyField()
                    ->getColumnName(),
                '=',
                ':objectId'
            );
        $deleteBuilder->bindValue(
            ':objectId',
            $object->getId(),
            $this->getIdentifyField()
                ->getDataType()
        );

        $result = $deleteBuilder->execute();
        if ($result->rowCount() != 1) {
            throw new RuntimeException($this->translate(
                'Cannot delete object with id "{id}" and type "{type}". Database row is not modified.',
                ['id' => $object->getId(), 'type' => $object->getTypePath()]
            ));
        }
    }

    /**
     * Запускает запросы на сохранение вычисляемых свойств объекта.
     * @param IObject $object объект, для которого сохраняются значения
     * @param IProperty[] $calculableProperties список свойств, которые должны быть вычислены
     * @throws RuntimeException если не удалось сохранить свойства
     */
    protected function persistCalculableProperties(IObject $object, array $calculableProperties)
    {

        $updateBuilder = $this->getMetadata()
            ->getCollectionDataSource()
            ->update();

        $updateBuilder
            ->where()
            ->expr(
                $this
                    ->getIdentifyField()
                    ->getColumnName(),
                '=',
                ':objectId'
            );
        $updateBuilder->bindValue(
            ':objectId',
            $object->getId(),
            $this->getIdentifyField()
                ->getDataType()
        );

        foreach ($calculableProperties as $property) {
            if ($this->getMetadata()
                ->getFieldExists($property->getName())
            ) {
                $field = $this->getMetadata()
                    ->getField($property->getName());
                $field->persistProperty($object, $property, $updateBuilder);
            }
        }

        if (!$updateBuilder->getUpdatePossible()) {
            return;
        }
        $result = $updateBuilder->execute();

        if ($result->rowCount() != 1) {
            throw new RuntimeException($this->translate(
                'Cannot set calculable properties for object with id "{id}" and type "{type}".'
                . ' Database row is not modified.',
                ['id' => $object->getId(), 'type' => $object->getTypePath()]
            ));
        }
    }

    /**
     * Возвращает обязательное поле коллекции.
     * @param $fieldName
     * @throws NonexistentEntityException если поле не существует
     * @return IField
     */
    protected function getRequiredField($fieldName)
    {
        if (!$this->metadata->getFieldExists($fieldName)) {
            throw new NonexistentEntityException($this->translate(
                'Collection "{collection}" does not contain required field "{name}".',
                ['collection' => $this->name, 'name' => $fieldName]
            ));
        }

        return $this->metadata->getField($fieldName);
    }
}
