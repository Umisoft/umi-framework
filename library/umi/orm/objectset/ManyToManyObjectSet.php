<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\objectset;

use umi\orm\collection\ICollectionManagerAware;
use umi\orm\collection\IHierarchicCollection;
use umi\orm\collection\ILinkedHierarchicCollection;
use umi\orm\collection\ISimpleCollection;
use umi\orm\collection\TCollectionManagerAware;
use umi\orm\exception\AlreadyExistentEntityException;
use umi\orm\exception\InvalidArgumentException;
use umi\orm\metadata\field\relation\ManyToManyRelationField;
use umi\orm\object\IObject;

/**
 * Набор объектов для свойства типа relation с типом связи manyToMany.
 */
class ManyToManyObjectSet extends ObjectSet implements IManyToManyObjectSet, ICollectionManagerAware
{

    use TCollectionManagerAware;

    /**
     * @var IObject $object объект
     */
    protected $object;
    /**
     * @var ManyToManyRelationField $field поле
     */
    protected $field;

    /**
     * Конструктор.
     * @param IObject $object объект данных
     * @param ManyToManyRelationField $field поле типа данных объекта
     */
    public function __construct(IObject $object, ManyToManyRelationField $field)
    {
        parent::__construct();

        $this->object = $object;
        $this->field = $field;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        if (null != ($object = current($this->iteratorArray))) {
            $linkedData = $this->set->offsetGet($object);
            if (is_array($linkedData) && $linkedData['deleted']) {
                $this->next();

                return $this->valid();
            }

            return true;
        }
        if ($this->isCompletelyLoaded) {
            return false;
        }
        if (null != ($row = $this->getQueryResultRow())) {

            $object = $this->loadObjects($row);

            if (!$this->attachObjectToStorage($object)) {
                $this->next();

                return $this->valid();
            }

            return true;
        }
        $this->isCompletelyLoaded = true;

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function attach(IObject $object)
    {
        if ($this->contains($object)) {
            throw new AlreadyExistentEntityException($this->translate(
                'Cannot attach object to ManyToManyObjectSet. This object is already attached.'
            ));
        }

        $linkObject = $this->getBridgeCollection()
            ->add()
            ->setValue($this->field->getRelatedFieldName(), $this->object)
            ->setValue($this->field->getTargetFieldName(), $object);

        $this->attachObjectToStorage(
            $object,
            array(
                'linkObject' => $linkObject,
                'deleted'    => false
            )
        );

        return $linkObject;
    }

    /**
     * {@inheritdoc}
     */
    public function link(IObject $object)
    {
        $linkObject = $this->getLinkObject($object);
        if ($linkObject instanceof IObject) {
            return $linkObject;
        }

        return $this->attach($object);
    }

    /**
     * {@inheritdoc}
     */
    public function contains(IObject $object)
    {
        return $this->getLinkObject($object) instanceof IObject;
    }

    /**
     * {@inheritdoc}
     */
    public function detach(IObject $object)
    {
        $linkObject = $this->getLinkObject($object);
        if ($linkObject instanceof IObject) {
            $linkObject->getCollection()
                ->delete($linkObject);
            $this->attachObjectToStorage(
                $object,
                array(
                    'linkObject' => $linkObject,
                    'deleted'    => true
                )
            );
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function detachAll()
    {
        $linkObjects = $this->getBridgeCollection()
            ->select()
            ->where($this->field->getRelatedFieldName())
            ->equals($this->object)
            ->result();

        foreach ($linkObjects as $linkObject) {
            $this->getBridgeCollection()
                ->delete($linkObject);
        }

        $this->set->removeAll($this->set);
        $this->iteratorArray = [];
        $this->isCompletelyLoaded = true;
    }

    /**
     * Возвращает объект содержащий в себе свойства связи (объект bridge-коллекции) либо null, если такого нет
     * @param IObject $object связанный объект (объект target-коллекции)
     * @throws InvalidArgumentException если передан объект неподходящей коллекции
     * @return IObject|null
     */
    protected function getLinkObject(IObject $object)
    {
        $collectionName = $this->field->getTargetCollectionName();
        $collection = $object->getCollection();

        if (($collectionName != $collection->getName()) &&
            ($collection instanceof ILinkedHierarchicCollection && $collection->getCommonHierarchy()
                    ->getName())
        ) {

            throw new InvalidArgumentException($this->translate(
                'Cannot get linked object. IObject from wrong collection is given.'
            ));
        }

        if ($this->set->contains($object) && ($data = $this->set->offsetGet($object))) {
            return $data['linkObject'];
        }

        if (($this->isCompletelyLoaded && !$this->set->contains($object)) || $object->getIsNew()) {
            return null;
        }

        $linkObject = $this->getBridgeCollection()
            ->select()
            ->where($this->field->getRelatedFieldName())
            ->equals($this->object)
            ->where($this->field->getTargetFieldName())
            ->equals($object)
            ->limit(1)
            ->result()
            ->fetch();

        if (!$linkObject instanceof IObject) {
            return null;
        }

        $this->attachObjectToStorage(
            $object,
            array(
                'linkObject' => $linkObject,
                'deleted'    => false
            )
        );

        return $linkObject;
    }

    /**
     * Возвращает связующую коллекцию
     * @return ISimpleCollection|IHierarchicCollection
     */
    protected function getBridgeCollection()
    {
        return $this->getCollectionManager()
            ->getCollection($this->field->getBridgeCollectionName());
    }

    /**
     * Добавляет объект со связанными данными
     * @param IObject $object
     * @param mixed $linkedData
     * @return bool false, если объект существовал в хранилище
     */
    protected function attachObjectToStorage(IObject $object, $linkedData = null)
    {
        $result = false;
        if (!$this->set->contains($object)) {
            $result = true;
            $this->iteratorArray[] = $object;
            $this->set->attach($object, $linkedData);
        } elseif (!is_null($linkedData)) {
            $this->set->attach($object, $linkedData);
        }

        return $result;
    }
}
