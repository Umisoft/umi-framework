<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\persister;

use SplObjectStorage;
use umi\dbal\driver\IDbDriver;
use umi\i18n\ILocalesAware;
use umi\i18n\ILocalizable;
use umi\i18n\TLocalesAware;
use umi\i18n\TLocalizable;
use umi\orm\exception\NotAllowedOperationException;
use umi\orm\exception\RuntimeException;
use umi\orm\manager\IObjectManagerAware;
use umi\orm\manager\TObjectManagerAware;
use umi\orm\metadata\field\relation\BelongsToRelationField;
use umi\orm\object\IObject;
use umi\orm\object\property\ICalculableProperty;
use umi\validation\IValidationAware;
use umi\validation\TValidationAware;

/**
 * Синхронизатор объектов бизнес-транзакций с базой данных (Unit Of Work).
 */
class ObjectPersister implements IObjectPersister, ILocalizable, IValidationAware, ILocalesAware, IObjectManagerAware
{

    use TLocalizable;
    use TLocalesAware;
    use TObjectManagerAware;
    use TValidationAware;

    /**
     * @var SplObjectStorage|IObject[] $newObjects список новых объектов
     */
    protected $newObjects;
    /**
     * @var SplObjectStorage|IObject[] $deletedObjects список объектов, помеченных на удаление
     */
    protected $deletedObjects;
    /**
     * @var SplObjectStorage|IObject[] $modifiedObjects список измененных объектов
     */
    protected $modifiedObjects;
    /**
     * @var SplObjectStorage|IObject[] $relatedObjects список зависимостей объектов
     */
    protected $relatedObjects;

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->newObjects = new SplObjectStorage();
        $this->deletedObjects = new SplObjectStorage();
        $this->modifiedObjects = new SplObjectStorage();
        $this->relatedObjects = new SplObjectStorage();
    }

    /**
     * {@inheritdoc}
     */
    public function getIsPersisted()
    {
        return !(count($this->modifiedObjects) || count($this->newObjects) || count($this->deletedObjects));
    }

    /**
     * {@inheritdoc}
     */
    public function executeTransaction(callable $transaction, array $affectedDrivers = [])
    {
        if (!$this->getIsPersisted()) {
            throw new NotAllowedOperationException($this->translate(
                'Cannot execute transaction. Not all objects are persisted.'
            ));
        }
        $this->startTransaction($affectedDrivers);

        try {
            call_user_func($transaction);
        } catch (\Exception $e) {
            $this->rollback($affectedDrivers);

            throw new RuntimeException($this->translate(
                'Cannot execute transaction.'
            ), 0, $e);
        }

        $this->commitTransaction($affectedDrivers);
    }

    /**
     * {@inheritdoc}
     */
    public function getInvalidObjects()
    {
        $result = [];
        foreach ($this->modifiedObjects as $object) {
            if (!$this->validateObject($object)) {
                $result[] = $object;
            }
        }
        foreach ($this->newObjects as $object) {
            if (!$this->validateObject($object)) {
                $result[] = $object;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function markAsNew(IObject $object)
    {
        $this->newObjects->attach($object);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function markAsDeleted(IObject $object)
    {
        $this->deletedObjects->attach($object);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function markAsModified(IObject $object)
    {
        if (!$object->getIsNew()) {
            $this->modifiedObjects->attach($object);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function storeNewBelongsToRelation(
        BelongsToRelationField $belongsToRelation,
        IObject $object,
        IObject $relatedObject
    )
    {
        $data = [];
        try {
            $data = $this->relatedObjects->offsetGet($object);
        } catch (\UnexpectedValueException $e) {
        }

        $data[$belongsToRelation->getName()] = $relatedObject;
        $this->relatedObjects->offsetSet($object, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function clearObjectsState()
    {
        $this->newObjects->removeAll($this->newObjects);
        $this->relatedObjects->removeAll($this->relatedObjects);
        $this->modifiedObjects->removeAll($this->modifiedObjects);
        $this->deletedObjects->removeAll($this->deletedObjects);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function clearObjectState(IObject $object)
    {
        $this->newObjects->detach($object);
        $this->modifiedObjects->detach($object);
        $this->deletedObjects->detach($object);
        $this->relatedObjects->detach($object);
    }

    /**
     * {@inheritdoc}
     */
    public function validateObject(IObject $object)
    {

        $object->clearValidationErrors();
        $result = true;

        foreach ($object->getAllProperties() as $property) {
            if (null != ($validators = $property->getField()
                    ->getValidators())
            ) {
                $validator = $this->createValidatorCollection($validators);
                if (!$validator->isValid($property->getValue())) {
                    $object->addValidationError($property->getName(), $validator->getMessages());
                    $result = false;
                }
            }
            $validatorMethod = IObject::VALIDATOR_METHOD_PREFIX . $property->getName();
            if (method_exists($object, $validatorMethod)) {
                if ($object->{$validatorMethod}() === false) {
                    $result = false;
                }
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {

        if (!$this->getIsPersisted()) {
            $drivers = $this->detectUsedDrivers();
            $this->startTransaction($drivers);

            try {
                $this->persist();
            } catch (\Exception $e) {
                $this->rollback($drivers);
                throw new RuntimeException($this->translate(
                    'Cannot persist objects.'
                ), 0, $e);
            }

            $this->commitTransaction($drivers);
        }

        return $this;
    }

    /**
     * Откатывает начатые транзакции и изменения объектов
     * @param IDbDriver[] $drivers
     * @return $this
     */
    protected function rollback(array $drivers)
    {
        foreach ($drivers as $driver) {
            $driver->rollbackTransaction();
        }
        $this->unloadStorageObjects($this->newObjects);
        $this->unloadStorageObjects($this->modifiedObjects);
        $this->unloadStorageObjects($this->deletedObjects);
        $this->clearObjectsState();

        return $this;
    }

    /**
     * Страртует транзакцию для указанных драйверов бд
     * @param IDbDriver[] $drivers
     * @return $this
     */
    protected function startTransaction(array $drivers)
    {
        foreach ($drivers as $driver) {
            $driver->startTransaction();
        }

        return $this;
    }

    /**
     * Фиксирует все начатые транзакции для указанных драйверов бд
     * @param IDbDriver[] $drivers
     * @return $this
     */
    protected function commitTransaction(array $drivers)
    {
        foreach ($drivers as $driver) {
            $driver->commitTransaction();
        }

        return $this;
    }

    /**
     * Определяет используемые для редактирования объектов драйверы бд
     * @return IDbDriver[]
     */
    protected function detectUsedDrivers()
    {
        $drivers = [];

        foreach ($this->newObjects as $object) {
            $source = $object->getCollection()
                ->getMetadata()
                ->getCollectionDataSource();
            $drivers[$source->getMasterServerId()] = $source->getMasterServer()
                ->getDbDriver();
        }
        foreach ($this->deletedObjects as $object) {
            $source = $object->getCollection()
                ->getMetadata()
                ->getCollectionDataSource();
            $drivers[$source->getMasterServerId()] = $source->getMasterServer()
                ->getDbDriver();
        }
        foreach ($this->modifiedObjects as $object) {
            $source = $object->getCollection()
                ->getMetadata()
                ->getCollectionDataSource();
            $drivers[$source->getMasterServerId()] = $source->getMasterServer()
                ->getDbDriver();
        }

        return $drivers;

    }

    /**
     * Сохраняет объекты в БД
     * @throws RuntimeException если при сохранении возникли ошибки
     * @return $this
     */
    protected function persist()
    {
        /**
         * @var IObject $object
         */
        foreach ($this->newObjects as $object) {
            $object->getCollection()
                ->persistNewObject($object);

            /**
             * @var ICalculableProperty[] $calculableProperties
             */
            $calculableProperties = [];
            foreach ($object->getAllProperties() as $property) {
                if (!$property->getIsModified() && $property instanceof ICalculableProperty) {
                    $calculableProperties[] = $property;
                }
            }

            $this->getObjectManager()
                ->storeNewObject($object);

            foreach ($calculableProperties as $property) {
                $property->recalculate();
            }
        }

        foreach ($this->relatedObjects as $object) {
            $this->restoreObjectRelations($object, $this->relatedObjects->getInfo());
        }

        foreach ($this->modifiedObjects as $object) {
            $object->getCollection()
                ->persistModifiedObject($object);
        }

        foreach ($this->deletedObjects as $object) {
            $object->getCollection()
                ->persistDeletedObject($object);
        }

        foreach ($this->modifiedObjects as $object) {
            $object->setIsConsistent();
        }

        $this->unloadStorageObjects($this->deletedObjects);
        $this->clearObjectsState();
    }

    /**
     * Восстанавливает значения полей типа relation
     * @param IObject $object
     * @param array $relatedValues
     */
    protected function restoreObjectRelations(IObject $object, array $relatedValues)
    {
        foreach ($relatedValues as $propertyName => $value) {
            $property = $object->getProperty($propertyName);
            $property->setInitialValue(null);
            $property->setValue($value);
        }
    }

    /**
     * Выгружает все объекты в указанном хранилище
     * @param SplObjectStorage $storage storage из IObject
     */
    protected function unloadStorageObjects(SplObjectStorage $storage)
    {
        $storage->rewind();
        while ($storage->valid()) {
            $object = $storage->current();
            $object->unload();
            $storage->detach($object);
        }
    }

}
