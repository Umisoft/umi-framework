<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace umi\orm\metadata\field\relation;

use umi\orm\collection\ICollection;
use umi\orm\collection\ICollectionManagerAware;
use umi\orm\collection\TCollectionManagerAware;
use umi\orm\exception\NotAllowedOperationException;
use umi\orm\metadata\field\BaseField;
use umi\orm\metadata\field\IRelationField;
use umi\orm\metadata\field\TRelationField;
use umi\orm\object\IObject;

/**
 * Класс поля связи "многие-ко-многим".
 */
class ManyToManyRelationField extends BaseField implements IRelationField, ICollectionManagerAware
{

    use TRelationField;
    use TCollectionManagerAware;

    /**
     * {@inheritdoc}
     */
    public function getTargetCollectionName()
    {
        return $this->targetCollectionName;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetCollection()
    {
        return $this->getCollectionManager()
            ->getCollection($this->targetCollectionName);
    }

    /**
     * Возвращает имя поля для связи с target-коллекцией
     * @return string
     */
    public function getTargetFieldName()
    {
        return $this->targetFieldName;
    }

    /**
     * Возвращает имя коллекции, которая является мостом для связи c target-коллекцией
     * @return string
     */
    public function getBridgeCollectionName()
    {
        return $this->bridgeCollectionName;
    }

    /**
     * Возвращает имя коллекции, которая является мостом для связи c target-коллекцией
     * @return ICollection
     */
    public function getBridgeCollection()
    {
        return $this->getCollectionManager()->getCollection($this->bridgeCollectionName);
    }

    /**
     * Возвращает имя связанного поля в bridge-коллекции
     * @return string
     */
    public function getRelatedFieldName()
    {
        return $this->relatedFieldName;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataType()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyConfiguration(array $config)
    {
        $this->applyTargetCollectionConfig($config);
        $this->applyTargetFieldConfig($config);
        $this->applyRelatedFieldConfig($config);
        $this->applyBridgeCollectionConfig($config);
    }

    /**
     * {@inheritdoc}
     */
    public function validateInputPropertyValue($propertyValue)
    {
        throw new NotAllowedOperationException($this->translate(
            'Cannot set value for property "{name}". Value should be appended to IManyToManyObjectSet.',
            ['name' => $this->getName()]
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function preparePropertyValue(IObject $object, $internalDbValue)
    {
        $targetCollection = $this->getCollectionManager()
            ->getCollection($this->targetCollectionName);
        $mirrorField = $targetCollection->getMetadata()
            ->getFieldByRelation($this->getTargetFieldName(), $this->getBridgeCollectionName());
        $selector = $targetCollection->getManyToManySelector($object, $this);
        $selector->where($mirrorField->getName())
            ->equals($object);

        return $selector->result();
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function prepareDbValue(IObject $object, $propertyValue)
    {
        return null;
    }
}
